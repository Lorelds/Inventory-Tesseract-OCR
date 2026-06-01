@extends('layouts.app')
@section('page_title', 'Debts Overview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Debt Management</h2>
        <p class="text-muted mb-0">Overview of all outstanding and paid debts grouped by vendor.</p>
    </div>
</div>

<div class="row">
    @forelse($storesWithDebts as $store)
        @php
            $totalDebt = $store->debts->sum('amount');
            $totalPaid = $store->debts->sum('paid_amount');
            $remaining = $totalDebt - $totalPaid;
            $percentage = $totalDebt > 0 ? ($totalPaid / $totalDebt) * 100 : 0;
            
            // Count active debts (hutang or partial)
            $activeDebtsCount = $store->debts->whereIn('status', ['hutang', 'partial'])->count();
        @endphp

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-light rounded text-primary">
                                <i class="ph-fill ph-storefront fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $store->name }}</h5>
                                <span class="text-muted small">{{ $activeDebtsCount }} Active Invoice(s)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Debt</span>
                            <span class="fw-medium">Rp {{ number_format($totalDebt, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Paid</span>
                            <span class="fw-medium text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-top pt-1 mt-1">
                            <span class="fw-semibold">Remaining Balance</span>
                            <span class="fw-bold text-danger">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('debts.showStore', $store->id) }}" class="btn btn-light w-100 text-primary fw-medium border">
                            Manage Vendor Debts <i class="ph-bold ph-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <i class="ph-fill ph-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                    <h4 class="mb-2">No Debts Found</h4>
                    <p class="text-muted">You currently do not have any debts recorded with any vendors. All your inventory receipts are fully paid!</p>
                    <a href="{{ route('admin.receipts.index') }}" class="btn btn-primary mt-3">Upload New Receipt</a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
