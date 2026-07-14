@extends('layouts.app')
@section('title', 'Stok Opname')
@section('page-title', 'Stok Opname')

@section('content')
<style>
    /* Premium Styling */
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
    
    .btn-add-opname {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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
    
    .btn-add-opname:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .btn-history-opname {
        background: #ffffff;
        color: #2563eb;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 10px 18px;
        border-radius: 8px;
        border: 1px solid rgba(37, 99, 235, 0.2);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-history-opname:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: rgba(37, 99, 235, 0.3);
    }
    
    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .filter-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }
    
    /* Stat Cards */
    .stat-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
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
    
    .bg-primary-soft { background-color: rgba(37, 99, 235, 0.08) !important; }
    .bg-success-soft { background-color: rgba(16, 185, 129, 0.08) !important; }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.08) !important; }
    .bg-danger-soft { background-color: rgba(220, 38, 38, 0.08) !important; }
    
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
        font-size: 1.80rem;
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
    
    /* Table Section */
    .section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        margin-top: 32px;
    }
    
    .section-subtitle {
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .section-subtitle i {
        font-size: 1rem;
        color: #3b82f6;
    }
    
    .opname-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .opname-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    
    .opname-table th {
        font-weight: 600;
        font-size: 0.78rem;
        color: #ffffff;
        background-color: #1e3a8a !important;
        border-bottom: none;
        padding: 10px 8px;
        text-align: left;
        line-height: 1.4;
        white-space: normal !important;
    }
    
    .opname-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    
    .opname-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .opname-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Column width hints */
    .col-no { width: 35px; text-align: center; }
    .col-date { white-space: nowrap; }
    .col-kode { white-space: nowrap; }

    .opname-table th.col-sistem, .opname-table td.col-sistem,
    .opname-table th.col-fisik, .opname-table td.col-fisik,
    .opname-table th.col-selisih, .opname-table td.col-selisih,
    .opname-table th.col-status, .opname-table td.col-status,
    .opname-table th.col-no, .opname-table td.col-no {
        text-align: center !important;
    }

    /* Status Badge */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    
    .badge-sesuai {
        background-color: rgba(16, 185, 129, 0.08);
        color: #059669;
    }
    
    .badge-kurang {
        background-color: rgba(220, 38, 38, 0.08);
        color: #dc2626;
    }
    
    .badge-lebih {
        background-color: rgba(37, 99, 235, 0.08);
        color: #2563eb;
    }
    
    /* Pagination */
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

    /* ── Searchable Select Dropdown ── */
    .searchable-select {
        position: relative;
        width: 100%;
    }
    .searchable-select-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .searchable-select-trigger:hover {
        border-color: #3b82f6 !important;
    }
    .searchable-select.open .searchable-select-trigger {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }
    .searchable-select-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.8rem;
        color: #475569;
        text-align: left;
    }
    .searchable-select-text.has-value {
        color: #1e293b;
        font-weight: 500;
    }
    .searchable-select-arrow {
        font-size: 0.85rem;
        color: #94a3b8;
        transition: transform 0.25s ease;
        flex-shrink: 0;
        margin-left: 8px;
    }
    .searchable-select.open .searchable-select-arrow {
        transform: rotate(180deg);
    }
    .searchable-select-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04);
        z-index: 1050;
        display: none;
        max-height: 280px;
        overflow: hidden;
        animation: searchableDropFadeIn 0.2s ease;
    }
    @keyframes searchableDropFadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .searchable-select.open .searchable-select-dropdown {
        display: block;
    }
    .searchable-select-search-wrapper {
        position: relative;
        padding: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .searchable-select-search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.9rem;
        color: #94a3b8;
        pointer-events: none;
    }
    .searchable-select-search {
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 5px 8px 5px 28px;
        font-size: 0.8rem;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #f8fafc;
    }
    .searchable-select-search:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.08);
        background: #fff;
    }
    .searchable-select-options {
        list-style: none;
        margin: 0;
        padding: 4px 0;
        max-height: 180px;
        overflow-y: auto;
        text-align: left;
    }
    .searchable-select-options::-webkit-scrollbar {
        width: 5px;
    }
    .searchable-select-options::-webkit-scrollbar-track {
        background: transparent;
    }
    .searchable-select-options::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .searchable-select-options li {
        padding: 8px 12px;
        font-size: 0.8rem;
        color: #334155;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .searchable-select-options li:hover {
        background: #eff6ff;
        color: #1e40af;
    }
    .searchable-select-options li.active {
        background: #3b82f6;
        color: #fff;
        font-weight: 600;
    }
    .searchable-select-options li .opt-code {
        background: #f1f5f9;
        color: #64748b;
        padding: 1px 5px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        flex-shrink: 0;
    }
    .searchable-select-options li.active .opt-code {
        background: rgba(255,255,255,0.2);
        color: #fff;
    }
    .searchable-select-options li .opt-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .searchable-select-empty {
        padding: 15px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.8rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title-main mb-1">Stok Opname</h1>
            <p class="page-subtitle mb-0">Kelola pencocokan stok fisik dengan stok pada sistem.</p>
        </div>
        <a href="{{ route('stok-opname.create') }}" class="btn-add-opname" id="btnInputOpname">
            <i class="ph ph-clipboard-text bold"></i> Input Stok Opname
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-primary-soft">
                    <i class="ph ph-clipboard-text text-primary"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Opname</div>
                    <div class="stat-value">{{ number_format($totalOpname) }}</div>
                    <div class="stat-subtitle">Pencatatan</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-success-soft">
                    <i class="ph ph-check-circle text-success"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Sesuai</div>
                    <div class="stat-value">{{ number_format($totalSesuai) }}</div>
                    <div class="stat-subtitle">Stok cocok</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-warning-soft">
                    <i class="ph ph-warning text-warning"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Selisih</div>
                    <div class="stat-value">{{ number_format($totalSelisih) }}</div>
                    <div class="stat-subtitle">Stok tidak cocok</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-danger-soft">
                    <i class="ph ph-minus-circle text-danger"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Selisih</div>
                    <div class="stat-value">{{ number_format($totalPcsSelisih) }}</div>
                    <div class="stat-subtitle">Pcs (absolut)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('stok-opname.index') }}" class="row g-3" id="formFilterOpname">
            <div class="col-md-3">
                <label class="filter-label">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}" min="2026-01-01">
            </div>
            <div class="col-md-3">
                <label class="filter-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}" min="2026-01-01">
            </div>
            <div class="col-md-4">
                <label class="filter-label">Produk</label>
                {{-- Hidden native select to hold the real value --}}
                <select name="product_id" id="product_id_select" style="display:none;">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-kode="{{ $p->kode_produk }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->kode_produk }} - {{ $p->nama_produk }}
                        </option>
                    @endforeach
                </select>
                {{-- Custom searchable dropdown --}}
                <div class="searchable-select" id="searchableProductSelect">
                    <div class="searchable-select-trigger form-control form-control-sm" id="productSelectTrigger" style="min-height: 31px; padding: 4px 10px; border-radius: .25rem; background: #fff; border: 1px solid #ced4da;">
                        <span class="searchable-select-text" id="productSelectText" style="font-size: 0.8rem;">Semua Produk</span>
                        <i class="ph ph-caret-down searchable-select-arrow" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="searchable-select-dropdown" id="productSelectDropdown" style="top: calc(100% + 2px); border-radius: 8px;">
                        <div class="searchable-select-search-wrapper" style="padding: 6px 8px;">
                            <i class="ph ph-magnifying-glass searchable-select-search-icon" style="left: 16px; font-size: 0.85rem;"></i>
                            <input type="text" class="searchable-select-search" id="productSearchInput" placeholder="Ketik nama atau kode produk..." autocomplete="off" style="padding: 4px 8px 4px 26px; font-size: 0.8rem; border-radius: 6px;">
                        </div>
                        <ul class="searchable-select-options" id="productOptionsList" style="max-height: 180px;">
                            {{-- Options populated by JS --}}
                        </ul>
                        <div class="searchable-select-empty" id="productEmptyMsg" style="display:none; padding: 12px;">
                            <i class="ph ph-magnifying-glass" style="font-size: 1rem; opacity: 0.4;"></i>
                            <span style="font-size: 0.8rem;">Produk tidak ditemukan</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-50"><i class="ph ph-funnel"></i> Filter</button>
                <a href="{{ route('stok-opname.index') }}" class="btn btn-light btn-sm w-50">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <h2 class="section-title">Riwayat Stok Opname</h2>
    <div class="section-subtitle">
        <i class="ph ph-info-fill"></i>
        <span>Perbandingan stok sistem vs stok fisik. Selisih otomatis disesuaikan pada saat penyimpanan.</span>
    </div>
    
    <div class="opname-table-card">
        <div class="table-responsive">
            <table class="table align-middle opname-table" id="tableStokOpname">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th class="col-date">Tanggal</th>
                        <th class="col-kode">Kode Produk</th>
                        <th class="col-product">Nama Produk</th>
                        <th class="col-sistem">Stok Sistem</th>
                        <th class="col-fisik">Stok Fisik</th>
                        <th class="col-selisih">Selisih</th>
                        <th class="col-user">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOpnames as $i => $opname)
                    <tr>
                        <td class="col-no text-muted">{{ $stockOpnames->firstItem() + $i }}</td>
                        <td class="col-date">{{ \Carbon\Carbon::parse($opname->tanggal_opname)->format('d/m/Y') }}</td>
                        <td class="col-kode fw-semibold text-muted">{{ $opname->product->kode_produk ?? '-' }}</td>
                        <td class="col-product fw-bold">{{ $opname->product->nama_produk ?? '-' }}</td>
                        <td class="col-sistem">{{ number_format($opname->stok_sistem) }}</td>
                        <td class="col-fisik fw-semibold">{{ number_format($opname->stok_fisik) }}</td>
                        <td class="col-selisih">
                            @if($opname->selisih > 0)
                                <span class="text-primary fw-bold">+{{ $opname->selisih }}</span>
                            @elseif($opname->selisih < 0)
                                <span class="text-danger fw-bold">{{ $opname->selisih }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="col-user text-muted">{{ $opname->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="ph ph-clipboard-text fs-1 d-block mb-2"></i>
                            Belum ada data stok opname.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($stockOpnames->hasPages() || $stockOpnames->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan {{ $stockOpnames->firstItem() ?? 0 }} - {{ $stockOpnames->lastItem() ?? 0 }} dari {{ $stockOpnames->total() ?? 0 }} data
            </div>
            <div>
                {{ $stockOpnames->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('product_id_select');
        const searchableProductSelect = document.getElementById('searchableProductSelect');
        const productSelectTrigger = document.getElementById('productSelectTrigger');
        const productSelectText = document.getElementById('productSelectText');
        const productSelectDropdown = document.getElementById('productSelectDropdown');
        const productSearchInput = document.getElementById('productSearchInput');
        const productOptionsList = document.getElementById('productOptionsList');
        const productEmptyMsg = document.getElementById('productEmptyMsg');

        // Extract options from the native select
        const optionsData = [];
        for (let i = 0; i < productSelect.options.length; i++) {
            const opt = productSelect.options[i];
            optionsData.push({
                value: opt.value,
                text: opt.text,
                code: opt.getAttribute('data-kode') || ''
            });
        }

        // Render searchable list items
        function renderOptions(filterText = '') {
            productOptionsList.innerHTML = '';
            const normalizedFilter = filterText.toLowerCase().trim();
            let matches = 0;

            optionsData.forEach(opt => {
                const optTextLower = opt.text.toLowerCase();
                const optCodeLower = opt.code.toLowerCase();

                if (optTextLower.includes(normalizedFilter) || optCodeLower.includes(normalizedFilter)) {
                    matches++;
                    const li = document.createElement('li');
                    li.setAttribute('data-value', opt.value);
                    if (productSelect.value === opt.value) {
                        li.className = 'active';
                    }
                    
                    if (opt.value === '') {
                        li.innerHTML = `<span class="opt-name fw-semibold" style="font-size: 0.8rem; color: #475569;">${opt.text}</span>`;
                    } else {
                        li.innerHTML = `
                            <span class="opt-code">${opt.code}</span>
                            <span class="opt-name">${opt.text.replace(opt.code + ' - ', '')}</span>
                        `;
                    }

                    li.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectProduct(opt.value, opt.text);
                    });
                    productOptionsList.appendChild(li);
                }
            });

            if (matches === 0) {
                productEmptyMsg.style.display = 'flex';
            } else {
                productEmptyMsg.style.display = 'none';
            }
        }

        // Handle Product Selection
        function selectProduct(val, text) {
            productSelect.value = val;

            if (val) {
                const optData = optionsData.find(o => o.value === val);
                const cleanName = optData ? optData.text.replace(optData.code + ' - ', '') : text;
                const displayHtml = optData && optData.code ? `<span class="opt-code me-1" style="font-size: 0.7rem; padding: 2px 5px; background: #e2e8f0; border-radius: 4px; color: #475569;">${optData.code}</span> <span class="fw-semibold text-dark" style="font-size: 0.8rem;">${cleanName}</span>` : `<span class="text-dark fw-semibold" style="font-size: 0.8rem;">${text}</span>`;
                
                productSelectText.innerHTML = displayHtml;
                productSelectText.classList.add('has-value');
            } else {
                productSelectText.innerHTML = 'Semua Produk';
                productSelectText.classList.remove('has-value');
            }
            closeProductDropdown();
        }

        // Open/Close dropdown
        productSelectTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = searchableProductSelect.classList.contains('open');
            if (isOpen) {
                closeProductDropdown();
            } else {
                openProductDropdown();
            }
        });

        function openProductDropdown() {
            searchableProductSelect.classList.add('open');
            productSearchInput.focus();
            renderOptions(productSearchInput.value);
        }

        function closeProductDropdown() {
            searchableProductSelect.classList.remove('open');
        }

        // Input filtering
        productSearchInput.addEventListener('input', function() {
            renderOptions(this.value);
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchableProductSelect.contains(e.target)) {
                closeProductDropdown();
            }
        });

        // Initialize state on page load
        if (productSelect.value) {
            const initialOpt = optionsData.find(o => o.value === productSelect.value);
            if (initialOpt) {
                selectProduct(initialOpt.value, initialOpt.text);
            }
        } else {
            selectProduct('', 'Semua Produk');
        }

        // Initialize option list
        renderOptions();
    });
</script>
@endsection
