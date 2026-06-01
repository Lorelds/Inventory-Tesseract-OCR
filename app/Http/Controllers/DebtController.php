<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index()
    {
        // Group debts by store. We get stores that have debts, and calculate totals.
        $storesWithDebts = Store::whereHas('debts')->with(['debts' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();

        return view('debts.index', compact('storesWithDebts'));
    }

    public function showStore($storeId)
    {
        $store = Store::with(['debts.receipt', 'debts.payments' => function($query) {
            $query->orderBy('payment_date', 'desc');
        }])->findOrFail($storeId);

        return view('debts.show', compact('store'));
    }

    public function pay(Request $request, $debtId)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $debt = Debt::findOrFail($debtId);
            
            // Calculate remaining balance (round to 2 decimals for floating point safety)
            $remainingBalance = round($debt->amount - $debt->paid_amount, 2);
            $amountPaid = round($request->amount_paid, 2);
            
            // Don't allow overpayment
            if ($amountPaid > $remainingBalance) {
                return redirect()->back()
                    ->withErrors(['amount_paid' => 'Payment amount cannot exceed the remaining balance of Rp ' . number_format($remainingBalance, 2, ',', '.')])
                    ->withInput();
            }

            // Create Payment Record
            DebtPayment::create([
                'debt_id' => $debt->id,
                'amount_paid' => $amountPaid,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            // Update Debt Balance and Status
            $debt->paid_amount += $amountPaid;
            
            if (round($debt->paid_amount, 2) >= round($debt->amount, 2)) {
                $debt->status = 'lunas';
            } else {
                $debt->status = 'partial';
            }
            
            $debt->save();

            DB::commit();

            return redirect()->back()->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }
}
