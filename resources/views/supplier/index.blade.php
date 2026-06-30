@extends('layouts.app')
@section('title', 'Kelola Supplier')
@section('page-title', 'Kelola Supplier')

@section('content')
<style>
    /* Premium Styling Overrides */
    .page-title-main {
        font-family: var(--font-display);
        font-weight: 700;
        color: #0f172a;
        font-size: 1.5rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
        color: #64748b;
    }
    
    .btn-add-supplier {
        background-color: #2563eb;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }
    
    .btn-add-supplier:hover {
        background-color: #1d4ed8;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    
    /* Search Filter Card */
    .filter-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .filter-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
    }
    
    .search-input-wrapper {
        position: relative;
    }
    
    .search-input-wrapper input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 40px 10px 16px;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s;
        width: 100%;
        background-color: #ffffff;
    }
    
    .search-input-wrapper input::placeholder {
        color: #94a3b8;
    }
    
    .search-input-wrapper input:focus {
        border-color: rgba(37, 99, 235, 0.8);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.22);
        outline: none;
    }
    
    .search-input-wrapper .search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 1.15rem;
        pointer-events: none;
        transition: color 0.3s;
    }

    @keyframes spin-loading {
        from { transform: translateY(-50%) rotate(0deg); }
        to { transform: translateY(-50%) rotate(360deg); }
    }
    .loading-spin {
        animation: spin-loading 0.8s linear infinite !important;
        color: #2563eb !important;
    }
    
    /* Stat Cards with Premium Hover Animations */
    .stat-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
        border-color: rgba(37, 99, 235, 0.15);
    }
    
    .stat-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon-wrapper i {
        font-size: 1.75rem;
    }
    
    .bg-primary-soft {
        background-color: rgba(37, 99, 235, 0.08) !important;
    }
    
    .bg-success-soft {
        background-color: rgba(16, 185, 129, 0.08) !important;
    }
    
    .bg-warning-soft {
        background-color: rgba(245, 158, 11, 0.08) !important;
    }
    
    .stat-content {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }
    
    .stat-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
    }
    
    .stat-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 6px;
    }
    
    .stat-subtitle {
        font-size: 0.78rem;
        font-weight: 500;
        color: #94a3b8;
    }
    
    .supplier-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        display: block;
    }
    
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
        border: 2px solid #f1f5f9;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .supplier-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    
    .supplier-table th {
        font-weight: 700;
        font-size: 0.78rem;
        color: #475569;
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 10px;
        text-align: left;
        line-height: 1.4;
        white-space: normal !important;
    }
    
    .supplier-table td {
        font-size: 0.82rem;
        padding: 10px 10px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    
    .supplier-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .supplier-table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        margin-top: 32px;
    }

    /* Column width hints */
    .col-no { width: 45px; }
    .col-telepon { white-space: nowrap; }
    
    .supplier-table th.col-status, 
    .supplier-table td.col-status { 
        width: 90px; 
        text-align: center !important; 
    }
    
    .supplier-table th.col-action, 
    .supplier-table td.col-action { 
        width: 80px; 
        text-align: center !important; 
    }
    
    /* Status Badges matching mockup */
    .badge-status-active {
        background-color: #e6fcf5 !important;
        color: #0ca678 !important;
        font-weight: 600;
        font-size: 0.74rem;
        padding: 5px 12px;
        border-radius: 9999px; /* Pill style */
        display: inline-block;
    }
    
    .badge-status-inactive {
        background-color: #fff5f5 !important;
        color: #f03e3e !important;
        font-weight: 600;
        font-size: 0.74rem;
        padding: 5px 12px;
        border-radius: 9999px; /* Pill style */
        display: inline-block;
    }
    
    /* Dropdown kustom */
    .btn-edit-dropdown {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        box-shadow: none;
    }
    
    .btn-edit-dropdown::after {
        display: none !important;
    }
    
    .btn-edit-dropdown:hover, .btn-edit-dropdown[aria-expanded="true"] {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }
    
    .btn-edit-dropdown i {
        font-size: 0.75rem;
        transition: transform 0.20s ease;
        color: #64748b;
    }
    
    .btn-edit-dropdown:hover i {
        color: #1e293b;
    }

    .btn-edit-dropdown[aria-expanded="true"] i {
        transform: rotate(180deg);
        color: #1e293b;
    }
    
    .action-dropdown-menu {
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.08);
        padding: 6px;
        min-width: 180px;
        background-color: #ffffff;
    }
    
    .action-dropdown-menu .dropdown-item {
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.88rem;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }
    
    .action-dropdown-menu .dropdown-item:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    
    .action-dropdown-menu .dropdown-item i {
        font-size: 1.1rem;
        color: #64748b;
    }
    
    /* Pagination style override */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
    }
    
    .pagination-info {
        font-size: 0.88rem;
        color: #64748b;
        font-weight: 500;
    }
    
    .pagination {
        display: flex;
        gap: 6px;
        margin: 0;
    }
    
    .pagination .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
        background-color: #ffffff;
        transition: all 0.2s;
        box-shadow: none;
    }
    
    .pagination .page-item .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: #ffffff;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title-main mb-1">Kelola Supplier</h1>
            <p class="page-subtitle mb-0">Kelola data supplier yang terdaftar pada sistem persediaan.</p>
        </div>
        <a href="{{ route('supplier.create') }}" class="btn-add-supplier" id="btnTambahSupplier">
            <i class="ph ph-plus bold"></i> Tambah Supplier
        </a>
    </div>

    {{-- Search Form Section --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('supplier.index') }}" id="formFilterSupplier">
            <div>
                <label for="inputSearchSupplier" class="filter-label">Cari Supplier</label>
                <div class="search-input-wrapper">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama supplier, kontak, atau alamat..." value="{{ request('search') }}" id="inputSearchSupplier">
                    <i class="ph ph-magnifying-glass search-icon"></i>
                </div>
            </div>
        </form>
    </div>

    {{-- Statistics Cards Section --}}
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-primary-soft">
                    <i class="ph ph-users text-primary"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Supplier</div>
                    <div class="stat-value">{{ $totalSupplier }}</div>
                    <div class="stat-subtitle">Supplier Terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-success-soft">
                    <i class="ph ph-handshake text-success"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Supplier Aktif</div>
                    <div class="stat-value">{{ $supplierAktif }}</div>
                    <div class="stat-subtitle">Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-warning-soft">
                    <i class="ph ph-pause text-warning"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Supplier Nonaktif</div>
                    <div class="stat-value">{{ $supplierNonaktif }}</div>
                    <div class="stat-subtitle">Nonaktif</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <h2 class="section-title">Daftar Supplier</h2>
    <div class="supplier-table-card">
        <div class="table-responsive">
            <table class="table align-middle supplier-table" id="tableSupplier">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-nama">Nama Supplier</th>
                        <th class="col-telepon">Telepon</th>
                        <th class="col-email">Email</th>
                        <th class="col-alamat">Alamat</th>
                        <th class="col-status">Status</th>
                        <th class="col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $i => $supplier)
                    <tr>
                        <td class="col-no text-muted">{{ $suppliers->firstItem() + $i }}</td>
                        <td class="col-nama fw-bold">{{ $supplier->nama_supplier }}</td>
                        <td class="col-telepon">{{ $supplier->telepon ?? '-' }}</td>
                        <td class="col-email text-muted">{{ $supplier->email ?? '-' }}</td>
                        <td class="col-alamat text-muted">{{ $supplier->alamat ?? '-' }}</td>
                        <td class="col-status">
                            @if($supplier->status_aktif)
                                <span class="badge badge-status-active">Aktif</span>
                            @else
                                <span class="badge badge-status-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td class="col-action">
                            <div class="dropdown">
                                <button class="btn-edit-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                                    <span>Edit</span>
                                    <i class="ph ph-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end action-dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('supplier.edit', $supplier) }}">
                                            <i class="ph ph-pencil-simple"></i> Edit Supplier
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('supplier.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ph ph-trash text-danger"></i> Hapus Supplier
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="ph ph-info fs-1 d-block mb-2"></i>
                            Belum ada data supplier yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Container --}}
        @if($suppliers->hasPages() || $suppliers->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() ?? 0 }} data
            </div>
            <div>
                {{ $suppliers->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('inputSearchSupplier');
        let timeout = null;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchIcon = document.querySelector('.search-input-wrapper .search-icon');
                if (searchIcon) {
                    searchIcon.className = 'ph ph-circle-notch search-icon loading-spin';
                }
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    document.getElementById('formFilterSupplier').submit();
                }, 750); // debounce 750ms
            });
            // Focus at the end of text when search is submitted
            const val = searchInput.value;
            if (val !== '') {
                searchInput.value = '';
                searchInput.focus();
                searchInput.value = val;
            }
        }
    });
</script>
@endsection
