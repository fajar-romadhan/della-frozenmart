@extends('layouts.app')
@section('title', 'Analisis Persediaan')
@section('page-title', 'Analisis Persediaan')

@section('content')
<style>
    /* Premium Analisis Persediaan Styling */
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

    .section-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        padding: 24px;
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Section 1: Drag & Drop Upload Zone */
    .upload-dropzone {
        border: 2px dashed #bfdbfe;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
    }

    .upload-dropzone:hover {
        border-color: #2563eb;
        background: #f8fafc;
        transform: translateY(-1px);
    }

    .upload-icon {
        font-size: 2.8rem;
        color: #2563eb;
        margin-bottom: 12px;
        display: inline-block;
        transition: transform 0.2s ease;
    }

    .upload-dropzone:hover .upload-icon {
        transform: scale(1.08);
    }

    .upload-dropzone input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .upload-text {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .upload-subtext {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Section 2: Summary Stats Row */
    .summary-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.03);
    }

    .summary-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .summary-icon-wrapper i {
        font-size: 1.6rem;
    }

    .summary-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }

    .summary-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 2px;
    }

    .summary-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .summary-unit {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        margin-left: 4px;
    }

    /* Colors and themes for stats */
    .bg-blue-soft { background-color: rgba(37, 99, 235, 0.07); color: #2563eb; }
    .bg-green-soft { background-color: rgba(16, 185, 129, 0.07); color: #10b981; }
    .bg-warning-soft { background-color: rgba(245, 158, 11, 0.07); color: #f59e0b; }
    .bg-danger-soft { background-color: rgba(220, 38, 38, 0.07); color: #dc2626; }

    .border-blue-hover:hover { border-color: rgba(37, 99, 235, 0.2); }
    .border-green-hover:hover { border-color: rgba(16, 185, 129, 0.2); }
    .border-warning-hover:hover { border-color: rgba(245, 158, 11, 0.2); }
    .border-danger-hover:hover { border-color: rgba(220, 38, 38, 0.2); }

    /* Section 3: Table Styles */
    .analysis-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
    }

    .analysis-table {
        margin-bottom: 0;
        width: 100%;
    }

    .analysis-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #ffffff !important;
        background-color: #1e293b !important; /* Premium Navy Blue */
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 8px;
        text-align: left;
        white-space: normal !important;
    }

    .analysis-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .analysis-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .analysis-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Table columns */
    .analysis-table th.text-center, .analysis-table td.text-center {
        text-align: center !important;
    }

    .analysis-table th.text-end, .analysis-table td.text-end {
        text-align: right !important;
    }

    /* Colored stock values */
    .stock-val-aman { color: #10b981; font-weight: 700; }
    .stock-val-warning { color: #f59e0b; font-weight: 700; }
    .stock-val-order { color: #dc2626; font-weight: 700; }

    /* Status badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-status-aman { background-color: rgba(16, 185, 129, 0.08); color: #059669; }
    .badge-status-warning { background-color: rgba(245, 158, 11, 0.08); color: #d97706; }
    .badge-status-order { background-color: rgba(220, 38, 38, 0.08); color: #dc2626; }

    /* File template banner */
    .alert-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.8rem;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Footer info */
    .footer-info-bar {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 0.8rem;
        color: #1e40af;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 20px;
        font-weight: 500;
    }

    /* Drag and drop overlay */
    .drag-active {
        border-color: #2563eb !important;
        background-color: #eff6ff !important;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title-main mb-1">Analisa Persediaan</h1>
            <p class="page-subtitle mb-0">Hitung Safety Stock (SS) dan Reorder Point (ROP) otomatis dari data penjualan harian.</p>
        </div>
    </div>

    {{-- Error/Success Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="border-radius: 8px; font-size: 0.85rem;">
            <i class="ph ph-check-circle-fill" style="font-size: 1.1rem; vertical-align: middle;"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="border-radius: 8px; font-size: 0.85rem;">
            <i class="ph ph-warning-circle-fill" style="font-size: 1.1rem; vertical-align: middle;"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
        </div>
    @endif

    {{-- Section 1: Upload Data Penjualan Harian --}}
    @if(auth()->user()->role !== 'admin')
        <div class="section-card">
            <h3 class="section-title">Upload Data Penjualan Harian</h3>
            <p class="text-muted fs-7 mb-3" style="font-size: 0.85rem; margin-top: -8px;">Upload file penjualan harian (.xlsx, .xls, .csv). Pastikan format sesuai template.</p>
            
            <form action="{{ route('analisis.upload-sales') }}" method="POST" enctype="multipart/form-data" id="uploadSalesForm">
                @csrf
                <div class="upload-dropzone" id="dropzone">
                    <i class="ph ph-cloud-arrow-up upload-icon"></i>
                    <div class="upload-text" id="uploadText">Klik atau drag file di sini</div>
                    <div class="upload-subtext">Format: .xlsx, .xls, .csv</div>
                    <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" required>
                </div>
            </form>

            <div class="alert-banner mt-3">
                <i class="ph ph-info-fill text-primary" style="font-size: 1.15rem;"></i>
                <span>Format kolom data penjualan: <strong>Tanggal, Nama Barang, Jumlah</strong>. Header diletakkan pada baris pertama.</span>
            </div>
        </div>
    @endif

    {{-- Section 2: Ringkasan Hasil Analisa --}}
    <div class="section-card" style="padding-bottom: 8px;">
        <h3 class="section-title">Ringkasan Hasil Analisa</h3>
        <div class="summary-stats-grid">
            {{-- Card 1: Total Dianalisis --}}
            <div class="summary-card border-blue-hover">
                <div class="summary-icon-wrapper bg-blue-soft">
                    <i class="ph ph-package"></i>
                </div>
                <div class="summary-info">
                    <span class="summary-label">Total Produk Dianalisis</span>
                    <div class="d-flex align-items-baseline">
                        <span class="summary-value">{{ $analyses->count() }}</span>
                        <span class="summary-unit">Produk</span>
                    </div>
                </div>
            </div>
            {{-- Card 2: Status Aman --}}
            <div class="summary-card border-green-hover">
                <div class="summary-icon-wrapper bg-green-soft">
                    <i class="ph ph-shield-check"></i>
                </div>
                <div class="summary-info">
                    <span class="summary-label">Status Aman</span>
                    <div class="d-flex align-items-baseline">
                        <span class="summary-value">{{ $analyses->where('status_stok', 'Aman')->count() }}</span>
                        <span class="summary-unit">Produk</span>
                    </div>
                </div>
            </div>
            {{-- Card 3: Status Warning --}}
            <div class="summary-card border-warning-hover">
                <div class="summary-icon-wrapper bg-warning-soft">
                    <i class="ph ph-warning"></i>
                </div>
                <div class="summary-info">
                    <span class="summary-label">Status Warning</span>
                    <div class="d-flex align-items-baseline">
                        <span class="summary-value">{{ $analyses->where('status_stok', 'Warning')->count() }}</span>
                        <span class="summary-unit">Produk</span>
                    </div>
                </div>
            </div>
            {{-- Card 4: Status Order --}}
            <div class="summary-card border-danger-hover">
                <div class="summary-icon-wrapper bg-danger-soft">
                    <i class="ph ph-x-circle"></i>
                </div>
                <div class="summary-info">
                    <span class="summary-label">Status Order</span>
                    <div class="d-flex align-items-baseline">
                        <span class="summary-value">{{ $analyses->where('status_stok', 'Order')->count() }}</span>
                        <span class="summary-unit">Produk</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 3: Hasil Perhitungan per Produk --}}
    <div class="section-card" style="padding: 0; border: none; background: transparent;">
        <div class="section-card" style="border-radius: 12px 12px 0 0; margin-bottom: 0; border-bottom: none;">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="section-title mb-0">Hasil Perhitungan per Produk</h3>
                <div class="d-flex gap-2">
                    <form action="{{ route('analisis.analyze-all') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3 py-2" style="border-radius: 8px;" onclick="return confirm('Proses ini akan menghitung ulang Safety Stock dan ROP untuk semua produk aktif berdasarkan data penjualan terbaru. Lanjutkan?')">
                            <i class="ph ph-arrows-clockwise" style="font-size: 1rem; vertical-align: middle;"></i> Analisis Ulang Semua Produk
                        </button>
                    </form>
                    <a href="{{ route('status-stok') }}" class="btn btn-light btn-sm border fw-bold px-3 py-2" style="border-radius: 8px;">
                        <i class="ph ph-chart-bar" style="font-size: 1rem; vertical-align: middle;"></i> Monitor Status Stok
                    </a>
                </div>
            </div>
        </div>
        
        <div class="analysis-table-card" style="border-radius: 0 0 12px 12px;">
            <div class="table-responsive">
                <table class="table align-middle analysis-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th>KODE PRODUK</th>
                            <th>NAMA PRODUK</th>
                            <th class="text-end">STOK SAAT INI (PCS)</th>
                            <th class="text-center">AU (PCS/HARI)</th>
                            <th class="text-center">PENJUALAN MAKS (PCS/HARI)</th>
                            <th class="text-center">LEAD TIME (LT) (HARI)</th>
                            <th class="text-end">SAFETY STOCK (SS) (PCS)</th>
                            <th class="text-end">REORDER POINT (ROP) (PCS)</th>
                            <th class="text-center">STATUS STOK</th>
                            @if(auth()->user()->role !== 'admin')
                                <th class="text-center" style="width: 60px;">AKSI</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analyses as $index => $analysis)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td class="fw-semibold text-muted">{{ $analysis->product->kode_produk ?? '-' }}</td>
                                <td class="fw-bold">{{ $analysis->product->nama_produk ?? '-' }}</td>
                                <td class="text-end">
                                    @php
                                        $stockClass = 'stock-val-aman';
                                        if ($analysis->status_stok == 'Warning') {
                                            $stockClass = 'stock-val-warning';
                                        } elseif ($analysis->status_stok == 'Order') {
                                            $stockClass = 'stock-val-order';
                                        }
                                    @endphp
                                    <span class="{{ $stockClass }}" style="font-size: 0.9rem;">
                                        {{ number_format($analysis->product->stok_saat_ini ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">{{ number_format($analysis->average_usage, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($analysis->max_sales, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $analysis->lead_time }}</td>
                                <td class="text-end fw-semibold" style="color: #475569;">{{ number_format($analysis->safety_stock, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold" style="color: #1e293b;">{{ number_format($analysis->reorder_point, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($analysis->status_stok == 'Aman')
                                        <span class="badge-status badge-status-aman">
                                            <i class="ph ph-check-circle-fill" style="font-size: 0.95rem;"></i> Aman
                                        </span>
                                    @elseif($analysis->status_stok == 'Warning')
                                        <span class="badge-status badge-status-warning">
                                            <i class="ph ph-warning-fill" style="font-size: 0.95rem;"></i> Warning
                                        </span>
                                    @else
                                        <span class="badge-status badge-status-order">
                                            <i class="ph ph-x-circle-fill" style="font-size: 0.95rem;"></i> Order
                                        </span>
                                    @endif
                                </td>
                                @if(auth()->user()->role !== 'admin')
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 6px;">
                                                <i class="ph ph-dots-three-vertical-bold" style="font-size: 1.1rem; vertical-align: middle;"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="background:#ffffff;">
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ route('analisis.show', $analysis->product_id) }}" style="font-size: 0.8rem; font-weight: 600;">
                                                        <i class="ph ph-eye text-primary me-2"></i> Detail Analisis
                                                    </a>
                                                </li>
                                                @if(in_array(auth()->user()->role, ['admin', 'manager']))
                                                    <li>
                                                        <form action="{{ route('analisis.analyze', $analysis->product_id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-2" style="font-size: 0.8rem; font-weight: 600;">
                                                                <i class="ph ph-arrows-clockwise text-success me-2"></i> Analisis Ulang
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @if($analysis->status_stok == 'Order' || $analysis->status_stok == 'Warning')
                                                        <li>
                                                            <a class="dropdown-item py-2 text-danger" href="{{ route('pemesanan-supplier.create', ['product_id' => $analysis->product_id]) }}" style="font-size: 0.8rem; font-weight: 700;">
                                                                <i class="ph ph-shopping-cart text-danger me-2"></i> Buat Pemesanan
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'admin' ? 10 : 11 }}" class="text-center py-5 text-muted">
                                    <i class="ph ph-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data hasil analisis. Silakan unggah file penjualan harian di atas atau klik "Analisis Ulang Semua Produk".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Footer Info --}}
    @if($analyses->count() > 0)
        <div class="footer-info-bar">
            <i class="ph ph-clock-countdown-fill text-primary" style="font-size: 1.25rem;"></i>
            <span>Terakhir dihitung: <strong>{{ \Carbon\Carbon::parse($analyses->max('updated_at'))->locale('id')->isoFormat('D MMMM Y HH:mm') . ' WIB' }}</strong></span>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const uploadText = document.getElementById('uploadText');
    const form = document.getElementById('uploadSalesForm');

    // Drag and Drop interaction
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            dropzone.classList.add('drag-active');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            dropzone.classList.remove('drag-active');
        }, false);
    });

    dropzone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            handleFileSelect(fileInput.files[0]);
        }
    });

    function handleFileSelect(file) {
        uploadText.textContent = "Mengunggah: " + file.name + "...";
        uploadText.style.color = "#2563eb";
        
        // Show spinner / loading state
        const icon = dropzone.querySelector('.upload-icon');
        icon.className = "ph ph-spinner spinner-border text-primary";
        icon.style.width = "2.5rem";
        icon.style.height = "2.5rem";
        icon.style.borderWidth = "0.25em";

        // Submit form
        setTimeout(() => {
            form.submit();
        }, 800);
    }
});
</script>
@endsection
