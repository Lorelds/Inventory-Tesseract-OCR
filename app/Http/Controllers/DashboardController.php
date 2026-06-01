<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receipt;
use App\Models\Product;
use App\Models\Debt;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Sales (Paid Receipts)
        $total_sales = Receipt::where('status', 'validated')
            ->where('payment_status', 'lunas')
            ->sum('total_amount');

        // Total Debt (Unpaid)
        $total_debt = Debt::where('status', 'hutang')->sum('amount') - Debt::where('status', 'hutang')->sum('paid_amount');

        // Total Products
        $total_products = Product::count();

        // Total Receipts Validated
        $total_receipts = Receipt::where('status', 'validated')->count();

        // Recent Receipts
        $recent_receipts = Receipt::with('store')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Chart Data: Last 6 Months Sales
        $chart_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $sales = Receipt::where('status', 'validated')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('total_amount');
                
            $chart_data[] = [
                'month' => $month->format('M Y'),
                'sales' => $sales
            ];
        }

        return view('dashboard', compact(
            'total_sales', 
            'total_debt', 
            'total_products', 
            'total_receipts', 
            'recent_receipts',
            'chart_data'
        ));
    }
}
