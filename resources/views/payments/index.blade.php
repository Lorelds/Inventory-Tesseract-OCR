@extends('layouts.app')
@section('page_title', 'Payment History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-1">Payment History</h2>
            <p class="text-muted mb-0">Overview of all payment history and finished debts grouped by vendor.</p>
        </div>
    </div>
    <form action="{{ route('payments.index') }}" method="GET" class="d-flex">
        <input type="hidden" name="type" value="{{ $type ?? 'receivable' }}">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="ph-bold ph-magnifying-glass text-muted"></i></span>
            <input type="text" class="form-control border-start-0 ps-0" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama toko/pelanggan..." style="min-width: 250px;">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>
</div>

<!-- Simple Toggle -->
<div class="d-flex gap-2 mb-4">
    <a href="{{ route('payments.index', ['type' => 'receivable']) }}" class="btn d-flex align-items-center gap-2 px-4 py-2 {{ ($type ?? 'receivable') === 'receivable' ? 'btn-primary shadow-sm' : 'btn-light border text-muted' }}" style="border-radius: 8px;">
        <i class="ph-fill ph-hand-coins fs-5"></i>
        <span class="fw-semibold">Piutang Lunas</span>
    </a>
    <a href="{{ route('payments.index', ['type' => 'payable']) }}" class="btn d-flex align-items-center gap-2 px-4 py-2 {{ ($type ?? 'receivable') === 'payable' ? 'btn-success shadow-sm' : 'btn-light border text-muted' }}" style="border-radius: 8px;">
        <i class="ph-fill ph-storefront fs-5"></i>
        <span class="fw-semibold">Hutang Lunas</span>
    </a>
</div>

<div class="row">
    @forelse($storesWithPayments as $store)
        @php
            $totalFinishedDebts = $store->debts->where('status', 'lunas')->count();
            $totalPayments = $store->debts->flatMap->payments->count();
            $totalAmountPaid = $store->debts->flatMap->payments->sum('amount_paid');
        @endphp

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-light rounded text-success">
                                <i class="ph-fill ph-check-circle fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $store->name }}</h5>
                                <span class="text-muted small">{{ $totalFinishedDebts }} Finished Invoice(s)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Payments Made</span>
                            <span class="fw-medium text-primary">{{ $totalPayments }} Transactions</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-top pt-1 mt-1">
                            <span class="fw-semibold">Total Amount Paid</span>
                            <span class="fw-bold text-success">Rp {{ number_format($totalAmountPaid, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('payments.showStore', $store->id) }}" class="btn btn-light w-100 text-primary fw-medium border">
                            View Payment History <i class="ph-bold ph-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <i class="ph-fill ph-clock text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="mb-2">No Payments Found</h4>
                    <p class="text-muted">You haven't made any payments or finished any debts yet.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
@if($storesWithPayments->hasPages())
<div class="card mt-4 border-0 shadow-sm">
    <div class="card-body">
        {{ $storesWithPayments->links() }}
    </div>
</div>
@endif
@endsection
