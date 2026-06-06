@extends('layouts.app')
@section('page_title', 'Product Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('products.index') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-1">Product Details</h2>
            <p class="text-muted mb-0">View complete information and history for {{ $product->name }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->check() && auth()->user()->role === 'super_admin')
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
            <i class="ph-bold ph-pencil-simple"></i>{{ __('Edit Product') }}</a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4 mb-md-0">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-5">
                <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                    <i class="ph-fill ph-package text-primary" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-1">{{ $product->name }}</h4>
                <p class="text-muted mb-4 font-monospace">{{ $product->sku }}</p>
                
                <div class="p-3 bg-light rounded-3 text-start mb-4">
                    <div class="text-muted small fw-medium mb-1 text-uppercase tracking-wider">Current Status</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-semibold">Stock Level</span>
                        @if($product->stock <= 5 && $product->stock > 0)
                            <span class="badge bg-warning text-dark fs-6"><i class="ph-fill ph-warning"></i> {{ $product->stock }}</span>
                        @elseif($product->stock == 0)
                            <span class="badge bg-danger fs-6"><i class="ph-fill ph-x-circle"></i>{{ __('Out of Stock') }}</span>
                        @else
                            <span class="badge bg-success bg-opacity-25 text-success border border-success fs-6"><i class="ph-fill ph-check-circle"></i> {{ $product->stock }}</span>
                        @endif
                    </div>
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                            <i class="ph-bold ph-faders"></i> Adjust Stock Manually
                        </button>
                    </div>
                    <div class="p-3 bg-light rounded-3 text-start mt-4">
                        <div class="text-muted small fw-medium mb-3 text-uppercase tracking-wider border-bottom pb-2">Information Overview</div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Product ID</span>
                            <span class="fw-medium small">#{{ $product->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ __('Buy Price') }}</span>
                            <span class="fw-medium small text-danger">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ __('Sell Price') }}</span>
                            <span class="fw-medium small text-success">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Margin</span>
                            <span class="fw-medium small text-primary">
                                @if($product->buy_price > 0)
                                    {{ number_format((($product->sell_price - $product->buy_price) / $product->buy_price) * 100, 1) }}%
                                @else
                                    100%
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 mt-3 pt-2 border-top">
                            <span class="text-muted small">Date Added</span>
                            <span class="fw-medium small">{{ $product->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Last Updated</span>
                            <span class="fw-medium small">{{ $product->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Price History Graph -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-semibold text-dark"><i class="ph ph-trend-up me-2"></i> Price History</h5>
                <p class="text-muted small mb-0">Track purchase price changes over time based on scanned receipts.</p>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="priceHistoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Stock History Table -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-semibold text-dark"><i class="ph ph-clock-counter-clockwise me-2"></i> Stock Movement History</h5>
                <p class="text-muted small mb-0">Record of all stock changes (in and out).</p>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Balance</th>
                                <th class="pe-4">{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stock_movements as $movement)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $movement->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        @if($movement->type == 'in')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><i class="ph-bold ph-arrow-down-left me-1"></i> IN</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1"><i class="ph-bold ph-arrow-up-right me-1"></i> OUT</span>
                                        @endif
                                    </td>
                                    <td class="fw-medium {{ $movement->type == 'in' ? 'text-primary' : 'text-warning' }}">
                                        {{ $movement->type == 'in' ? '+' : '-' }}{{ $movement->quantity }}
                                    </td>
                                    <td class="fw-bold">{{ $movement->balance }}</td>
                                    <td class="pe-4 small text-muted">
                                        @if($movement->receipt_id)
                                            <a href="{{ route('admin.receipts.show', $movement->receipt_id) }}" class="text-decoration-none">
                                                {{ $movement->notes }}
                                            </a>
                                        @else
                                            {{ $movement->notes }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No stock movements recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-semibold">Adjust Stock Manually</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('products.adjust_stock', $product->id) }}" method="POST">
                @csrf
                <div class="modal-body pb-0 pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Type of Adjustment <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="adjust_in" value="in" required>
                                <label class="form-check-label fw-medium text-primary" for="adjust_in">
                                    <i class="ph-bold ph-arrow-down-left"></i> Stock IN (Add)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="adjust_out" value="out" required>
                                <label class="form-check-label fw-medium text-warning" for="adjust_out">
                                    <i class="ph-bold ph-arrow-up-right"></i> Stock OUT (Reduce)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-medium">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="0.01" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-medium">Notes / Reason <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="notes" name="notes" placeholder="e.g. Broken item, returns, etc." required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-2 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary px-4">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('priceHistoryChart').getContext('2d');
        const labels = @json($price_history_dates);
        const data = @json($price_history_values);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Buy Price (Rp)',
                    data: data,
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.2,
                    pointBackgroundColor: '#0ea5e9',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value, index, values) {
                                if(value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + 'M';
                                } else if(value >= 1000) {
                                    return 'Rp ' + (value / 1000) + 'k';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
