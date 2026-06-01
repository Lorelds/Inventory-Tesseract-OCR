@extends('layouts.app')
@section('page_title', 'Products Inventory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Products Inventory</h2>
        <p class="text-muted mb-0">Manage all items, pricing, and stock levels.</p>
    </div>
    <div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-plus"></i> Add New Product
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">SKU</th>
                        <th>Product Name</th>
                        <th>Buy Price</th>
                        <th>Sell Price</th>
                        <th>Stock</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-secondary border font-monospace">{{ $product->sku }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 bg-light rounded text-success">
                                        <i class="ph-fill ph-package fs-5"></i>
                                    </div>
                                    <a href="{{ route('products.show', $product->id) }}" class="fw-medium text-dark text-decoration-none hover-primary">
                                        {{ $product->name }}
                                    </a>
                                </div>
                            </td>
                            <td>Rp {{ number_format($product->buy_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                            <td>
                                @if($product->stock <= 5 && $product->stock > 0)
                                    <span class="badge bg-warning text-dark"><i class="ph-fill ph-warning"></i> {{ $product->stock }} Low</span>
                                @elseif($product->stock == 0)
                                    <span class="badge bg-danger"><i class="ph-fill ph-x-circle"></i> Out of Stock</span>
                                @else
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success"><i class="ph-fill ph-check-circle"></i> {{ $product->stock }} In Stock</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-light text-secondary border" title="View Details">
                                        <i class="ph-bold ph-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-light text-primary border" title="Edit">
                                        <i class="ph-bold ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border" title="Delete">
                                            <i class="ph-bold ph-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ph-fill ph-package fs-1 mb-2"></i>
                                    <p class="mb-0">No products found. Start by adding a new product manually or scan a receipt.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
