@extends('layouts.app')
@section('title', 'Input Barang Keluar')
@section('page-title', 'Input Barang Keluar')

@section('content')
<style>
    /* Premium Form Styling */
    .page-header-back {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: #475569;
        transition: all 0.2s;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .page-header-back:hover {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08);
    }

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

    /* Info Banner */
    .info-banner {
        background: linear-gradient(135deg, #fff7ed 0%, #fef3c7 100%);
        border: 1px solid rgba(245, 158, 11, 0.2);
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.88rem;
        color: #92400e;
    }

    .info-banner i {
        font-size: 1.4rem;
        color: #d97706;
        flex-shrink: 0;
    }

    .info-banner strong {
        color: #78350f;
    }

    /* Form Cards */
    .form-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
    }

    .form-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-card-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .form-card-header i {
        font-size: 1.3rem;
        color: #dc2626;
    }

    .form-card-body {
        padding: 20px;
    }

    .form-label-custom {
        font-weight: 600;
        font-size: 0.85rem;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .form-label-custom .text-danger { font-size: 0.9rem; }

    .form-control:focus, .form-select:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08);
    }

    /* Product Info Card */
    .product-info-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid rgba(16, 185, 129, 0.15);
        border-radius: 10px;
        padding: 14px 18px;
        margin-top: 12px;
        display: none;
    }

    .product-info-card.visible {
        display: block;
        animation: fadeSlideIn 0.3s ease;
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .product-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        font-size: 0.85rem;
    }

    .product-info-label {
        color: #64748b;
        font-weight: 500;
    }

    .product-info-value {
        font-weight: 700;
        color: #065f46;
    }

    .product-info-value.text-danger {
        color: #dc2626 !important;
    }

    /* Submit Section */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 20px;
        margin-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .btn-cancel {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .btn-submit-outgoing {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        border: none;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-submit-outgoing:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        color: #ffffff;
    }

    .btn-submit-outgoing:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Jenis Keluar Option Cards */
    .jenis-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .jenis-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        background: #ffffff;
    }

    .jenis-option:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .jenis-option.selected {
        border-color: #dc2626;
        background: #fef2f2;
    }

    .jenis-option input[type="radio"] {
        display: none;
    }

    .jenis-option-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        flex-shrink: 0;
        position: relative;
        transition: all 0.2s;
    }

    .jenis-option.selected .jenis-option-dot {
        border-color: #dc2626;
    }

    .jenis-option.selected .jenis-option-dot::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 8px;
        height: 8px;
        background: #dc2626;
        border-radius: 50%;
        transform: translate(-50%, -50%);
    }

    .jenis-option-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .jenis-option-text {
        font-weight: 600;
        font-size: 0.85rem;
        color: #334155;
    }

    .jenis-option-desc {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 400;
    }

    .icon-penjualan { background: rgba(37, 99, 235, 0.08); color: #2563eb; }
    .icon-rusak { background: rgba(220, 38, 38, 0.08); color: #dc2626; }
    .icon-kedaluwarsa { background: rgba(245, 158, 11, 0.08); color: #d97706; }
    .icon-penyesuaian { background: rgba(100, 116, 139, 0.08); color: #64748b; }

    /* Character counter */
    .char-counter {
        font-size: 0.75rem;
        color: #94a3b8;
        text-align: right;
        margin-top: 4px;
    }

    /* Error alert */
    .error-alert {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .error-alert i {
        color: #dc2626;
        font-size: 1.2rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .error-alert ul {
        margin: 0;
        padding-left: 16px;
        font-size: 0.85rem;
        color: #991b1b;
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
        min-height: 42px;
        padding: 8px 14px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .searchable-select-trigger:hover {
        border-color: #dc2626;
    }
    .searchable-select.open .searchable-select-trigger {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
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
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
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
        background: #fef2f2;
        color: #b91c1c;
    }
    .searchable-select-options li.active {
        background: #dc2626;
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

<div class="container-fluid py-2">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('barang-keluar.index') }}" class="page-header-back me-3">
            <i class="ph ph-arrow-left" style="font-size: 1.2rem;"></i>
        </a>
        <div>
            <h1 class="page-title-main mb-1">Proses Barang Keluar</h1>
            <p class="page-subtitle mb-0">Catat pengeluaran barang dengan pengurangan stok otomatis metode FIFO.</p>
        </div>
    </div>



    {{-- Error Display --}}
    @if($errors->any())
    <div class="error-alert">
        <i class="ph ph-warning-circle-fill"></i>
        <div>
            <ul>
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ route('barang-keluar.store') }}" method="POST" id="formBarangKeluar">
        @csrf
        <div class="row g-4">
            {{-- Left Column - Form --}}
            <div class="col-lg-7">
                {{-- Card: Detail Transaksi --}}
                <div class="form-card mb-4">
                    <div class="form-card-header">
                        <i class="ph ph-clipboard-text"></i>
                        <h3>Detail Transaksi</h3>
                    </div>
                    <div class="form-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">
                                    <i class="ph ph-calendar-blank text-muted" style="font-size: 1rem;"></i>
                                    Tanggal Keluar <span class="text-danger">*</span>
                                <input type="date" name="tanggal_keluar" id="tanggal_keluar" 
                                    class="form-control @error('tanggal_keluar') is-invalid @enderror" 
                                    value="{{ old('tanggal_keluar', date('Y-m-d')) }}" required min="2026-01-01">
                                @error('tanggal_keluar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">
                                    <i class="ph ph-hash text-muted" style="font-size: 1rem;"></i>
                                    Jumlah (pcs) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="jumlah" id="jumlah" 
                                    class="form-control @error('jumlah') is-invalid @enderror" 
                                    value="{{ old('jumlah') }}" min="1" placeholder="0" required>
                                @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label-custom">
                                <i class="ph ph-cube text-muted" style="font-size: 1rem;"></i>
                                Pilih Produk <span class="text-danger">*</span>
                            </label>
                            {{-- Hidden native select --}}
                            <select name="product_id" id="product_id" style="display:none;" required>
                                <option value="">-- Pilih produk yang akan dikeluarkan --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" 
                                        data-stok="{{ $p->stok_saat_ini }}"
                                        data-kode="{{ $p->kode_produk }}"
                                        data-nama="{{ $p->nama_produk }}"
                                        {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->kode_produk }} — {{ $p->nama_produk }} (Stok: {{ number_format($p->stok_saat_ini) }})
                                    </option>
                                @endforeach
                            </select>
                            {{-- Custom searchable select dropdown --}}
                            <div class="searchable-select" id="searchableProductSelect">
                                <div class="searchable-select-trigger form-control" id="productSelectTrigger" style="border-radius: 8px; min-height: 42px;">
                                    <span class="searchable-select-text" id="productSelectText">-- Pilih produk yang akan dikeluarkan --</span>
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

                        {{-- Product Info Card (shows on product select) --}}
                        <div class="product-info-card" id="productInfoCard">
                            <div class="product-info-row">
                                <span class="product-info-label">Kode Produk</span>
                                <span class="product-info-value" id="infoKode">-</span>
                            </div>
                            <div class="product-info-row">
                                <span class="product-info-label">Nama Produk</span>
                                <span class="product-info-value" id="infoNama">-</span>
                            </div>
                            <div class="product-info-row" style="border-top: 1px dashed #d1fae5; padding-top: 8px; margin-top: 4px;">
                                <span class="product-info-label">Stok Tersedia</span>
                                <span class="product-info-value" id="infoStok" style="font-size: 1.1rem;">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Jenis & Keterangan --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <i class="ph ph-tag"></i>
                        <h3>Jenis & Keterangan</h3>
                    </div>
                    <div class="form-card-body">
                        <label class="form-label-custom mb-2">
                            <i class="ph ph-list-dashes text-muted" style="font-size: 1rem;"></i>
                            Jenis Keluar <span class="text-danger">*</span>
                        </label>
                        <div class="jenis-options mb-3">
                            <label class="jenis-option {{ old('jenis_keluar') == 'penjualan' ? 'selected' : '' }}" data-value="penjualan">
                                <input type="radio" name="jenis_keluar" value="penjualan" {{ old('jenis_keluar') == 'penjualan' ? 'checked' : '' }} required>
                                <span class="jenis-option-dot"></span>
                                <span class="jenis-option-icon icon-penjualan"><i class="ph ph-shopping-cart"></i></span>
                                <div>
                                    <div class="jenis-option-text">Penjualan</div>
                                    <div class="jenis-option-desc">Produk terjual</div>
                                </div>
                            </label>
                            <label class="jenis-option {{ old('jenis_keluar') == 'rusak' ? 'selected' : '' }}" data-value="rusak">
                                <input type="radio" name="jenis_keluar" value="rusak" {{ old('jenis_keluar') == 'rusak' ? 'checked' : '' }}>
                                <span class="jenis-option-dot"></span>
                                <span class="jenis-option-icon icon-rusak"><i class="ph ph-x-circle"></i></span>
                                <div>
                                    <div class="jenis-option-text">Rusak</div>
                                    <div class="jenis-option-desc">Produk defect</div>
                                </div>
                            </label>
                            <label class="jenis-option {{ old('jenis_keluar') == 'kedaluwarsa' ? 'selected' : '' }}" data-value="kedaluwarsa">
                                <input type="radio" name="jenis_keluar" value="kedaluwarsa" {{ old('jenis_keluar') == 'kedaluwarsa' ? 'checked' : '' }}>
                                <span class="jenis-option-dot"></span>
                                <span class="jenis-option-icon icon-kedaluwarsa"><i class="ph ph-clock-countdown"></i></span>
                                <div>
                                    <div class="jenis-option-text">Kedaluwarsa</div>
                                    <div class="jenis-option-desc">Produk expired</div>
                                </div>
                            </label>
                        </div>
                        @error('jenis_keluar') <div class="text-danger" style="font-size: 0.82rem; margin-top: -8px; margin-bottom: 12px;">{{ $message }}</div> @enderror

                        <input type="hidden" name="keterangan" id="keterangan" value="">
                    </div>
                </div>
            </div>

            {{-- Right Column - Summary --}}
            <div class="col-lg-5">
                <div class="form-card" style="position: sticky; top: 24px;">
                    <div class="form-card-header">
                        <i class="ph ph-receipt"></i>
                        <h3>Ringkasan Transaksi</h3>
                    </div>
                    <div class="form-card-body">
                        <div class="product-info-row mb-2">
                            <span class="product-info-label">Tanggal Keluar</span>
                            <span class="product-info-value" id="summaryDate" style="color: #334155;">{{ date('d/m/Y') }}</span>
                        </div>
                        <div class="product-info-row mb-2">
                            <span class="product-info-label">Produk</span>
                            <span class="product-info-value" id="summaryProduct" style="color: #334155; text-align: right; max-width: 60%;">Belum dipilih</span>
                        </div>
                        <div class="product-info-row mb-2">
                            <span class="product-info-label">Stok Tersedia</span>
                            <span class="product-info-value" id="summaryStok" style="color: #10b981;">-</span>
                        </div>
                        <div class="product-info-row mb-2">
                            <span class="product-info-label">Qty Keluar</span>
                            <span class="product-info-value text-danger" id="summaryQty" style="font-size: 1.3rem;">0</span>
                        </div>
                        <div class="product-info-row mb-2">
                            <span class="product-info-label">Jenis Keluar</span>
                            <span class="product-info-value" id="summaryJenis" style="color: #64748b;">Belum dipilih</span>
                        </div>
                        <hr style="border-color: #e2e8f0; margin: 16px 0;">
                        <div class="product-info-row">
                            <span class="product-info-label">Sisa Stok (estimasi)</span>
                            <span class="product-info-value" id="summaryRemaining" style="font-size: 1.2rem;">-</span>
                        </div>

                        {{-- Stok Warning --}}
                        <div class="info-banner mt-3" id="stokWarning" style="display: none; padding: 10px 14px; margin-bottom: 0;">
                            <i class="ph ph-warning-fill" style="font-size: 1.1rem;"></i>
                            <span id="stokWarningText" style="font-size: 0.82rem;"></span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9; background: #fafbfc;">
                        <div class="d-flex gap-2">
                            <a href="{{ route('barang-keluar.index') }}" class="btn-cancel flex-fill text-center" style="text-decoration: none;">
                                <i class="ph ph-x"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit-outgoing flex-fill" id="btnSubmit">
                                <i class="ph ph-check-fat"></i> Proses Barang Keluar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const jumlahInput = document.getElementById('jumlah');
    const tanggalInput = document.getElementById('tanggal_keluar');
    const keteranganInput = document.getElementById('keterangan');
    const charCount = document.getElementById('charCount');
    const productInfoCard = document.getElementById('productInfoCard');
    const stokWarning = document.getElementById('stokWarning');
    const stokWarningText = document.getElementById('stokWarningText');

    // ── Custom Searchable Select Dropdown Logic ──
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
                nama: opt.getAttribute('data-nama') || '',
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
                
                li.innerHTML = `
                    <span class="opt-code">${opt.code}</span>
                    <span class="opt-name">${opt.nama} (Stok: ${parseInt(opt.stok).toLocaleString('id-ID')})</span>
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
            const displayHtml = optData ? `<span class="opt-code me-2">${optData.code}</span> <span class="fw-semibold text-dark">${optData.nama} (Stok: ${parseInt(optData.stok).toLocaleString('id-ID')})</span>` : text;
            
            productSelectText.innerHTML = displayHtml;
            productSelectText.classList.add('has-value');
        } else {
            productSelectText.innerHTML = '-- Pilih produk yang akan dikeluarkan --';
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

    let selectedStok = 0;

    // Jenis Keluar radio card interaction
    document.querySelectorAll('.jenis-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.jenis-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            updateSummary();
        });
    });

    // Product select change
    productSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            selectedStok = parseInt(selected.dataset.stok) || 0;
            document.getElementById('infoKode').textContent = selected.dataset.kode || '-';
            document.getElementById('infoNama').textContent = selected.dataset.nama || '-';
            document.getElementById('infoStok').textContent = selectedStok.toLocaleString('id-ID') + ' pcs';
            productInfoCard.classList.add('visible');
        } else {
            selectedStok = 0;
            productInfoCard.classList.remove('visible');
        }
        updateSummary();
    });

    // Jumlah input
    jumlahInput.addEventListener('input', updateSummary);

    // Tanggal input
    tanggalInput.addEventListener('change', updateSummary);

    // Character counter
    if (keteranganInput && charCount) {
        keteranganInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        charCount.textContent = keteranganInput.value.length;
    }

    function updateSummary() {
        // Date
        const tgl = tanggalInput.value;
        if (tgl) {
            const d = new Date(tgl);
            document.getElementById('summaryDate').textContent = 
                d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        // Product
        const selOpt = productSelect.options[productSelect.selectedIndex];
        if (productSelect.value) {
            document.getElementById('summaryProduct').textContent = selOpt.dataset.nama || '-';
            document.getElementById('summaryStok').textContent = selectedStok.toLocaleString('id-ID') + ' pcs';
        } else {
            document.getElementById('summaryProduct').textContent = 'Belum dipilih';
            document.getElementById('summaryStok').textContent = '-';
        }

        // Qty
        const qty = parseInt(jumlahInput.value) || 0;
        document.getElementById('summaryQty').textContent = qty > 0 ? '-' + qty.toLocaleString('id-ID') : '0';

        // Jenis
        const jenisChecked = document.querySelector('input[name="jenis_keluar"]:checked');
        document.getElementById('summaryJenis').textContent = jenisChecked 
            ? jenisChecked.value.charAt(0).toUpperCase() + jenisChecked.value.slice(1) 
            : 'Belum dipilih';

        // Remaining
        if (productSelect.value && qty > 0) {
            const remaining = selectedStok - qty;
            const remainingEl = document.getElementById('summaryRemaining');
            remainingEl.textContent = remaining.toLocaleString('id-ID') + ' pcs';
            
            if (remaining < 0) {
                remainingEl.style.color = '#dc2626';
                stokWarning.style.display = 'flex';
                stokWarningText.textContent = 'Jumlah keluar melebihi stok tersedia!';
                stokWarning.style.background = 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)';
                stokWarning.style.borderColor = 'rgba(220, 38, 38, 0.2)';
                stokWarning.querySelector('i').style.color = '#dc2626';
                stokWarningText.style.color = '#991b1b';
            } else if (remaining <= 10 && remaining >= 0) {
                remainingEl.style.color = '#d97706';
                stokWarning.style.display = 'flex';
                stokWarningText.textContent = 'Stok akan hampir habis setelah transaksi ini.';
                stokWarning.style.background = 'linear-gradient(135deg, #fff7ed 0%, #fef3c7 100%)';
                stokWarning.style.borderColor = 'rgba(245, 158, 11, 0.2)';
                stokWarning.querySelector('i').style.color = '#d97706';
                stokWarningText.style.color = '#92400e';
            } else {
                remainingEl.style.color = '#10b981';
                stokWarning.style.display = 'none';
            }
        } else {
            document.getElementById('summaryRemaining').textContent = '-';
            document.getElementById('summaryRemaining').style.color = '#334155';
            stokWarning.style.display = 'none';
        }
    }

    // Initial update if old values exist
    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
    updateSummary();
});
</script>
@endsection
