@extends('layouts.app')
@section('page_title', 'Add Store')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('stores.index') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-1">Register New Store</h2>
            <p class="text-muted mb-0">Enter the details of the new vendor or supplier.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('stores.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">{{ __('Store Name') }}<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ph ph-storefront"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. ABC Supermarket" required>
                        </div>
                        @error('name')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="form-label fw-medium">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ph ph-phone"></i></span>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. 08123456789">
                        </div>
                        @error('phone')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label fw-medium">Complete Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Enter full address details">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4 border-light">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('stores.index') }}" class="btn btn-light border">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ph-bold ph-floppy-disk me-1"></i> Save Store
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
