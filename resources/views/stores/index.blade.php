@extends('layouts.app')
@section('page_title', 'Stores Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('dashboard') }}" class="btn btn-light border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%;">
            <i class="ph-bold ph-arrow-left"></i>
        </a>
        <div>
            <h2 class="mb-1">Stores & Vendors</h2>
            <p class="text-muted mb-0">Manage all your supplier and vendor details.</p>
        </div>
    </div>
    <div>
        <a href="{{ route('stores.create') }}" class="btn btn-primary">
            <i class="ph-bold ph-plus"></i> Add New Store
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('stores.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label text-muted small mb-1">Search Stores</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ph ph-magnifying-glass"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by name or phone...">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="ph-bold ph-funnel me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Store Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $store->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 bg-light rounded text-primary">
                                        <i class="ph-fill ph-storefront fs-5"></i>
                                    </div>
                                    <span class="fw-medium text-dark">{{ $store->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                    {{ $store->address ?? 'No address provided' }}
                                </span>
                            </td>
                            <td>
                                @if($store->phone)
                                    <a href="tel:{{ $store->phone }}" class="text-decoration-none text-muted">
                                        <i class="ph ph-phone me-1"></i> {{ $store->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    @if(auth()->check() && auth()->user()->role === 'super_admin')
                                    <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-sm btn-light text-primary border" title="Edit">
                                        <i class="ph-bold ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('stores.destroy', $store->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this store?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border" title="Delete">
                                            <i class="ph-bold ph-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ph-fill ph-storefront fs-1 mb-2"></i>
                                    <p class="mb-0">No stores found. Add a store to start uploading receipts.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($stores->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $stores->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
