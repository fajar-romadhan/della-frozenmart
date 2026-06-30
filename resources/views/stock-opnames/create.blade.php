@extends('layouts.app')
@section('title', 'Input Stok Opname')
@section('page-title', 'Input Stok Opname')

@section('content')
<style>
    /* Premium Opname Wizard */
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

    .btn-history-opname {
        background: #ffffff;
        color: #2563eb;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #bfdbfe;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-history-opname:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1d4ed8;
    }

    /* Stepper Container */
    .stepper-container {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        padding: 24px 32px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .step-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.88rem;
        flex-shrink: 0;
        transition: all 0.3s;
    }

    .step-circle.completed {
        background: #10b981;
        color: #ffffff;
    }

    .step-circle.active {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }

    .step-circle.pending {
        background: #ffffff;
        color: #94a3b8;
        border: 2px solid #e2e8f0;
    }

    .step-text {
        display: flex;
        flex-direction: column;
    }

    .step-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
    }

    .step-sublabel {
        font-size: 0.73rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .step-sublabel.completed { color: #10b981; }
    .step-sublabel.active { color: #2563eb; }

    .step-line {
        width: 60px;
        height: 2px;
        border-radius: 1px;
        margin: 0 12px;
        flex-shrink: 0;
    }

    .step-line.completed { background: #10b981; }
    .step-line.active { background: #2563eb; }
    .step-line.pending { background: #e2e8f0; }

    /* Stat Cards Row */
    .opname-stats {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .opname-stat-chip {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 10px;
        padding: 12px 16px;
        flex: 1;
        min-width: 150px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .opname-stat-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
    }

    .opname-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .opname-stat-icon i { font-size: 1.3rem; }

    .opname-stat-text {
        display: flex;
        flex-direction: column;
    }

    .opname-stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }

    .opname-stat-value {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* Form Panels */
    .form-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
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

    /* Filters block */
    .opname-filter-row {
        display: flex;
        gap: 16px;
        align-items: flex-end;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }

    .opname-filter-row .filter-group {
        display: flex;
        flex-direction: column;
    }

    /* Table Styles */
    .opname-input-table {
        margin: 0;
        width: 100%;
    }

    .opname-input-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #ffffff !important;
        background-color: #1e293b !important;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 8px;
        text-align: left;
        white-space: normal !important;
    }

    .opname-input-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .opname-input-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .opname-input-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .opname-input-table th.text-center,
    .opname-input-table td.text-center {
        text-align: center !important;
    }

    /* Inputs inside table */
    .stok-fisik-input {
        width: 90px;
        text-align: center;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .stok-fisik-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }

    .stok-fisik-input.changed {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .ket-input {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 0.8rem;
        transition: all 0.2s;
        color: #475569;
        background: #ffffff;
    }

    .ket-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }

    /* Status Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.73rem;
        font-weight: 600;
    }

    .badge-sesuai { background: rgba(16, 185, 129, 0.08); color: #059669; }
    .badge-kurang { background: rgba(220, 38, 38, 0.08); color: #dc2626; }
    .badge-lebih { background: rgba(37, 99, 235, 0.08); color: #2563eb; }
    .badge-pending { background: rgba(100, 116, 139, 0.06); color: #94a3b8; }

    /* Footer Styles */
    .form-footer {
        padding: 20px 24px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-cancel {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .btn-wizard-next {
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
        background: #2563eb;
        border: none;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-wizard-next:hover {
        background: #1d4ed8;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-wizard-prev {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-wizard-prev:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    /* Col summary boxes inside step 3 table card */
    .selisih-summary-row {
        display: flex;
        gap: 16px;
        padding: 20px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .selisih-summary-box {
        flex: 1;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .selisih-summary-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
    }

    .selisih-summary-value {
        font-size: 1.1rem;
        font-weight: 800;
    }

    /* Confirm Panel Column Details */
    .confirm-info-panel {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .confirm-info-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        padding: 20px;
    }

    .confirm-info-card h4 {
        font-size: 0.92rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 8px;
    }

    .confirm-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.82rem;
    }

    .confirm-info-label {
        color: #64748b;
        font-weight: 500;
    }

    .confirm-info-val {
        font-weight: 700;
        color: #1e293b;
    }

    /* Confirm layout split */
    .confirm-split-layout {
        display: grid;
        grid-template-columns: 3fr 1fr;
        gap: 20px;
    }

    /* Penyesuaian labels */
    .badge-penyesuaian {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-penyesuaian-kurang { background: rgba(220, 38, 38, 0.08); color: #dc2626; }
    .badge-penyesuaian-tambah { background: rgba(16, 185, 129, 0.08); color: #059669; }

    /* Timeline widget */
    .timeline-widget {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        padding: 20px;
        height: fit-content;
    }

    .timeline-widget h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 16px;
    }

    .timeline-steps {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .timeline-step {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .timeline-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.8rem;
    }

    .timeline-content {
        display: flex;
        flex-direction: column;
    }

    .timeline-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .timeline-desc {
        font-size: 0.72rem;
        color: #64748b;
        line-height: 1.3;
    }

    /* Warning alerts */
    .warning-banner {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.8rem;
        color: #b45309;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
    }

    .info-box-widget {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 0.76rem;
        margin-top: 14px;
        line-height: 1.4;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title-main mb-1" style="font-size: 1.8rem; font-weight: 800;">Stok Opname</h1>
            <p class="page-subtitle mb-0">Kelola pencocokan stok fisik dengan stok pada sistem.</p>
        </div>
        <a href="{{ route('stok-opname.index') }}" class="btn-history-opname" id="btnHistoryOpname">
            <i class="ph ph-clock" style="font-size: 1.2rem;"></i> Riwayat Stok Opname
        </a>
    </div>

    {{-- Stepper (Wizard) --}}
    <div class="stepper-container">
        <div class="step-item">
            <div class="step-circle completed" id="circle-1"><i class="ph ph-check-bold" style="font-size: 1rem;"></i></div>
            <div class="step-text">
                <span class="step-label">1 &nbsp; Pilih Tanggal</span>
                <span class="step-sublabel completed" id="sublabel-1">Selesai</span>
            </div>
        </div>
        <div class="step-line completed" id="line-1"></div>
        <div class="step-item">
            <div class="step-circle active" id="circle-2">2</div>
            <div class="step-text">
                <span class="step-label">Hitung Stok Fisik</span>
                <span class="step-sublabel active" id="sublabel-2">Aktif</span>
            </div>
        </div>
        <div class="step-line pending" id="line-2"></div>
        <div class="step-item">
            <div class="step-circle pending" id="circle-3">3</div>
            <div class="step-text">
                <span class="step-label">Review Selisih</span>
                <span class="step-sublabel" id="sublabel-3">Belum Aktif</span>
            </div>
        </div>
        <div class="step-line pending" id="line-3"></div>
        <div class="step-item">
            <div class="step-circle pending" id="circle-4">4</div>
            <div class="step-text">
                <span class="step-label">Simpan Penyesuaian</span>
                <span class="step-sublabel" id="sublabel-4">Belum Aktif</span>
            </div>
        </div>
    </div>

    {{-- Stats Cards Row --}}
    <div class="opname-stats">
        {{-- Card 1: Tanggal --}}
        <div class="opname-stat-chip">
            <div class="opname-stat-icon" style="background: rgba(37, 99, 235, 0.08);">
                <i class="ph ph-calendar-blank text-primary"></i>
            </div>
            <div class="opname-stat-text">
                <span class="opname-stat-label">Tanggal Opname</span>
                <span class="opname-stat-value" id="statDate">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>
        {{-- Card 2: Lokasi (Hidden in Step 2, Shown in Step 3 & 4) --}}
        <div class="opname-stat-chip d-none" id="statCardLokasi">
            <div class="opname-stat-icon" style="background: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                <i class="ph ph-map-pin"></i>
            </div>
            <div class="opname-stat-text">
                <span class="opname-stat-label">Lokasi</span>
                <span class="opname-stat-value" id="statLokasi" style="font-size: 0.95rem;">FRZ-01 <br><small class="text-muted fw-normal" style="font-size: 0.72rem;">Freezer 1</small></span>
            </div>
        </div>
        {{-- Card 3: Total Produk --}}
        <div class="opname-stat-chip">
            <div class="opname-stat-icon" style="background: rgba(245, 158, 11, 0.08); color: #ea580c;">
                <i class="ph ph-package text-warning"></i>
            </div>
            <div class="opname-stat-text">
                <span class="opname-stat-label">Total Produk</span>
                <span class="opname-stat-value" id="statTotal">{{ $products->count() }}</span>
            </div>
        </div>
        {{-- Card 4: Sesuai --}}
        <div class="opname-stat-chip">
            <div class="opname-stat-icon" style="background: rgba(16, 185, 129, 0.08);">
                <i class="ph ph-check-circle text-success"></i>
            </div>
            <div class="opname-stat-text">
                <span class="opname-stat-label">Sesuai</span>
                <div class="d-flex align-items-baseline gap-1">
                    <span class="opname-stat-value" id="statSesuai">0</span>
                    <span class="text-muted" id="statSesuaiPct" style="font-size: 0.72rem; font-weight: 600;">(0%)</span>
                </div>
            </div>
        </div>
        {{-- Card 5: Selisih --}}
        <div class="opname-stat-chip">
            <div class="opname-stat-icon" style="background: rgba(245, 158, 11, 0.08);">
                <i class="ph ph-warning text-warning"></i>
            </div>
            <div class="opname-stat-text">
                <span class="opname-stat-label">Selisih</span>
                <div class="d-flex align-items-baseline gap-1">
                    <span class="opname-stat-value" id="statSelisih">0</span>
                    <span class="text-muted" id="statSelisihPct" style="font-size: 0.72rem; font-weight: 600;">(0%)</span>
                </div>
            </div>
        </div>
        {{-- Card 6: Total Selisih --}}
        <div class="opname-stat-chip">
            <div class="opname-stat-icon" style="background: rgba(220, 38, 38, 0.08);">
                <i class="ph ph-minus-circle text-danger"></i>
            </div>
            <div class="opname-stat-text">
                <span class="opname-stat-label">Total Selisih</span>
                <span class="opname-stat-value" id="statTotalSelisih">0 pcs</span>
            </div>
        </div>
    </div>

    {{-- Error Display --}}
    @if($errors->any())
    <div class="error-alert alert alert-danger py-2 px-3 mb-3 d-flex align-items-start gap-2" style="border-radius: 10px;">
        <i class="ph ph-warning-circle-fill text-danger fs-5" style="margin-top: 2px;"></i>
        <div>
            <ul class="mb-0 ps-3 fs-6">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('stok-opname.store') }}" method="POST" id="formOpname">
        @csrf
        <input type="hidden" name="tanggal_opname" id="tanggalOpnameHidden" value="{{ date('Y-m-d') }}">

        <!-- ========================================== -->
        <!-- WIZARD STEP 2: HITUNG STOK FISIK           -->
        <!-- ========================================== -->
        <div id="step-hitung-container">
            <div class="info-banner" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid rgba(37, 99, 235, 0.15); color: #1e40af; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px;">
                <i class="ph ph-lightbulb-filament text-primary" style="font-size: 1.4rem;"></i>
                <div>
                    <strong>Petunjuk:</strong> Masukkan jumlah stok fisik yang Anda hitung secara nyata pada kolom "Stok Fisik". Sistem akan otomatis menghitung selisih dan menentukan status. Klik "Selanjutnya: Review Selisih" untuk melanjutkan.
                </div>
            </div>

            <div class="form-card">
                {{-- Filter bar --}}
                <div class="opname-filter-row">
                    <div class="filter-group" style="width: 25%;">
                        <label class="form-label-custom">Tanggal Opname</label>
                        <input type="date" class="form-control" id="tanggalOpname" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="filter-group" style="width: 25%;">
                        <label class="form-label-custom">ID Lokasi</label>
                        <select class="form-select" id="idLokasi" name="id_lokasi">
                            <option value="FRZ-01" selected>FRZ-01 (Freezer 1)</option>
                            <option value="FRZ-02">FRZ-02 (Freezer 2)</option>
                            <option value="FRZ-03">FRZ-03 (Freezer 3)</option>
                            <option value="RAK-A">RAK-A (Rak A)</option>
                            <option value="RAK-B">RAK-B (Rak B)</option>
                            <option value="RAK-C">RAK-C (Rak C)</option>
                            <option value="RAK-D">RAK-D (Rak D)</option>
                        </select>
                    </div>
                    <div class="filter-group" style="flex: 1;">
                        <label class="form-label-custom">Nama Produk</label>
                        <div class="search-input-wrapper">
                            <input type="text" class="form-control" id="searchProduct" placeholder="Cari berdasarkan nama atau kode produk...">
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table align-middle opname-input-table" id="tableOpname">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">NO</th>
                                <th>KODE PRODUK</th>
                                <th>NAMA PRODUK</th>
                                <th class="text-center">STOK SISTEM</th>
                                <th class="text-center">STOK FISIK <i class="ph ph-info text-white" style="font-size: 0.92rem; vertical-align: middle;" title="Jumlah perhitungan fisik"></i></th>
                                <th class="text-center">SELISIH</th>
                                <th class="text-center">STATUS</th>
                                <th>KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody id="opnameTableBody">
                            @foreach($products as $idx => $product)
                            <tr class="opname-row" data-product-id="{{ $product->id }}" data-kode="{{ strtolower($product->kode_produk) }}" data-nama="{{ strtolower($product->nama_produk) }}" data-stok="{{ $product->stok_saat_ini }}">
                                <td class="text-center text-muted row-number">{{ $idx + 1 }}</td>
                                <td class="fw-semibold text-muted">{{ $product->kode_produk }}</td>
                                <td class="fw-bold">{{ $product->nama_produk }}</td>
                                <td class="text-center">{{ number_format($product->stok_saat_ini) }}</td>
                                <td class="text-center">
                                    <input type="number" 
                                        class="stok-fisik-input" 
                                        name="items[{{ $product->id }}][stok_fisik]"
                                        data-product-id="{{ $product->id }}"
                                        data-stok-sistem="{{ $product->stok_saat_ini }}"
                                        min="0" 
                                        placeholder="—"
                                        value="">
                                    <input type="hidden" name="items[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                                </td>
                                <td class="text-center selisih-cell">
                                    <span class="text-muted">—</span>
                                </td>
                                <td class="text-center status-cell">
                                    <span class="badge-status badge-pending">
                                        <i class="ph ph-minus"></i> —
                                    </span>
                                </td>
                                <td>
                                    <input type="text" 
                                        class="ket-input" 
                                        name="items[{{ $product->id }}][keterangan]"
                                        placeholder="Opsional..."
                                        value="">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="form-footer">
                    <a href="{{ route('stok-opname.index') }}" class="btn-cancel">
                        Batal
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn-wizard-next" id="btnSimpanLangsung" style="background: #10b981; gap: 6px;">
                            <i class="ph ph-floppy-disk" style="font-size: 1.1rem; vertical-align: middle;"></i> Simpan Langsung
                        </button>
                        <button type="button" class="btn-wizard-next" id="btnGoToStep3">
                            Selanjutnya: Review Selisih <i class="ph ph-arrow-right" style="font-size: 1.1rem;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- WIZARD STEP 3: REVIEW SELISIH              -->
        <!-- ========================================== -->
        <div id="step-review-container" style="display: none;">
            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 8px; font-size: 0.85rem;">
                <i class="ph ph-info-fill text-primary" style="font-size: 1.2rem;"></i>
                <span>Berikut adalah daftar produk yang memiliki selisih stok. Pastikan keterangan sudah sesuai sebelum menyimpan penyesuaian.</span>
            </div>

            <div class="form-card">
                <div class="opname-filter-row" style="justify-content: space-between; align-items: center; border-bottom: none;">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 0.98rem;"><i class="ph ph-list-bullets text-primary"></i> Daftar Selisih Stok</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm border" style="font-size: 0.8rem; font-weight: 600;"><i class="ph ph-funnel"></i> Filter Status</button>
                        <button type="button" class="btn btn-light btn-sm border" style="font-size: 0.8rem; font-weight: 600;"><i class="ph ph-export"></i> Export</button>
                    </div>
                </div>

                {{-- Table (Review) --}}
                <div class="table-responsive">
                    <table class="table align-middle opname-input-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">NO</th>
                                <th>KODE PRODUK</th>
                                <th>NAMA PRODUK</th>
                                <th class="text-center">STOK SISTEM</th>
                                <th class="text-center">STOK FISIK</th>
                                <th class="text-center">SELISIH</th>
                                <th class="text-center">STATUS</th>
                                <th>KETERANGAN</th>
                                <th class="text-center" style="width: 60px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="reviewTableBody">
                            <!-- Populated via Javascript -->
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer Summary boxes --}}
                <div class="selisih-summary-row">
                    <div class="selisih-summary-box" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;">
                        <span class="selisih-summary-label">Total Selisih (Kurang)</span>
                        <span class="selisih-summary-value text-danger" id="summaryTotalKurang">-0 pcs</span>
                    </div>
                    <div class="selisih-summary-box" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;">
                        <span class="selisih-summary-label">Total Selisih (Lebih)</span>
                        <span class="selisih-summary-value text-success" id="summaryTotalLebih">+0 pcs</span>
                    </div>
                    <div class="selisih-summary-box" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                        <span class="selisih-summary-label">Total Selisih Keseluruhan</span>
                        <span class="selisih-summary-value text-primary" id="summaryTotalAll">0 pcs</span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="form-footer">
                    <button type="button" class="btn-wizard-prev" id="btnBackToStep2">
                        <i class="ph ph-arrow-left"></i> Kembali ke Hitung Stok Fisik
                    </button>
                    <button type="button" class="btn-wizard-next" id="btnGoToStep4">
                        Selanjutnya: Simpan Penyesuaian <i class="ph ph-arrow-right" style="font-size: 1.1rem;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- WIZARD STEP 4: SIMPAN PENYESUAIAN          -->
        <!-- ========================================== -->
        <div id="step-simpan-container" style="display: none;">
            {{-- Info Banner --}}
            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 8px; font-size: 0.85rem;">
                <i class="ph ph-info-fill text-primary" style="font-size: 1.2rem;"></i>
                <span>Pastikan data selisih sudah benar sebelum menyimpan penyesuaian. Setelah disimpan, stok sistem akan diperbarui dan riwayat opname akan tercatat.</span>
            </div>

            {{-- Top Info Grid --}}
            <div class="confirm-info-panel">
                {{-- Card Left: Informasi Opname --}}
                <div class="confirm-info-card">
                    <h4><i class="ph ph-clipboard-text text-primary"></i> Informasi Opname</h4>
                    <div class="confirm-info-row">
                        <span class="confirm-info-label">Tanggal Opname</span>
                        <span class="confirm-info-val" id="confirmTanggal">14 Mei 2025</span>
                    </div>
                    <div class="confirm-info-row">
                        <span class="confirm-info-label">Lokasi</span>
                        <span class="confirm-info-val" id="confirmLokasi">FRZ-01 - Freezer 1</span>
                    </div>
                    <div class="confirm-info-row">
                        <span class="confirm-info-label">Nama Produk</span>
                        <span class="confirm-info-val" id="confirmNamaProduk">Nugget Ayam 500g</span>
                    </div>
                    <div class="confirm-info-row">
                        <span class="confirm-info-label">Waktu Opname</span>
                        <span class="confirm-info-val" id="confirmWaktu">14/05/2025 09:30 WIB</span>
                    </div>
                </div>

                {{-- Card Right: Ringkasan Selisih --}}
                <div class="confirm-info-card" style="display: flex; flex-direction: column; justify-content: center; gap: 10px;">
                    <h4 style="margin-bottom: 4px; border-bottom: none;"><i class="ph ph-chart-pie-slice text-primary"></i> Ringkasan Selisih</h4>
                    <div class="d-flex gap-2">
                        <div class="text-center p-2 rounded flex-fill" style="background: #fef2f2; border: 1px solid #fecaca;">
                            <span class="d-block text-muted" style="font-size: 0.72rem; font-weight: 600;">Total Selisih (Kurang)</span>
                            <span class="fw-extrabold text-danger" id="confirmTotalKurang" style="font-size: 1.15rem; font-weight: 800;">-0 pcs</span>
                        </div>
                        <div class="text-center p-2 rounded flex-fill" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <span class="d-block text-muted" style="font-size: 0.72rem; font-weight: 600;">Total Selisih (Lebih)</span>
                            <span class="fw-extrabold text-success" id="confirmTotalLebih" style="font-size: 1.15rem; font-weight: 800;">+0 pcs</span>
                        </div>
                        <div class="text-center p-2 rounded flex-fill" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                            <span class="d-block text-muted" style="font-size: 0.72rem; font-weight: 600;">Total Selisih Keseluruhan</span>
                            <span class="fw-extrabold text-primary" id="confirmTotalAll" style="font-size: 1.15rem; font-weight: 800;">0 pcs</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Split layout: Table vs Widget --}}
            <div class="confirm-split-layout">
                {{-- Split Left: Table --}}
                <div>
                    <div class="form-card">
                        <div class="opname-filter-row" style="border-bottom: none;">
                            <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 0.98rem;"><i class="ph ph-database text-primary"></i> Data Penyesuaian Stok</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle opname-input-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">NO</th>
                                        <th>KODE PRODUK</th>
                                        <th>NAMA PRODUK</th>
                                        <th class="text-center">STOK SISTEM</th>
                                        <th class="text-center">STOK FISIK</th>
                                        <th class="text-center">SELISIH</th>
                                        <th class="text-center">PENYESUAIAN STOK</th>
                                        <th class="text-center">AKIBAT PENYESUAIAN</th>
                                        <th>KETERANGAN</th>
                                    </tr>
                                </thead>
                                <tbody id="confirmTableBody">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="warning-banner">
                        <i class="ph ph-warning-fill" style="font-size: 1.2rem;"></i>
                        <span>Penyesuaian stok akan langsung memperbarui jumlah stok produk pada sistem.</span>
                    </div>
                </div>

                {{-- Split Right: Process timeline --}}
                <div>
                    <div class="timeline-widget">
                        <h4>Proses Setelah Disimpan</h4>
                        <div class="timeline-steps">
                            <div class="timeline-step">
                                <div class="timeline-icon"><i class="ph ph-check-bold"></i></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">Hitung Selisih</span>
                                    <span class="timeline-desc">Sistem menghitung selisih antara stok sistem dan stok fisik.</span>
                                </div>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-icon"><i class="ph ph-check-bold"></i></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">Perbarui Stok</span>
                                    <span class="timeline-desc">Sistem menyesuaikan stok produk sesuai hasil opname.</span>
                                </div>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-icon"><i class="ph ph-check-bold"></i></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">Simpan Riwayat</span>
                                    <span class="timeline-desc">Riwayat stok opname akan disimpan untuk audit.</span>
                                </div>
                            </div>
                            <div class="timeline-step">
                                <div class="timeline-icon"><i class="ph ph-check-bold"></i></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">Notifikasi Berhasil</span>
                                    <span class="timeline-desc">Sistem menampilkan notifikasi jika penyesuaian berhasil.</span>
                                </div>
                            </div>
                        </div>
                        <div class="info-box-widget">
                            <i class="ph ph-info-fill" style="color: #2563eb;"></i> Data riwayat dapat dilihat pada menu <strong>Riwayat Stok Opname</strong>.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="form-footer" style="margin-top: 24px; border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);">
                <button type="button" class="btn-wizard-prev" id="btnBackToStep3">
                    <i class="ph ph-arrow-left"></i> Kembali ke Review Selisih
                </button>
                <div class="d-flex gap-2">
                    <a href="{{ route('stok-opname.index') }}" class="btn-cancel">
                        Batal
                    </a>
                    <button type="submit" class="btn-wizard-next" id="btnSubmitOpname" style="background: #2563eb;">
                        <i class="ph ph-floppy-disk" style="font-size: 1.15rem;"></i> Simpan Penyesuaian
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.opname-row');
    const searchInput = document.getElementById('searchProduct');
    const filterView = document.getElementById('filterView');
    
    const tanggalInputs = document.querySelectorAll('input[type="date"]');
    const tanggalHidden = document.getElementById('tanggalOpnameHidden');
    const idLokasiSelect = document.getElementById('idLokasi');

    // Sync tanggal inputs
    tanggalInputs.forEach(input => {
        input.addEventListener('change', function() {
            tanggalHidden.value = this.value;
            tanggalInputs.forEach(i => i.value = this.value);
            if (this.value) {
                const d = new Date(this.value);
                const formatted = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                document.getElementById('statDate').textContent = formatted;
                document.getElementById('confirmTanggal').textContent = formatted;
                
                // Format confirm tanggal/waktu
                const datePart = d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
                document.getElementById('confirmWaktu').textContent = datePart + ' ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            }
        });
    });

    // Update location text
    idLokasiSelect.addEventListener('change', function() {
        const text = this.options[this.selectedIndex].text;
        const val = this.value;
        document.getElementById('statLokasi').innerHTML = val + ' <br><small class="text-muted fw-normal" style="font-size: 0.72rem;">' + text.substring(text.indexOf('(') + 1, text.indexOf(')')) + '</small>';
        document.getElementById('confirmLokasi').textContent = val + ' - ' + text.substring(text.indexOf('(') + 1, text.indexOf(')'));
    });

    // Stok fisik input handler
    document.querySelectorAll('.stok-fisik-input').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            const stokSistem = parseInt(this.dataset.stokSistem) || 0;
            const stokFisik = this.value !== '' ? parseInt(this.value) : null;
            const selisihCell = row.querySelector('.selisih-cell');
            const statusCell = row.querySelector('.status-cell');

            if (stokFisik !== null && !isNaN(stokFisik)) {
                const selisih = stokFisik - stokSistem;
                this.classList.add('changed');

                // Selisih display
                if (selisih > 0) {
                    selisihCell.innerHTML = '<span class="text-primary fw-bold">+' + selisih + '</span>';
                } else if (selisih < 0) {
                    selisihCell.innerHTML = '<span class="text-danger fw-bold">' + selisih + '</span>';
                } else {
                    selisihCell.innerHTML = '<span class="text-muted">0</span>';
                }

                // Status display
                if (selisih === 0) {
                    statusCell.innerHTML = '<span class="badge-status badge-sesuai"><i class="ph ph-check-circle"></i> Sesuai</span>';
                } else if (selisih < 0) {
                    statusCell.innerHTML = '<span class="badge-status badge-kurang"><i class="ph ph-arrow-down"></i> Kurang</span>';
                } else {
                    statusCell.innerHTML = '<span class="badge-status badge-lebih"><i class="ph ph-arrow-up"></i> Lebih</span>';
                }
            } else {
                this.classList.remove('changed');
                selisihCell.innerHTML = '<span class="text-muted">—</span>';
                statusCell.innerHTML = '<span class="badge-status badge-pending"><i class="ph ph-minus"></i> —</span>';
            }

            updateStats();
        });
    });

    function updateStats() {
        let sesuai = 0, selisih = 0, totalSelisih = 0, filled = 0;
        let totalProduk = rows.length;

        document.querySelectorAll('.stok-fisik-input').forEach(input => {
            if (input.value !== '' && !isNaN(parseInt(input.value))) {
                filled++;
                const stokSistem = parseInt(input.dataset.stokSistem) || 0;
                const stokFisik = parseInt(input.value);
                const diff = stokFisik - stokSistem;
                if (diff === 0) {
                    sesuai++;
                } else {
                    selisih++;
                }
                totalSelisih += diff;
            }
        });

        // Percentages
        const sesuaiPct = totalProduk > 0 ? Math.round((sesuai / totalProduk) * 100) : 0;
        const selisihPct = totalProduk > 0 ? Math.round((selisih / totalProduk) * 100) : 0;

        const prefix = totalSelisih > 0 ? '+' : '';
        document.getElementById('statSesuai').textContent = sesuai;
        document.getElementById('statSesuaiPct').textContent = '(' + sesuaiPct + '%)';
        document.getElementById('statSelisih').textContent = selisih;
        document.getElementById('statSelisihPct').textContent = '(' + selisihPct + '%)';
        document.getElementById('statTotalSelisih').textContent = prefix + totalSelisih + ' pcs';
        document.getElementById('filledCount').textContent = filled;
    }

    // Search filter
    searchInput.addEventListener('input', applyFilters);

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(row => {
            const kode = row.dataset.kode;
            const nama = row.dataset.nama;

            let matchSearch = true;
            if (query) {
                matchSearch = kode.includes(query) || nama.includes(query);
            }

            if (matchSearch) {
                row.style.display = '';
                visibleCount++;
                row.querySelector('.row-number').textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });
    }

    // ==========================================
    // STEP TRANSITION LOGIC
    // ==========================================
    const stepHitung = document.getElementById('step-hitung-container');
    const stepReview = document.getElementById('step-review-container');
    const stepSimpan = document.getElementById('step-simpan-container');

    const circle1 = document.getElementById('circle-1');
    const circle2 = document.getElementById('circle-2');
    const circle3 = document.getElementById('circle-3');
    const circle4 = document.getElementById('circle-4');

    const label1 = document.getElementById('sublabel-1');
    const label2 = document.getElementById('sublabel-2');
    const label3 = document.getElementById('sublabel-3');
    const label4 = document.getElementById('sublabel-4');

    const line1 = document.getElementById('line-1');
    const line2 = document.getElementById('line-2');
    const line3 = document.getElementById('line-3');

    const statCardLokasi = document.getElementById('statCardLokasi');

    // Go to Step 3 (Review Selisih)
    document.getElementById('btnGoToStep3').addEventListener('click', function() {
        let hasData = false;
        document.querySelectorAll('.stok-fisik-input').forEach(input => {
            if (input.value !== '' && !isNaN(parseInt(input.value))) {
                hasData = true;
            }
        });

        if (!hasData) {
            alert('Silakan isi minimal satu produk dengan stok fisik sebelum melanjutkan.');
            return;
        }

        // Stepper Visuals
        circle2.innerHTML = '<i class="ph ph-check-bold" style="font-size: 1rem;"></i>';
        circle2.className = 'step-circle completed';
        label2.textContent = 'Selesai';
        label2.className = 'step-sublabel completed';

        circle3.className = 'step-circle active';
        label3.textContent = 'Aktif';
        label3.className = 'step-sublabel active';

        line2.className = 'step-line completed';

        // Show location card
        statCardLokasi.classList.remove('d-none');
        
        // Sync Location
        const locSelect = document.getElementById('idLokasi');
        const locText = locSelect.options[locSelect.selectedIndex].text;
        const locVal = locSelect.value;
        document.getElementById('statLokasi').innerHTML = locVal + ' <br><small class="text-muted fw-normal" style="font-size: 0.72rem;">' + locText.substring(locText.indexOf('(') + 1, locText.indexOf(')')) + '</small>';
        document.getElementById('confirmLokasi').textContent = locVal + ' - ' + locText.substring(locText.indexOf('(') + 1, locText.indexOf(')'));

        // Populate step 3 table (Only products with difference)
        populateReviewTable();

        // Switch containers
        stepHitung.style.display = 'none';
        stepReview.style.display = 'block';
        stepSimpan.style.display = 'none';
        
        window.scrollTo(0, 0);
    });

    // Direct submit from Step 2
    document.getElementById('btnSimpanLangsung').addEventListener('click', function() {
        let hasData = false;
        document.querySelectorAll('#opnameTableBody .stok-fisik-input').forEach(input => {
            if (input.value !== '' && !isNaN(parseInt(input.value))) {
                hasData = true;
            }
        });

        if (!hasData) {
            alert('Silakan isi minimal satu produk dengan stok fisik sebelum menyimpan.');
            return;
        }

        if (confirm('Apakah Anda yakin ingin langsung menyimpan hasil penyesuaian stok opname ini?')) {
            document.getElementById('formOpname').submit();
        }
    });

    // Go back to Step 2
    document.getElementById('btnBackToStep2').addEventListener('click', function() {
        // Reset Stepper
        circle2.innerHTML = '2';
        circle2.className = 'step-circle active';
        label2.textContent = 'Aktif';
        label2.className = 'step-sublabel active';

        circle3.className = 'step-circle pending';
        label3.textContent = 'Belum Aktif';
        label3.className = 'step-sublabel';

        line2.className = 'step-line pending';

        // Hide location card
        statCardLokasi.classList.add('d-none');

        // Switch containers
        stepHitung.style.display = 'block';
        stepReview.style.display = 'none';
        stepSimpan.style.display = 'none';
        
        window.scrollTo(0, 0);
    });

    // Go to Step 4 (Simpan Penyesuaian)
    document.getElementById('btnGoToStep4').addEventListener('click', function() {
        // Stepper Visuals
        circle3.innerHTML = '<i class="ph ph-check-bold" style="font-size: 1rem;"></i>';
        circle3.className = 'step-circle completed';
        label3.textContent = 'Selesai';
        label3.className = 'step-sublabel completed';

        circle4.className = 'step-circle active';
        label4.textContent = 'Aktif';
        label4.className = 'step-sublabel active';

        line3.className = 'step-line completed';

        // Populate step 4 table
        populateConfirmTable();

        // Switch containers
        stepHitung.style.display = 'none';
        stepReview.style.display = 'none';
        stepSimpan.style.display = 'block';
        
        window.scrollTo(0, 0);
    });

    // Go back to Step 3
    document.getElementById('btnBackToStep3').addEventListener('click', function() {
        // Reset Stepper
        circle3.innerHTML = '3';
        circle3.className = 'step-circle active';
        label3.textContent = 'Aktif';
        label3.className = 'step-sublabel active';

        circle4.className = 'step-circle pending';
        label4.textContent = 'Belum Aktif';
        label4.className = 'step-sublabel';

        line3.className = 'step-line pending';

        // Switch containers
        stepHitung.style.display = 'none';
        stepReview.style.display = 'block';
        stepSimpan.style.display = 'none';
        
        window.scrollTo(0, 0);
    });

    // Populate Review Table (Step 3)
    function populateReviewTable() {
        const reviewBody = document.getElementById('reviewTableBody');
        reviewBody.innerHTML = '';

        let rowNum = 1;
        let totalKurang = 0;
        let totalLebih = 0;
        let totalAll = 0;
        let firstChangedName = '';

        rows.forEach(row => {
            const input = row.querySelector('.stok-fisik-input');
            const stokFisikVal = input.value;

            if (stokFisikVal !== '' && !isNaN(parseInt(stokFisikVal))) {
                const pId = row.dataset.productId;
                const kode = row.querySelector('td:nth-child(2)').textContent;
                const nama = row.querySelector('td:nth-child(3)').textContent;
                const stokSistem = parseInt(input.dataset.stokSistem) || 0;
                const stokFisik = parseInt(stokFisikVal);
                const diff = stokFisik - stokSistem;
                const ket = row.querySelector('.ket-input').value || '-';

                // Only show rows with differences
                if (diff !== 0) {
                    if (firstChangedName === '') firstChangedName = nama;

                    if (diff < 0) {
                        totalKurang += diff;
                    } else {
                        totalLebih += diff;
                    }
                    totalAll += diff;

                    const selisihHtml = diff > 0 
                        ? '<span class="text-primary fw-bold">+' + diff + '</span>'
                        : '<span class="text-danger fw-bold">' + diff + '</span>';

                    const statusHtml = diff < 0
                        ? '<span class="badge-status badge-kurang"><i class="ph ph-arrow-down"></i> Kurang</span>'
                        : '<span class="badge-status badge-lebih"><i class="ph ph-arrow-up"></i> Lebih</span>';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center text-muted">${rowNum}</td>
                        <td class="fw-semibold text-muted">${kode}</td>
                        <td class="fw-bold">${nama}</td>
                        <td class="text-center">${stokSistem.toLocaleString('id-ID')}</td>
                        <td class="text-center fw-semibold">
                            <input type="number" class="stok-fisik-input text-center" style="width:90px;" value="${stokFisik}" readonly disabled>
                        </td>
                        <td class="text-center">${selisihHtml}</td>
                        <td class="text-center">${statusHtml}</td>
                        <td>
                            <input type="text" class="ket-input" value="${ket}" readonly disabled style="background:#f8fafc;">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-light border p-1 btn-edit-trigger" style="border-radius:6px;" title="Edit Stok">
                                <i class="ph ph-pencil-simple text-primary" style="font-size:1.15rem;"></i>
                            </button>
                        </td>
                    `;

                    // Edit trigger returning to step 2
                    tr.querySelector('.btn-edit-trigger').addEventListener('click', function() {
                        document.getElementById('btnBackToStep2').click();
                        // Focus on the specific input
                        input.focus();
                        input.select();
                    });

                    reviewBody.appendChild(tr);
                    rowNum++;
                }
            }
        });

        // If no changes found, display placeholder row
        if (rowNum === 1) {
            reviewBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="ph ph-info fs-3 d-block mb-1"></i> Tidak ada produk dengan selisih stok.
                    </td>
                </tr>
            `;
        }

        // Update step 3 footer summary cards
        document.getElementById('summaryTotalKurang').textContent = totalKurang + ' pcs';
        document.getElementById('summaryTotalLebih').textContent = '+' + totalLebih + ' pcs';
        
        const prefix = totalAll > 0 ? '+' : '';
        document.getElementById('summaryTotalAll').textContent = prefix + totalAll + ' pcs';

        // Set name confirm
        document.getElementById('confirmNamaProduk').textContent = firstChangedName ? (rowNum > 2 ? firstChangedName + ' & ' + (rowNum - 2) + ' produk lainnya' : firstChangedName) : '-';
        
        // Update confirm screen metrics too
        document.getElementById('confirmTotalKurang').textContent = totalKurang + ' pcs';
        document.getElementById('confirmTotalLebih').textContent = '+' + totalLebih + ' pcs';
        document.getElementById('confirmTotalAll').textContent = prefix + totalAll + ' pcs';
    }

    // Populate Confirm Table (Step 4)
    function populateConfirmTable() {
        const confirmBody = document.getElementById('confirmTableBody');
        confirmBody.innerHTML = '';

        let rowNum = 1;

        rows.forEach(row => {
            const input = row.querySelector('.stok-fisik-input');
            const stokFisikVal = input.value;

            if (stokFisikVal !== '' && !isNaN(parseInt(stokFisikVal))) {
                const kode = row.querySelector('td:nth-child(2)').textContent;
                const nama = row.querySelector('td:nth-child(3)').textContent;
                const stokSistem = parseInt(input.dataset.stokSistem) || 0;
                const stokFisik = parseInt(stokFisikVal);
                const diff = stokFisik - stokSistem;
                const ket = row.querySelector('.ket-input').value || '-';

                // Only show changes
                if (diff !== 0) {
                    const selisihHtml = diff > 0 
                        ? '<span class="text-primary fw-bold">+' + diff + '</span>'
                        : '<span class="text-danger fw-bold">' + diff + '</span>';

                    const actionHtml = diff < 0
                        ? '<span class="badge-penyesuaian badge-penyesuaian-kurang"><i class="ph ph-minus"></i> Kurangi ' + Math.abs(diff) + '</span>'
                        : '<span class="badge-penyesuaian badge-penyesuaian-tambah"><i class="ph ph-plus"></i> Tambah ' + diff + '</span>';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center text-muted">${rowNum}</td>
                        <td class="fw-semibold text-muted">${kode}</td>
                        <td class="fw-bold">${nama}</td>
                        <td class="text-center">${stokSistem.toLocaleString('id-ID')}</td>
                        <td class="text-center fw-semibold">${stokFisik.toLocaleString('id-ID')}</td>
                        <td class="text-center">${selisihHtml}</td>
                        <td class="text-center">${actionHtml}</td>
                        <td class="text-center"><span class="text-success fw-bold">Stok baru: ${stokFisik.toLocaleString('id-ID')}</span></td>
                        <td class="text-muted" style="font-size:0.78rem;">${ket}</td>
                    `;

                    confirmBody.appendChild(tr);
                    rowNum++;
                }
            }
        });

        // Time Confirmation stamp
        const now = new Date();
        const timePart = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        const datePart = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        document.getElementById('confirmWaktu').textContent = datePart + ' ' + timePart;
    }

    // Submit Action - Disable empty fields before submitting so they are ignored by controller
    document.getElementById('formOpname').addEventListener('submit', function(e) {
        let hasData = false;
        
        // Temporarily enable everything just to make sure disabled review elements don't block
        document.querySelectorAll('.stok-fisik-input, .ket-input, input[type="hidden"]').forEach(el => el.disabled = false);

        document.querySelectorAll('#opnameTableBody .stok-fisik-input').forEach(input => {
            if (input.value !== '' && !isNaN(parseInt(input.value))) {
                hasData = true;
            } else {
                // Disable to exclude from POST request
                input.disabled = true;
                const row = input.closest('tr');
                const ket = row.querySelector('.ket-input');
                if (ket) ket.disabled = true;
                const hidden = row.querySelector('input[type="hidden"]');
                if (hidden) hidden.disabled = true;
            }
        });

        if (!hasData) {
            e.preventDefault();
            alert('Silakan isi minimal satu produk dengan stok fisik.');
            // Re-enable everything if aborted
            document.querySelectorAll('.stok-fisik-input, .ket-input, input[type="hidden"]').forEach(el => el.disabled = false);
            return false;
        }
    });

    // Initial setup
    updateStats();
});
</script>
@endsection
