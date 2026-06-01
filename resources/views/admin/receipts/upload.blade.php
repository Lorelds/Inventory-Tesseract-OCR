@extends('layouts.app')
@section('page_title', 'Upload Receipt')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.receipts.index') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-1">Scan Receipt</h2>
            <p class="text-muted mb-0">Upload a receipt image to extract inventory and pricing data automatically.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5">
                <form action="{{ route('admin.receipts.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium d-block">Tipe Nota (Receipt Type) <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-check-inline p-3 border rounded w-100 me-0 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-1" type="radio" name="type" id="type_pembelian" value="pembelian" required>
                                    <label class="form-check-label fw-medium ms-2" for="type_pembelian">
                                        <i class="ph-bold ph-download-simple text-primary"></i> Nota Pembelian (Barang Masuk)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline p-3 border rounded w-100 me-0 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-1" type="radio" name="type" id="type_penjualan" value="penjualan" required checked>
                                    <label class="form-check-label fw-medium ms-2" for="type_penjualan">
                                        <i class="ph-bold ph-upload-simple text-success"></i> Nota Penjualan (Barang Keluar)
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="store_id" class="form-label fw-medium">Store / Vendor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ph ph-storefront"></i></span>
                                <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" name="store_id" required>
                                    <option value="" selected disabled>-- Select Store/Vendor --</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text mt-2">
                                <a href="{{ route('stores.create') }}" class="text-decoration-none"><i class="ph-bold ph-plus"></i> Add new vendor</a>
                            </div>
                            @error('store_id')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="payment_status" class="form-label fw-medium">Payment Status</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="ph ph-credit-card"></i></span>
                                <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                    <option value="lunas">Lunas (Paid)</option>
                                    <option value="hutang" selected>Hutang (Debt/Unpaid)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium">Receipt Image <span class="text-danger">*</span></label>
                        
                        <div class="drag-drop-zone position-relative" id="dropZone" onclick="document.getElementById('receipt_image').click()">
                            <i class="ph-fill ph-image icon"></i>
                            <h5 class="mb-2">Click to upload or drag and drop</h5>
                            <p class="text-muted small mb-0">PNG, JPG or JPEG (MAX. 5MB)</p>
                            <p id="fileName" class="text-primary fw-medium mt-3 mb-0" style="display:none;"></p>
                            
                            <input class="form-control d-none @error('receipt_image') is-invalid @enderror" type="file" id="receipt_image" name="receipt_image" accept="image/jpeg,image/png,image/jpg" required onchange="showFileName(this)">
                        </div>
                        @error('receipt_image')
                            <div class="text-danger mt-1 small text-center">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                            <i class="ph-bold ph-scan me-2"></i> Start OCR Extraction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showFileName(input) {
        const fileNameElement = document.getElementById('fileName');
        const dropZone = document.getElementById('dropZone');
        
        if (input.files && input.files[0]) {
            fileNameElement.textContent = "Selected: " + input.files[0].name;
            fileNameElement.style.display = 'block';
            dropZone.style.borderColor = 'var(--primary-color)';
            dropZone.style.backgroundColor = '#f0f9ff';
        }
    }
    
    // Simple drag and drop visual feedback
    const dropZone = document.getElementById('dropZone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        dropZone.style.borderColor = 'var(--primary-color)';
        dropZone.style.backgroundColor = '#e0f2fe';
    }
    
    function unhighlight(e) {
        dropZone.style.borderColor = 'var(--border-color)';
        dropZone.style.backgroundColor = '#f8fafc';
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;
        document.getElementById('receipt_image').files = files;
        showFileName(document.getElementById('receipt_image'));
    }
    
    document.getElementById('uploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing OCR...';
        btn.disabled = true;
    });
</script>
@endpush