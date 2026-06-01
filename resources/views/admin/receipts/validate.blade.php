@extends('layouts.app')
@section('page_title', 'Validate OCR Data')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Review OCR Results</h2>
        <p class="text-muted mb-0">Please verify and correct the extracted data before saving to inventory.</p>
    </div>
</div>

<form action="{{ route('admin.receipts.validateSubmit', $receipt->id) }}" method="POST">
    @csrf
    
    @if ($errors->any())
        <div class="alert alert-danger mb-4 border-0 shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <i class="ph-fill ph-warning-circle fs-4 me-2"></i>
                <h6 class="mb-0 fw-bold">Please fix the following errors:</h6>
            </div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Image Preview Column -->
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm h-100 sticky-top" style="top: 90px; z-index: 10;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-semibold text-dark"><i class="ph ph-image me-2"></i> Receipt Image</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="border rounded p-2 bg-light d-flex justify-content-center align-items-center" style="min-height: 400px; max-height: 600px; overflow: hidden;">
                        <img src="{{ Storage::url($receipt->image_path) }}" alt="Receipt" class="img-fluid rounded" style="object-fit: contain; max-height: 100%; width: auto;">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Validation Form Column -->
        <div class="col-lg-7">
            <!-- General Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-semibold text-dark"><i class="ph ph-receipt me-2"></i> General Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Store/Vendor</label>
                            <input type="text" class="form-control bg-light" value="{{ $receipt->store->name ?? 'Unknown' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label fw-medium">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $receipt->transaction_date ? \Carbon\Carbon::parse($receipt->transaction_date)->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="total_amount" class="form-label fw-medium">Total Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="{{ old('total_amount', $receipt->total_amount) }}" required>
                            </div>
                            <div class="form-text mt-1"><i class="ph ph-magic-wand"></i> Will update automatically based on item subtotals.</div>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-success w-100 mb-4" id="convertUsdBtn">
                                <i class="ph-bold ph-currency-circle-dollar me-1"></i> Convert USD to IDR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Items Validation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold text-dark mb-0"><i class="ph ph-list-numbers me-2"></i> Extracted Items</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                        <i class="ph-bold ph-plus"></i> Add Missing Item
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th style="width: 90px;">Qty</th>
                                    <th style="width: 140px;">Unit Price</th>
                                    <th style="width: 140px;">Subtotal</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $items = old('items', $parsedData['items'] ?? []);
                                @endphp
                                
                                @forelse($items as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" class="form-control" name="items[{{ $index }}][name]" value="{{ $item['name'] }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="1" min="1" class="form-control qty-input" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control price-input" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] }}" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control subtotal-input bg-light" value="{{ (float)$item['quantity'] * (float)$item['unit_price'] }}" readonly tabindex="-1">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-light text-danger remove-item-btn"><i class="ph-bold ph-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noItemsRow">
                                        <td colspan="5" class="text-center text-muted py-3">
                                            No items were automatically detected. Please add them manually.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-3 border-0 d-flex align-items-center">
                        <i class="ph-fill ph-info fs-4 me-2"></i>
                        <div>Items will be automatically matched to existing products or created as new products.</div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-3 mb-5">
                <a href="{{ route('admin.receipts.index') }}" class="btn btn-light border px-4 py-2">Cancel</a>
                <button type="submit" class="btn btn-success px-5 py-2 fw-semibold">
                    <i class="ph-bold ph-check-circle me-1"></i> Confirm & Save to Inventory
                </button>
            </div>
        </div>
    </div>
</form>

<template id="itemRowTemplate">
    <tr class="item-row">
        <td><input type="text" class="form-control" name="items[__INDEX__][name]" required></td>
        <td><input type="number" step="1" min="1" class="form-control qty-input" name="items[__INDEX__][quantity]" value="1" required></td>
        <td><input type="number" step="0.01" class="form-control price-input" name="items[__INDEX__][unit_price]" value="0" required></td>
        <td><input type="number" class="form-control subtotal-input bg-light" value="0" readonly tabindex="-1"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-light text-danger remove-item-btn"><i class="ph-bold ph-trash"></i></button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = document.querySelectorAll('.item-row').length;
        const totalAmountInput = document.getElementById('total_amount');
        
        function updateSubtotalsAndTotal() {
            let overallTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const subtotal = qty * price;
                
                row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
                overallTotal += subtotal;
            });
            
            totalAmountInput.value = overallTotal.toFixed(2);
        }

        // Listen for input changes to dynamically update subtotal
        document.getElementById('itemsTable').addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                updateSubtotalsAndTotal();
            }
        });

        // Add new item row
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const noItemsRow = document.getElementById('noItemsRow');
            if (noItemsRow) noItemsRow.remove();
            
            const template = document.getElementById('itemRowTemplate').innerHTML;
            const newRowHtml = template.replace(/__INDEX__/g, itemIndex++);
            
            const tbody = document.querySelector('#itemsTable tbody');
            tbody.insertAdjacentHTML('beforeend', newRowHtml);
        });
        
        // Remove item row (event delegation)
        document.getElementById('itemsTable').addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const row = e.target.closest('tr');
                row.remove();
                
                updateSubtotalsAndTotal();
                
                // If no rows left, add the empty message back
                if (document.querySelectorAll('.item-row').length === 0) {
                    const tbody = document.querySelector('#itemsTable tbody');
                    tbody.innerHTML = '<tr id="noItemsRow"><td colspan="5" class="text-center text-muted py-3">No items. Click "Add Missing Item" to add one.</td></tr>';
                }
            }
        });
        
        // Initial calculation to ensure subtotals match if manually edited earlier
        updateSubtotalsAndTotal();

        // USD to IDR Converter
        const convertUsdBtn = document.getElementById('convertUsdBtn');
        if (convertUsdBtn) {
            convertUsdBtn.addEventListener('click', async function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="ph-bold ph-spinner ph-spin me-1"></i> Fetching Rate...';
                this.disabled = true;

                try {
                    const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
                    const data = await response.json();
                    const idrRate = data.rates.IDR;

                    if (!idrRate) throw new Error("IDR rate not found");

                    // Ask for confirmation
                    if (confirm(`Current USD to IDR rate is Rp ${idrRate.toLocaleString('id-ID')}.\n\nDo you want to multiply all unit prices by this rate?`)) {
                        
                        document.querySelectorAll('.item-row').forEach(row => {
                            const priceInput = row.querySelector('.price-input');
                            const currentPrice = parseFloat(priceInput.value) || 0;
                            
                            // Multiply and format to 2 decimal places to avoid floating point issues
                            priceInput.value = (currentPrice * idrRate).toFixed(2);
                        });

                        // Update all subtotals and the grand total
                        updateSubtotalsAndTotal();
                        
                        alert('Successfully converted all prices to IDR!');
                    }
                } catch (error) {
                    console.error('Error fetching exchange rate:', error);
                    alert('Failed to fetch the latest exchange rate. Please check your internet connection.');
                } finally {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            });
        }
    });
</script>
@endpush