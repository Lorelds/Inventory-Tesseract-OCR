@extends('layouts.app')
@section('page_title', 'Receipt History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Receipt History</h2>
        <p class="text-muted mb-0">View all scanned receipts and their current status.</p>
    </div>
    <div>
        <a href="{{ route('admin.receipts.upload.form') }}" class="btn btn-primary px-4 fw-medium">
            <i class="ph-bold ph-plus me-1"></i> Upload New Receipt
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Receipt ID</th>
                        <th>Date</th>
                        <th>Store / Vendor</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Validation Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                        <tr>
                            <td class="ps-4 fw-medium text-primary">#{{ str_pad($receipt->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $receipt->transaction_date ? \Carbon\Carbon::parse($receipt->transaction_date)->format('d M Y') : 'Unknown' }}</td>
                            <td>
                                @if($receipt->store)
                                    {{ $receipt->store->name }}
                                @else
                                    {{ $receipt->store_name ?? 'Unknown' }}
                                @endif
                            </td>
                            <td class="fw-medium">Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($receipt->payment_status == 'lunas')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="ph-fill ph-check-circle me-1"></i> Paid in Full</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1"><i class="ph-fill ph-clock me-1"></i> Debt (Hutang)</span>
                                @endif
                            </td>
                            <td>
                                @if($receipt->status == 'validated')
                                    <span class="badge bg-primary">Validated</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($receipt->status == 'pending')
                                    <a href="{{ route('admin.receipts.validate', $receipt->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="ph-bold ph-check-square-offset"></i> Validate
                                    </a>
                                @else
                                    <a href="{{ route('admin.receipts.show', $receipt->id) }}" class="btn btn-sm btn-light border text-primary">
                                        <i class="ph-bold ph-eye"></i> View Details
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="ph-fill ph-receipt fs-1 mb-2 text-light"></i>
                                <h5>No Receipts Found</h5>
                                <p>You haven't uploaded any receipts yet.</p>
                                <a href="{{ route('admin.receipts.upload.form') }}" class="btn btn-sm btn-primary mt-2">Upload Now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($receipts->hasPages())
    <div class="card-footer bg-white border-top-0 pt-4">
        {{ $receipts->links() }}
    </div>
    @endif
</div>
@endsection
