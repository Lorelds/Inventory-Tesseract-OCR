@extends('layouts.app')
@section('page_title', 'Product Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Product Details</h2>
        <p class="text-muted mb-0">View complete information and history for {{ $product->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-light border">
            <i class="ph-bold ph-arrow-left"></i> Back
        </a>
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
            <i class="ph-bold ph-pencil-simple"></i> Edit Product
        </a>
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
                            <span class="badge bg-danger fs-6"><i class="ph-fill ph-x-circle"></i> Out of Stock</span>
                        @else
                            <span class="badge bg-success bg-opacity-25 text-success border border-success fs-6"><i class="ph-fill ph-check-circle"></i> {{ $product->stock }}</span>
                        @endif
                    </div>
                    <div class="p-3 bg-light rounded-3 text-start mt-4">
                        <div class="text-muted small fw-medium mb-3 text-uppercase tracking-wider border-bottom pb-2">Information Overview</div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Product ID</span>
                            <span class="fw-medium small">#{{ $product->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Buy Price</span>
                            <span class="fw-medium small text-danger">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Sell Price</span>
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
