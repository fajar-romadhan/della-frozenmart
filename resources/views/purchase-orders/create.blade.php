@extends('layouts.app')
@section('title', 'Pemesanan Produk')
@section('page-title', 'Pemesanan Produk')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs & Header --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Transaksi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pemesanan-supplier.index') }}" class="text-decoration-none">Pemesanan Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buat Pemesanan</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0" style="color: #0f172a; font-family: var(--font-display);">Pemesanan Produk</h4>
    </div>

    <form action="{{ route('pemesanan-supplier.store') }}" method="POST">
        @csrf
        
        {{-- Section 1: Data Pemesanan --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08) !important;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="ph ph-calendar-blank me-2 text-primary"></i>Data Pemesanan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Tanggal Pemesanan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ph ph-calendar"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="{{ date('d/m/Y') }}" readonly disabled style="font-weight: 500;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label small fw-bold text-muted">Pilih Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required style="border-radius: 8px;">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Detail Pemesanan --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08) !important; overflow: visible !important;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="ph ph-shopping-bag me-2 text-primary"></i>Detail Pemesanan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="product_id" class="form-label small fw-bold text-muted">Nama Produk <span class="text-danger">*</span></label>
                        {{-- Hidden native select --}}
                        <select name="product_id" id="product_id" style="display:none;" required>
                            <option value="">Pilih Produk</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id', $selectedProductId ?? '') == $p->id ? 'selected' : '' }} data-kode="{{ $p->kode_produk }}" data-stok="{{ $p->stok_saat_ini }}">
                                    {{ $p->kode_produk }} - {{ $p->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Custom searchable select dropdown --}}
                        <div class="searchable-select" id="searchableProductSelect">
                            <div class="searchable-select-trigger form-control" id="productSelectTrigger" style="border-radius: 8px; min-height: 38px;">
                                <span class="searchable-select-text" id="productSelectText">Pilih Produk</span>
                                <i class="ph ph-caret-down searchable-select-arrow"></i>
                            </div>
                            <div class="searchable-select-dropdown" id="productSelectDropdown">
                                <div class="searchable-select-search-wrapper">
                                    <i class="ph ph-magnifying-glass searchable-select-search-icon"></i>
                                    <input type="text" class="searchable-select-search" id="productSearchInput" placeholder="Ketik nama atau kode produk..." autocomplete="off">
                                </div>
                                <ul class="searchable-select-options" id="productOptionsList">
                                    {{-- Options populated by JS --}}
                                </ul>
                                <div class="searchable-select-empty" id="productEmptyMsg" style="display:none; padding: 15px; text-align: center; color: #94a3b8; font-size: 0.85rem;">
                                    <span>Produk tidak ditemukan</span>
                                </div>
                            </div>
                        </div>
                        @error('product_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah_pesan" class="form-label small fw-bold text-muted">Jumlah Produk (pcs) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah_pesan" id="jumlah_pesan" class="form-control @error('jumlah_pesan') is-invalid @enderror" value="{{ old('jumlah_pesan') }}" min="1" required style="border-radius: 8px 0 0 8px;">
                            <span class="input-group-text bg-light">pcs</span>
                        </div>
                        @error('jumlah_pesan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('pemesanan-supplier.index') }}" class="btn btn-light px-4 fw-semibold" style="border-radius: 8px; border: 1px solid #cbd5e1;">Batal</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 8px;"><i class="ph ph-floppy-disk me-1"></i> Simpan Pesanan</button>
        </div>
    </form>
</div>

<style>
    .text-teal-dark { color: #0f766e !important; }

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
        min-height: 42px;
        padding: 8px 14px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .searchable-select-trigger:hover {
        border-color: #3b82f6;
    }
    .searchable-select.open .searchable-select-trigger {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }
    .searchable-select-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.85rem;
        color: #475569;
    }
    .searchable-select-text.has-value {
        color: #1e293b;
        font-weight: 500;
    }
    .searchable-select-arrow {
        font-size: 1rem;
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
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1), 0 2px 8px rgba(0,0,0,0.06);
        z-index: 1050;
        display: none;
        max-height: 320px;
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
        padding: 10px 12px 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .searchable-select-search-icon {
        position: absolute;
        left: 22px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        color: #94a3b8;
        pointer-events: none;
    }
    .searchable-select-search {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px 8px 34px;
        font-size: 0.83rem;
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
        max-height: 220px;
        overflow-y: auto;
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
        padding: 9px 16px;
        font-size: 0.83rem;
        color: #334155;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        display: flex;
        align-items: center;
        gap: 8px;
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
        padding: 2px 7px;
        border-radius: 5px;
        font-size: 0.75rem;
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('product_id');
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
            if (opt.value) {
                optionsData.push({
                    value: opt.value,
                    text: opt.text,
                    code: opt.getAttribute('data-kode') || '',
                    stok: opt.getAttribute('data-stok') || '0'
                });
            }
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
                    
                    const cleanName = opt.text.replace(opt.code + ' - ', '');
                    li.innerHTML = `
                        <span class="opt-code">${opt.code}</span>
                        <span class="opt-name">${cleanName} (Stok: ${opt.stok})</span>
                    `;

                    li.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectProduct(opt.value, opt.text);
                    });
                    productOptionsList.appendChild(li);
                }
            });

            if (matches === 0) {
                productEmptyMsg.style.display = 'block';
            } else {
                productEmptyMsg.style.display = 'none';
            }
        }

        // Handle Product Selection
        function selectProduct(val, text) {
            productSelect.value = val;
            productSelect.dispatchEvent(new Event('change'));

            if (val) {
                const optData = optionsData.find(o => o.value === val);
                const cleanName = optData ? optData.text.replace(optData.code + ' - ', '') : text;
                const displayHtml = optData ? `<span class="opt-code me-2">${optData.code}</span> <span class="fw-semibold text-dark">${cleanName} (Stok: ${optData.stok})</span>` : text;
                
                productSelectText.innerHTML = displayHtml;
                productSelectText.classList.add('has-value');
            } else {
                productSelectText.innerHTML = 'Pilih Produk';
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

        // Recover selected value from server redirect/validation failures
        const initialValue = productSelect.value;
        if (initialValue) {
            const initialOpt = optionsData.find(o => o.value === initialValue);
            if (initialOpt) {
                selectProduct(initialOpt.value, initialOpt.text);
            }
        }

        // Initialize option list
        renderOptions();
    });
</script>
@endsection
