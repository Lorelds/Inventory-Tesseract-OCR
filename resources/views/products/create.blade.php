@extends('layouts.app')
@section('page_title', 'Add Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Add New Product</h2>
        <p class="text-muted mb-0">Create a new inventory item manually.</p>
    </div>
    <div>
        <a href="{{ route('products.index') }}" class="btn btn-light border">
            <i class="ph-bold ph-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">Product Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ph ph-package"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. T-Shirt Polo" required>
                        </div>
                        @error('name')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="category" class="form-label fw-medium">Category</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ph ph-tag"></i></span>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" placeholder="e.g. Paku, Bendrat, Seng">
                        </div>
                        <div class="form-text mt-1"><i class="ph ph-info"></i> Group this product. Leave blank if none.</div>
                        @error('category')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="buy_price" class="form-label fw-medium">Buy Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" step="0.01" class="form-control @error('buy_price') is-invalid @enderror" id="buy_price" name="buy_price" value="{{ old('buy_price') }}" placeholder="0" required>
                            </div>
                            @error('buy_price')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="sell_price" class="form-label fw-medium">Sell Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" step="0.01" class="form-control @error('sell_price') is-invalid @enderror" id="sell_price" name="sell_price" value="{{ old('sell_price') }}" placeholder="0" required>
                            </div>
                            @error('sell_price')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="stock" class="form-label fw-medium">Initial Stock Quantity <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ph ph-stack"></i></span>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                        </div>
                        @error('stock')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4 border-light">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ph-bold ph-floppy-disk me-1"></i> Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
