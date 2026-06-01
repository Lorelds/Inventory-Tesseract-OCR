@extends('layouts.app')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <!-- Receipt Processing Card -->
    <div class="col-md-3">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold">Receipts</h6>
                    <div class="p-2 bg-white bg-opacity-25 rounded-circle d-flex">
                        <i class="ph-fill ph-receipt fs-4"></i>
                    </div>
                </div>
                <h3 class="mb-1">OCR Upload</h3>
                <p class="text-white-50 mb-4 small">Process new store receipts</p>
                <div class="mt-auto">
                    <a href="{{ route('admin.receipts.index') }}" class="btn btn-light btn-sm w-100 fw-semibold text-primary">
                        Scan Receipt
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Card -->
    <div class="col-md-3">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold">Inventory</h6>
                    <div class="p-2 bg-white bg-opacity-25 rounded-circle d-flex">
                        <i class="ph-fill ph-package fs-4"></i>
                    </div>
                </div>
                <h3 class="mb-1">Products</h3>
                <p class="text-white-50 mb-4 small">Manage your stock</p>
                <div class="mt-auto">
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-sm w-100 fw-semibold text-success">
                        View Database
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stores Card -->
    <div class="col-md-3">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold">Vendors</h6>
                    <div class="p-2 bg-white bg-opacity-25 rounded-circle d-flex">
                        <i class="ph-fill ph-storefront fs-4"></i>
                    </div>
                </div>
                <h3 class="mb-1">Stores</h3>
                <p class="text-white-50 mb-4 small">Manage supplier details</p>
                <div class="mt-auto">
                    <a href="{{ route('stores.index') }}" class="btn btn-light btn-sm w-100 fw-semibold" style="color: #6d28d9;">
                        Manage Stores
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Card -->
    <div class="col-md-3">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #334155, #0f172a); color: white;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold">System</h6>
                    <div class="p-2 bg-white bg-opacity-25 rounded-circle d-flex">
                        <i class="ph-fill ph-cpu fs-4"></i>
                    </div>
                </div>
                <h3 class="mb-1">Active</h3>
                <p class="text-white-50 mb-4 small">Laravel v{{ app()->version() }}</p>
                <div class="mt-auto">
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <span class="p-1 bg-success rounded-circle"></span> Tesseract OCR Ready
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="ph-fill ph-aperture text-primary" style="font-size: 4rem;"></i>
                </div>
                <h2 class="mb-3">Welcome to the OCR Inventory System</h2>
                <p class="text-muted w-75 mx-auto mb-4">
                    This advanced dashboard provides automated inventory management powered by Tesseract OCR technology. 
                    Begin by uploading receipt images to instantly extract product data, quantities, and pricing without manual entry.
                </p>
                <a href="{{ route('admin.receipts.index') }}" class="btn btn-primary px-4 py-2">
                    Start Scanning Receipts
                </a>
            </div>
        </div>
    </div>
</div>
@endsection