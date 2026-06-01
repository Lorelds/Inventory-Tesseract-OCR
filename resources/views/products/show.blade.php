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
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Details Card -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-semibold text-dark"><i class="ph ph-info me-2"></i> Information Overview</h5>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 150px;">Product ID</td>
                            <td class="fw-medium">#{{ $product->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Buy Price</td>
                            <td class="fw-medium text-danger">Rp {{ number_format($product->buy_price, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sell Price</td>
                            <td class="fw-medium text-success">Rp {{ number_format($product->sell_price, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Profit Margin</td>
                            <td class="fw-medium text-primary">
                                @if($product->buy_price > 0)
                                    {{ number_format((($product->sell_price - $product->buy_price) / $product->buy_price) * 100, 1) }}%
                                @else
                                    100%
                                @endif
                                (Rp {{ number_format($product->sell_price - $product->buy_price, 0, ',', '.') }})
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date Added</td>
                            <td class="fw-medium">{{ $product->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Last Updated</td>
                            <td class="fw-medium">{{ $product->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
