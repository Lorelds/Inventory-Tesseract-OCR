@extends('layouts.app')
@section('page_title', 'Payment History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('payments.index') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-1">{{ $store->name }}</h2>
            <p class="text-muted mb-0">
                @if(($type ?? 'receivable') === 'payable')
                    Histori Hutang Kita yang Sudah Lunas
                @else
                    Histori Piutang Pelanggan yang Sudah Lunas
                @endif
            </p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Receipt ID</th>
                                <th>Date</th>
                                <th>Total Debt</th>
                                <th>{{ __('Paid Amount') }}</th>
                                <th>Remaining</th>
                                <th class="text-end pe-4">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($store->debts as $debt)
                                @php
                                    $remaining = $debt->amount - $debt->paid_amount;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('admin.receipts.show', $debt->receipt_id) }}" class="fw-medium text-primary text-decoration-none">
                                            #{{ $debt->receipt_id }}
                                        </a>
                                    </td>
                                    <td>{{ $debt->created_at->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($debt->amount, 0, ',', '.') }}</td>
                                    <td class="text-success">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</td>
                                    <td class="fw-bold text-danger">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
                                    <td class="text-end pe-4">
                                        @if($debt->status == 'lunas')
                                            <span class="badge bg-success">Paid in Full</span>
                                        @elseif($debt->status == 'partial')
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No finished debts recorded for this store.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h4 class="mb-3 mt-4">All Payment History</h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>For Receipt</th>
                                <th>Amount Paid</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Collect all payments for all debts of this store, sorted by date
                                $allPayments = collect();
                                foreach($store->debts as $d) {
                                    foreach($d->payments as $p) {
                                        $p->receipt_id = $d->receipt_id;
                                        $allPayments->push($p);
                                    }
                                }
                                $allPayments = $allPayments->sortByDesc('payment_date');
                            @endphp
                            
                            @forelse($allPayments as $payment)
                                <tr>
                                    <td class="ps-4">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.receipts.show', $payment->receipt_id) }}" class="text-decoration-none">
                                            <span class="text-muted">#{{ $payment->receipt_id }}</span>
                                        </a>
                                    </td>
                                    <td class="fw-bold text-success">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="ph-fill ph-wallet"></i> {{ $payment->payment_method ?? 'Unknown' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No payments have been made to this vendor yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
