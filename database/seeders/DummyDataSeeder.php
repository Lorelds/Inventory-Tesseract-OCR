<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Debt;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Stores
        $store1 = Store::create([
            'name' => 'Toko Bangunan Maju Jaya',
            'phone' => '081234567890',
            'address' => 'Jl. Raya Kenjeran No. 123, Surabaya'
        ]);

        $store2 = Store::create([
            'name' => 'Sinar Terang Elektrik',
            'phone' => '089876543210',
            'address' => 'Jl. Pahlawan 45, Sidoarjo'
        ]);

        $store3 = Store::create([
            'name' => 'CV Bumi Indah',
            'phone' => '08561239874',
            'address' => 'Kawasan Industri Rungkut, Surabaya'
        ]);

        // 2. Create Products
        $p1 = Product::create([
            'name' => 'Semen Gresik 40kg',
            'sku' => 'PRD-SG40',
            'category' => 'Bahan Bangunan',
            'buy_price' => 45000,
            'sell_price' => 52000,
            'stock' => 0
        ]);

        $p2 = Product::create([
            'name' => 'Cat Tembok Dulux 5Kg',
            'sku' => 'PRD-CAT-DLX5',
            'category' => 'Cat & Aksesoris',
            'buy_price' => 120000,
            'sell_price' => 150000,
            'stock' => 0
        ]);

        $p3 = Product::create([
            'name' => 'Paku Beton 5cm',
            'sku' => 'PRD-PAKU-B5',
            'category' => 'Perkakas',
            'buy_price' => 12000,
            'sell_price' => 18000,
            'stock' => 0
        ]);

        $p4 = Product::create([
            'name' => 'Kabel Eterna NYM 2x1.5 (50m)',
            'sku' => 'PRD-KBL-ET2',
            'category' => 'Listrik',
            'buy_price' => 250000,
            'sell_price' => 310000,
            'stock' => 0
        ]);

        $p5 = Product::create([
            'name' => 'Lampu Philips LED 9W',
            'sku' => 'PRD-LMP-PH9',
            'category' => 'Listrik',
            'buy_price' => 25000,
            'sell_price' => 35000,
            'stock' => 0
        ]);

        // 3. Create Receipts (Pembelian / In)
        
        // Receipt 1 (Buy Semen & Paku from Maju Jaya - Lunas)
        $r1 = Receipt::create([
            'store_id' => $store1->id,
            'type' => 'pembelian',
            'transaction_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'total_amount' => 45000 * 50 + 12000 * 10, // 2,370,000
            'status' => 'validated',
            'payment_status' => 'lunas',
            'image_path' => 'dummy/receipt1.jpg'
        ]);

        $this->addReceiptItemAndStock($r1, $p1, 50, 45000);
        $this->addReceiptItemAndStock($r1, $p3, 10, 12000);

        // Receipt 2 (Buy Elektrik from Sinar Terang - Hutang)
        $r2 = Receipt::create([
            'store_id' => $store2->id,
            'type' => 'pembelian',
            'transaction_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'total_amount' => 250000 * 5 + 25000 * 20, // 1,250,000 + 500,000 = 1,750,000
            'status' => 'validated',
            'payment_status' => 'hutang',
            'image_path' => 'dummy/receipt2.jpg'
        ]);

        $this->addReceiptItemAndStock($r2, $p4, 5, 250000);
        $this->addReceiptItemAndStock($r2, $p5, 20, 25000);

        // Add Debt for Receipt 2
        Debt::create([
            'receipt_id' => $r2->id,
            'store_id' => $store2->id,
            'amount' => 1750000,
            'paid_amount' => 500000,
            'status' => 'partial'
        ]);

        // Receipt 3 (Buy Cat from CV Bumi Indah - Lunas)
        $r3 = Receipt::create([
            'store_id' => $store3->id,
            'type' => 'pembelian',
            'transaction_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'total_amount' => 120000 * 10, // 1,200,000
            'status' => 'validated',
            'payment_status' => 'lunas',
            'image_path' => 'dummy/receipt3.jpg'
        ]);

        $this->addReceiptItemAndStock($r3, $p2, 10, 120000);

        // 4. Create Receipts (Penjualan / Out)

        // Receipt 4 (Sell Semen & Cat - Lunas)
        $r4 = Receipt::create([
            'store_id' => $store1->id,
            'store_name' => 'Pelanggan Umum (Cash)',
            'type' => 'penjualan',
            'transaction_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'total_amount' => 52000 * 5 + 150000 * 2, // 260,000 + 300,000 = 560,000
            'status' => 'validated',
            'payment_status' => 'lunas',
            'image_path' => 'dummy/receipt4.jpg'
        ]);

        $this->addReceiptItemAndStock($r4, $p1, 5, 52000);
        $this->addReceiptItemAndStock($r4, $p2, 2, 150000);

        // Receipt 5 (Sell Listrik - Lunas)
        $r5 = Receipt::create([
            'store_id' => $store2->id,
            'store_name' => 'Proyek Perumahan A',
            'type' => 'penjualan',
            'transaction_date' => Carbon::now()->format('Y-m-d'),
            'total_amount' => 310000 * 2 + 35000 * 5, // 620,000 + 175,000 = 795,000
            'status' => 'validated',
            'payment_status' => 'lunas',
            'image_path' => 'dummy/receipt5.jpg'
        ]);

        $this->addReceiptItemAndStock($r5, $p4, 2, 310000);
        $this->addReceiptItemAndStock($r5, $p5, 5, 35000);
    }

    private function addReceiptItemAndStock($receipt, $product, $qty, $price)
    {
        ReceiptItem::create([
            'receipt_id' => $receipt->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $qty,
            'unit_price' => $price,
            'subtotal' => $qty * $price
        ]);

        if ($receipt->type == 'pembelian') {
            $product->stock += $qty;
            StockMovement::create([
                'product_id' => $product->id,
                'receipt_id' => $receipt->id,
                'type' => 'in',
                'quantity' => $qty,
                'balance' => $product->stock,
                'notes' => 'Pembelian via Nota #' . str_pad($receipt->id, 5, '0', STR_PAD_LEFT)
            ]);
        } else {
            $product->stock -= $qty;
            StockMovement::create([
                'product_id' => $product->id,
                'receipt_id' => $receipt->id,
                'type' => 'out',
                'quantity' => $qty,
                'balance' => $product->stock,
                'notes' => 'Penjualan via Nota #' . str_pad($receipt->id, 5, '0', STR_PAD_LEFT)
            ]);
        }
        $product->save();
    }
}
