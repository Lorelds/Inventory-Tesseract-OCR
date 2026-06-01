@extends('layouts.app')
@section('page_title', 'Vendor Debts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('debts.index') }}" class="text-muted text-decoration-none"><i class="ph-bold ph-arrow-left"></i> Back</a>
            <span class="text-muted">/</span>
            <h2 class="mb-0">{{ $store->name }}</h2>
        </div>
        <p class="text-muted mb-0">Manage all unpaid invoices and payment history for this vendor.</p>
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
                                <th>Paid Amount</th>
                                <th>Remaining</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($store->debts as $debt)
                                @php
                                    $remaining = $debt->amount - $debt->paid_amount;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-medium text-primary">#{{ $debt->receipt_id }}</td>
                                    <td>{{ $debt->created_at->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($debt->amount, 0, ',', '.') }}</td>
                                    <td class="text-success">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</td>
                                    <td class="fw-bold text-danger">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
                                    <td>
                                        @if($debt->status == 'lunas')
                                            <span class="badge bg-success">Paid in Full</span>
                                        @elseif($debt->status == 'partial')
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($remaining > 0)
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payModal{{ $debt->id }}">
                                                Pay Now
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-light border text-muted" disabled>Fully Paid</button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Payment Modal for this Debt -->
                                @if($remaining > 0)
                                <div class="modal fade" id="payModal{{ $debt->id }}" tabindex="-1" aria-labelledby="payModalLabel{{ $debt->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                                                <h5 class="modal-title fw-bold" id="payModalLabel{{ $debt->id }}">Add Payment (Receipt #{{ $debt->receipt_id }})</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('debts.pay', $debt->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-4">
                                                    <div class="alert alert-light border mb-4">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span class="text-muted">Total Debt:</span>
                                                            <span class="fw-medium">Rp {{ number_format($debt->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted">Remaining Balance:</span>
                                                            <span class="fw-bold text-danger">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Payment Amount <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light fw-bold">Rp</span>
                                                            <input type="number" step="0.01" class="form-control" name="amount_paid" max="{{ $remaining }}" value="{{ $remaining }}" required>
                                                        </div>
                                                        <div class="form-text">Cannot exceed remaining balance.</div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Payment Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Payment Method</label>
                                                        <select class="form-select" name="payment_method">
                                                            <option value="Cash">Cash</option>
                                                            <option value="Bank Transfer">Bank Transfer</option>
                                                            <option value="E-Wallet">E-Wallet</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary px-4"><i class="ph-bold ph-check-circle me-1"></i> Submit Payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No debts recorded for this store.</td>
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
        <h4 class="mb-3 mt-4">Recent Payment History</h4>
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
                                    <td><span class="text-muted">#{{ $payment->receipt_id }}</span></td>
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
