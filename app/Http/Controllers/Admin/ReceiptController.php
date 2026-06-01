<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Receipt;
use App\Models\Store;
use App\Models\Product;
use App\Models\ReceiptItem;
use App\Models\Debt;

use Intervention\Image\Facades\Image as InterventionImage;

class ReceiptController extends Controller
{
    // Show list of all receipts (History)
    public function index(Request $request)
    {
        $query = Receipt::with('store');

        // Search logic
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('store', function($qStore) use ($search) {
                      $qStore->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Sort logic
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest_amount':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'lowest_amount':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $receipts = $query->paginate(10)->withQueryString();
        
        return view('admin.receipts.index', compact('receipts'));
    }

    // Show upload form
    public function create()
    {
        $stores = Store::all();
        return view('admin.receipts.upload', compact('stores'));
    }

    // Show receipt details
    public function show($id)
    {
        $receipt = Receipt::with(['store', 'items', 'validatedBy', 'debt'])->findOrFail($id);
        return view('admin.receipts.show', compact('receipt'));
    }

    // Handle file upload and OCR processing
    public function upload(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'store_id' => 'required|exists:stores,id',
            'type' => 'required|in:pembelian,penjualan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Store the uploaded image
        $imagePath = $request->file('receipt_image')->store('receipts', 'public');

        // Get full path for Tesseract processing
        $fullImagePath = Storage::disk('public')->path($imagePath);

        // Preprocess image for better OCR
        $preprocessedPath = $this->preprocessImage($fullImagePath);

        // Initialize and run Tesseract OCR
        $tesseract = new \thiagoalessio\TesseractOCR\TesseractOCR($preprocessedPath);
        $tesseract->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
        $tesseract->tessdataDir(storage_path('app/tessdata'));
        $tesseract->lang('ind'); // Indonesian language as per user requirements
        $tesseract->allowlist(range('a', 'z'), range('A', 'Z'), range(0, 9), ['.', ',', ':', '%', '$', '/', '(', ')', '-']);
        
        // Extract text from image
        $rawText = $tesseract->run();

        // Clean up preprocessed image
        if (file_exists($preprocessedPath) && $preprocessedPath !== $fullImagePath) {
            unlink($preprocessedPath);
        }

        // Parse extracted text using regex
        $parsedData = $this->parseReceiptText($rawText);

        // Create receipt record
        $receipt = Receipt::create([
            'store_id' => $request->store_id,
            'image_path' => $imagePath,
            'raw_text' => $rawText,
            'store_name' => $parsedData['store_name'] ?? null,
            'transaction_date' => $parsedData['transaction_date'] ?? null,
            'total_amount' => $parsedData['total_amount'] ?? 0.00,
            'status' => 'pending',
            'type' => $request->type,
            'payment_status' => $request->payment_status ?? 'hutang',
        ]);

        // If payment is lunas, no debt created
        // If payment is hutang, debt will be created after validation

        // Redirect to validation page
        return redirect()->route('admin.receipts.validate', $receipt->id)
            ->with('success', 'Receipt uploaded successfully. Please validate the extracted data.');
    }

    // Show validation form
    public function validate($id)
    {
        $receipt = Receipt::with('store')->findOrFail($id);
        $stores = Store::all();
        $categories = Product::whereNotNull('category')->where('category', '!=', '')->distinct()->pluck('category');
        $parsedData = $this->parseReceiptText($receipt->raw_text);

        // If already validated, redirect to list or show message
        if ($receipt->status === 'validated') {
            return redirect()->route('admin.receipts.index')
                ->with('info', 'This receipt has already been validated.');
        }

        return view('admin.receipts.validate', compact('receipt', 'stores', 'categories', 'parsedData'));
    }

    // Handle validation and save data
    public function validateSubmit(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);

        // Validate request
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.category' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.measure' => 'nullable|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update receipt with validated data
        $receipt->update([
            'transaction_date' => $request->transaction_date,
            'total_amount' => $request->total_amount,
            'status' => 'validated',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Process receipt items and update inventory
        $this->processReceiptItems($receipt, $request->items);

        // Create debt record. If lunas, it goes directly to payments history.
        if ($receipt->payment_status === 'hutang') {
            $this->createDebtFromReceipt($receipt, false);
        } elseif ($receipt->payment_status === 'lunas') {
            $this->createDebtFromReceipt($receipt, true, $request->payment_method);
        }

        return redirect()->route('admin.receipts.index')
            ->with('success', 'Receipt validated and processed successfully.');
    }

    public function destroy(\App\Models\Receipt $receipt)
    {
        // 1. Revert Inventory Stock
        foreach ($receipt->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->stock -= $item->quantity;
                // Ensure stock doesn't go below 0 just in case
                if ($product->stock < 0) {
                    $product->stock = 0;
                }
                $product->save();
            }
        }

        // 2. Delete related Debt (if any)
        $debt = \App\Models\Debt::where('store_id', $receipt->store_id)
            ->where('amount', $receipt->total_amount)
            ->where('notes', 'like', '%#' . $receipt->id . '%')
            ->first();
            
        if ($debt) {
            // Also delete its payments
            \App\Models\DebtPayment::where('debt_id', $debt->id)->delete();
            $debt->delete();
        }

        // 3. Delete Receipt Items (if DB cascade isn't set up)
        \App\Models\ReceiptItem::where('receipt_id', $receipt->id)->delete();

        // 4. Delete the receipt image file if exists
        if ($receipt->image_path && \Illuminate\Support\Facades\Storage::exists($receipt->image_path)) {
            \Illuminate\Support\Facades\Storage::delete($receipt->image_path);
        }

        // 5. Delete Receipt
        $receipt->delete();

        return redirect()->route('admin.receipts.index')
            ->with('success', 'Receipt completely deleted. Inventory and debts reverted.');
    }

    // Preprocess image for better OCR results
    private function preprocessImage($imagePath)
    {
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        $image = $manager->read($imagePath);

        // Convert to grayscale
        $image->greyscale();

        // Increase contrast (Intervention v3 contrast takes values from -100 to 100)
        $image->contrast(10);

        // Resize if too small (OCR works better on larger images)
        // Intervention v3 uses scale() to maintain aspect ratio
        if ($image->width() < 1000) {
            $image->scale(width: 1000);
        }

        // Save preprocessed image
        $preprocessedPath = tempnam(sys_get_temp_dir(), 'receipt_') . '.png';
        $image->save($preprocessedPath);

        return $preprocessedPath;
    }

    // Parse receipt text using regex patterns
    private function parseReceiptText($text)
    {
        $data = [];
        $lines = explode("\n", $text);
        
        // Clean up empty lines
        $lines = array_values(array_filter(array_map('trim', $lines)));

        // Extract store name (Heuristic: First line that isn't just a single word or generic like "Logo")
        foreach ($lines as $line) {
            if (strlen($line) > 3 && !preg_match('/logo|details|from|to/i', $line)) {
                $data['store_name'] = $line;
                break;
            }
        }

        // Extract date (various formats)
        $datePatterns = [
            '/(?:Date|RecelptDate|Tanggal)[\s:]*([A-Za-z0-9\s,\/\-]+)/i',
            '/\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/',
            '/\d{2,4}[\/\-]\d{1,2}[\/\-]\d{1,2}/',
            '/(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[\s\-,\/]+\d{1,2}(st|nd|rd|th)?[\s\-,\/]+\d{2,4}/i',
        ];

        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $dateStr = isset($matches[1]) ? $matches[1] : $matches[0];
                try {
                    $data['transaction_date'] = date('Y-m-d', strtotime(str_replace(['st','nd','rd','th'], '', $dateStr)));
                } catch (\Exception $e) {
                    $data['transaction_date'] = date('Y-m-d');
                }
                break;
            }
        }

        // Extract total amount (Look for Total, Amount, or common OCR misreads like Tata)
        $totalPatterns = [
            '/(?:TOTAL|AMOUNT\s+(?:DUE|PAYABLE)|BALANCE|TATA|SUBTOTAL)[\s:]*[A-Z$€£]*\s*(\d+[,.]\d{2})/i',
        ];

        $totalsFound = [];
        foreach ($totalPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $match) {
                    $totalsFound[] = (float) str_replace(',', '', $match);
                }
            }
        }
        // If multiple totals found (like Subtotal and Total), take the largest one
        if (!empty($totalsFound)) {
            $data['total_amount'] = max($totalsFound);
        }

        // Extract items
        $itemLines = [];
        foreach ($lines as $line) {
            // Pattern 1: [Name] [Qty] [Price] [Total] e.g. "ItemName 4 45.00 USD180.00"
            // Pattern 2: [Name] [Qty]x[Price] e.g. "ItemName 2 x 50.00"
            if (preg_match('/^(.+?)\s+(\d+(?:\.\d+)?)\s*[x×]?\s*[A-Z$€£]*\s*(\d+[,.]\d{2})(?:\s+[A-Z$€£]*\s*\d+[,.]\d{2})?$/i', $line, $matches) ||
                preg_match('/^(.+?)(?<!\s)(\d+)\s+([A-Z$€£]*\s*\d+[,.]\d{2})\s+[A-Z$€£]*\s*(\d+[,.]\d{2})$/i', $line, $matches)) { // Handles missing space before Qty
                
                $qty = (float) $matches[2];
                $price = (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', $matches[3]));
                
                // Exclude lines that are obviously not items (like "item HRS/ATY Rate Subtotal")
                if (strtolower(trim($matches[1])) !== 'item' && $qty > 0) {
                    $itemLines[] = [
                        'name' => trim($matches[1]),
                        'quantity' => $qty,
                        'unit_price' => $price,
                    ];
                }
            } 
            // Pattern 3 (Swiss/Euro robust): [Qty]x[Name] [@] [Price] [Currency] [Total]
            // e.g., "2xLatteMacehiato84.50CHF 9.00" or "IxGloki a5.00CHF5.00"
            elseif (preg_match('/^([0-9Il]+)[xX×]\s*(.*?)(?:\s+|8|a|à|@)+(\d+[,.]\d{2})(?:\s*[A-Za-z$€£]*\s*)?(\d+[,.]\d{2})?$/ui', $line, $matches)) {
                $qty = (float) str_replace(['I', 'l'], '1', $matches[1]);
                $name = trim($matches[2]);
                $price = (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', $matches[3]));
                
                if (strtolower($name) !== 'item' && $qty > 0) {
                    $itemLines[] = [
                        'name' => $name,
                        'quantity' => $qty,
                        'unit_price' => $price,
                    ];
                }
            }
            // Pattern 4 (Indonesian Nota / Hardware Store): [Qty] [Name] [Unit Price] [Total]
            // e.g., "10 Pasir 0.25 x 6m 33.500 2.010.000"
            elseif (preg_match('/^(\d+)\s+(.+?)\s+([\d.,]{4,})\s+([\d.,]{4,})$/i', trim($line), $matches)) {
                $qty = (float) $matches[1];
                $name = trim($matches[2]);
                $price = (float) preg_replace('/[^0-9]/', '', $matches[3]);
                
                if (strtolower($name) !== 'item' && $qty > 0) {
                    $itemLines[] = [
                        'name' => $name,
                        'quantity' => $qty,
                        'unit_price' => $price,
                    ];
                }
            }
        }

        if (!empty($itemLines)) {
            $data['items'] = $itemLines;
        }

        return $data;
    }

    // Process receipt items and update inventory
    private function processReceiptItems($receipt, $items)
    {
        foreach ($items as $itemData) {
            // Find or create product
            $product = Product::firstOrCreate(
                ['name' => $itemData['name']],
                [
                    'sku' => $this->generateSKU($itemData['name']),
                    'buy_price' => $itemData['unit_price'],
                    'sell_price' => $itemData['unit_price'] * 1.5, // 50% markup as example
                    'stock' => 0,
                    'category' => $itemData['category'] ?? null,
                ]
            );

            // Update category if provided and different
            if (!empty($itemData['category']) && $product->category !== $itemData['category']) {
                $product->update(['category' => $itemData['category']]);
            }

            // Update price if it has changed
            if ($product->buy_price != $itemData['unit_price']) {
                $product->update([
                    'buy_price' => $itemData['unit_price'],
                    'sell_price' => $itemData['unit_price'] * 1.5, // maintain 50% markup rule
                ]);
            }

            $measure = isset($itemData['measure']) && $itemData['measure'] > 0 ? (float) $itemData['measure'] : 1;
            
            // Create receipt item
            ReceiptItem::create([
                'receipt_id' => $receipt->id,
                'product_id' => $product->id,
                'product_name' => $itemData['name'],
                'quantity' => $itemData['quantity'],
                'measure' => $measure,
                'unit_price' => $itemData['unit_price'],
                'subtotal' => $itemData['quantity'] * $itemData['unit_price'] * $measure,
            ]);

            // Update product stock and log movement based on receipt type
            if ($receipt->type == 'pembelian') {
                $product->increment('stock', $itemData['quantity']);
                $newBalance = $product->fresh()->stock;
                
                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'receipt_id' => $receipt->id,
                    'type' => 'in',
                    'quantity' => $itemData['quantity'],
                    'balance' => $newBalance,
                    'notes' => 'Restock from Purchase Receipt #' . str_pad($receipt->id, 5, '0', STR_PAD_LEFT),
                ]);
            } else {
                $product->decrement('stock', $itemData['quantity']);
                $newBalance = $product->fresh()->stock;
                
                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'receipt_id' => $receipt->id,
                    'type' => 'out',
                    'quantity' => $itemData['quantity'],
                    'balance' => $newBalance,
                    'notes' => 'Sale to external store via Receipt #' . str_pad($receipt->id, 5, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }

    // Create debt record from validated receipt
    private function createDebtFromReceipt($receipt, $isPaidInFull = false, $paymentMethod = 'Cash')
    {
        $status = $isPaidInFull ? 'lunas' : 'hutang';
        $paidAmount = $isPaidInFull ? $receipt->total_amount : 0;

        $debt = Debt::create([
            'receipt_id' => $receipt->id,
            'store_id' => $receipt->store_id,
            'amount' => $receipt->total_amount,
            'paid_amount' => $paidAmount,
            'status' => $status,
            'notes' => 'Created from receipt #' . $receipt->id,
        ]);

        if ($isPaidInFull && $receipt->total_amount > 0) {
            \App\Models\DebtPayment::create([
                'debt_id' => $debt->id,
                'amount_paid' => $receipt->total_amount,
                'payment_date' => $receipt->transaction_date ?? now(),
                'payment_method' => $paymentMethod ?? 'Cash',
                'notes' => 'Paid in full on receipt validation',
            ]);
        }
    }

    // Generate SKU from product name
    private function generateSKU($name)
    {
        // Take first 3 letters of each word, uppercase
        $words = preg_split('/[\s\-_]+/', $name);
        $skuParts = array_map(function($word) {
            return strtoupper(substr($word, 0, min(3, strlen($word))));
        }, $words);

        $sku = implode('', $skuParts);

        // Ensure uniqueness by adding timestamp if needed
        $baseSku = $sku;
        $counter = 1;
        while (Product::where('sku', $sku)->exists()) {
            $sku = $baseSku . $counter;
            $counter++;
        }

        return $sku;
    }
}
