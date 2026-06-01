<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Store;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'receivable'); // default to receivable (Piutang Pelanggan)
        $receiptType = $type === 'payable' ? 'pembelian' : 'penjualan';

        // Get stores that have payment history OR finished debts (lunas) of the requested type
        $query = Store::where(function($q) use ($receiptType) {
            $q->whereHas('debts', function($q2) use ($receiptType) {
                $q2->where('status', 'lunas')
                   ->whereHas('receipt', function($q3) use ($receiptType) {
                       $q3->where('type', $receiptType);
                   });
            })->orWhereHas('debts', function($q2) use ($receiptType) {
                $q2->has('payments')
                   ->whereHas('receipt', function($q3) use ($receiptType) {
                       $q3->where('type', $receiptType);
                   });
            });
        });

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $storesWithPayments = $query->with(['debts' => function($query) use ($receiptType) {
            $query->whereHas('receipt', function($q) use ($receiptType) {
                      $q->where('type', $receiptType);
                  })
                  ->orderBy('created_at', 'desc');
        }, 'debts.payments'])->paginate(10)->withQueryString();

        return view('payments.index', compact('storesWithPayments', 'search', 'type'));
    }

    public function showStore(Request $request, $storeId)
    {
        $type = $request->input('type', 'receivable');
        $receiptType = $type === 'payable' ? 'pembelian' : 'penjualan';

        $store = Store::with(['debts' => function($query) use ($receiptType) {
            $query->where('status', 'lunas')
                  ->whereHas('receipt', function($q) use ($receiptType) {
                      $q->where('type', $receiptType);
                  })
                  ->orderBy('created_at', 'desc');
        }, 'debts.receipt', 'debts.payments' => function($query) {
            $query->orderBy('payment_date', 'desc');
        }])->findOrFail($storeId);

        return view('payments.show', compact('store', 'type'));
    }
}
