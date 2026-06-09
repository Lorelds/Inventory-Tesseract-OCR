@extends('layouts.app')
@section('page_title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('products.index') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-1">{{ __('Edit Product') }}</h2>
            <p class="text-muted mb-0">Update information for {{ $product->name }}.</p>
        </div>
    </div>
</div>

<form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Left Column: Image Upload -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <h5 class="card-title fw-semibold mb-4">Product Image</h5>
                    
                    <div class="position-relative mb-3">
                        <div id="imagePreview" class="bg-light rounded-3 d-flex align-items-center justify-content-center overflow-hidden border border-dashed border-2" 
                             style="height: 250px; cursor: pointer; @if($product->image_path) background-image: url('{{ Storage::url($product->image_path) }}'); background-size: cover; background-position: center; @endif" 
                             onclick="document.getElementById('image').click()">
                            
                            <div class="text-muted text-center" id="uploadPlaceholder" style="@if($product->image_path) display: none; @endif">
                                <i class="ph-bold ph-image fs-1 mb-2"></i>
                                <p class="mb-0 small fw-medium">Click or Drag Image Here</p>
                                <p class="text-muted" style="font-size: 0.75rem;">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                    <input class="form-control d-none @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                    <div class="form-text mt-2"><i class="ph ph-info"></i> Uploading a new image will replace the existing one.</div>
                    @error('image')
                        <div class="text-danger mt-1 small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Right Column: Product Details -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4">Product Details</h5>
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">{{ __('Product Name') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ph ph-package text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        </div>
                        @error('name')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="category" class="form-label fw-medium">{{ __('Category') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ph ph-tag text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $product->category) }}" placeholder="e.g. Paku, Bendrat, Seng">
                        </div>
                        <div class="form-text mt-1"><i class="ph ph-info"></i> Group this product. Leave blank if none.</div>
                        @error('category')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <label for="buy_price" class="form-label fw-medium">{{ __('Buy Price') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                                <input type="number" step="0.01" class="form-control border-start-0 ps-0 @error('buy_price') is-invalid @enderror" id="buy_price" name="buy_price" value="{{ old('buy_price', $product->buy_price) }}" required>
                            </div>
                            @error('buy_price')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="sell_price" class="form-label fw-medium">{{ __('Sell Price') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                                <input type="number" step="0.01" class="form-control border-start-0 ps-0 @error('sell_price') is-invalid @enderror" id="sell_price" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" required>
                            </div>
                            @error('sell_price')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="stock" class="form-label fw-medium">Current Stock <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ph ph-stack text-muted"></i></span>
                            <input type="number" class="form-control border-start-0 ps-0 @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                        </div>
                        <div class="form-text mt-2"><i class="ph ph-info"></i> Adjusting stock manually is not recommended. Use receipts to update stock dynamically.</div>
                        @error('stock')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4 border-light">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light border">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ph-bold ph-check-circle me-1"></i> Update Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const imagePreview = document.getElementById('imagePreview');
    const imageInput = document.getElementById('image');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreview.style.backgroundImage = `url('${e.target.result}')`;
                imagePreview.style.backgroundSize = 'cover';
                imagePreview.style.backgroundPosition = 'center';
                uploadPlaceholder.style.display = 'none';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Drag and drop logic
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        imagePreview.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        imagePreview.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        imagePreview.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        imagePreview.classList.add('border-primary');
        imagePreview.classList.remove('border-dashed');
    }

    function unhighlight(e) {
        imagePreview.classList.remove('border-primary');
        imagePreview.classList.add('border-dashed');
    }

    imagePreview.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;
        imageInput.files = files;
        previewImage(imageInput);
    }
</script>
@endpush
