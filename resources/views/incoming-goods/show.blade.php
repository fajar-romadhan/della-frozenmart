@extends('layouts.app')
@section('title', 'Detail Barang Masuk')
@section('page-title', 'Detail Barang Masuk')

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
    
    .glass-card {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-card);
        transition: var(--transition);
        margin-bottom: 24px;
        height: 100%;
    }
    
    .glass-card:hover {
        box-shadow: var(--shadow-card-hover);
        background: rgba(255, 255, 255, 0.85) !important;
    }

    .card-header-custom {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header-title {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1rem;
        color: #1e293b;
        margin: 0;
    }

    .card-body-custom {
        padding: 24px;
    }

    /* Info Table Style */
    .info-table {
        margin-bottom: 0;
        width: 100%;
    }

    .info-table td {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
    }

    .info-table tr:last-child td {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #64748b;
        width: 35%;
    }

    .info-value {
        color: #1e293b;
        font-weight: 500;
    }

    /* Batch Table style matching mockup */
    .batch-table-container {
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .batch-table {
        width: 100%;
        margin-bottom: 0;
    }

    .batch-table th {
        font-weight: 600;
        font-size: 0.78rem;
        color: #ffffff;
        background-color: #1e3a8a !important; /* Deep Navy Blue header */
        border-bottom: none;
        padding: 12px 14px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .batch-table td {
        font-size: 0.85rem;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .batch-table tbody tr:last-child td {
        border-bottom: none;
    }

    .batch-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header Section --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary me-3 border-0 bg-white shadow-sm" style="border-radius: 8px; padding: 10px 14px;">
            <i class="ph ph-arrow-left" style="font-size: 1.1rem; vertical-align: middle;"></i>
        </a>
        <div>
            <h1 class="page-title-main mb-1">Detail Transaksi Barang Masuk</h1>
            <p class="page-subtitle mb-0">Kode Batch: <span class="badge fw-bold" style="font-size: 0.85rem; background-color: rgba(91, 141, 238, 0.15); color: #1d4ed8; border: 1px solid rgba(91, 141, 238, 0.35);">{{ $barang_masuk->batch_code }}</span></p>
        </div>
    </div>

    <div class="row">
        {{-- Left Card: Detail Information --}}
        <div class="col-md-6 mb-4">
            <div class="card glass-card">
                <div class="card-header-custom">
                    <i class="ph ph-info-fill text-primary" style="font-size: 1.25rem;"></i>
                    <h5 class="card-header-title">Informasi Penerimaan</h5>
                </div>
                <div class="card-body-custom">
                    <table class="table info-table">
                        <tbody>
                            <tr>
                                <td class="info-label">Kode Batch</td>
                                <td class="info-value fw-bold text-primary">{{ $barang_masuk->batch_code }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Nama Produk</td>
                                <td class="info-value fw-bold text-dark">
                                    {{ $barang_masuk->product->nama_produk ?? '-' }}
                                    <span class="d-block text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600;">{{ $barang_masuk->product->kode_produk ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label">Nama Supplier</td>
                                <td class="info-value fw-semibold text-secondary">{{ $barang_masuk->supplier->nama_supplier ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Tanggal Masuk</td>
                                <td class="info-value">{{ \Carbon\Carbon::parse($barang_masuk->tanggal_masuk)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Jumlah Masuk</td>
                                <td class="info-value">
                                    <span class="text-success fw-bold fs-5">+{{ number_format($barang_masuk->jumlah) }}</span>
                                    <span class="badge bg-light text-secondary border fw-semibold ms-1">{{ $barang_masuk->satuan ?? 'PCS' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label">Harga Satuan</td>
                                <td class="info-value fw-semibold">Rp {{ number_format($barang_masuk->harga_beli ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Total Nilai</td>
                                <td class="info-value fw-bold text-dark fs-6">Rp {{ number_format(($barang_masuk->jumlah * $barang_masuk->harga_beli), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Lokasi Simpan</td>
                                <td class="info-value"><span class="badge bg-light text-secondary fw-semibold border">{{ $barang_masuk->id_lokasi ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td class="info-label">Kedaluwarsa</td>
                                <td class="info-value">
                                    @if($barang_masuk->tanggal_kedaluwarsa)
                                        @php
                                            $isExpired = \Carbon\Carbon::parse($barang_masuk->tanggal_kedaluwarsa)->isPast();
                                        @endphp
                                        <span class="fw-semibold {{ $isExpired ? 'text-danger' : 'text-dark' }}">
                                            {{ \Carbon\Carbon::parse($barang_masuk->tanggal_kedaluwarsa)->format('d F Y') }}
                                        </span>
                                        @if($isExpired)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger ms-1" style="font-size:0.7rem">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label">Sumber Data</td>
                                <td class="info-value"><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-semibold">{{ $barang_masuk->sumber_import }}</span></td>
                            </tr>
                            <tr>
                                <td class="info-label">Dicatat Oleh</td>
                                <td class="info-value">{{ $barang_masuk->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Waktu Input</td>
                                <td class="info-value text-muted">{{ $barang_masuk->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Keterangan</td>
                                <td class="info-value text-secondary">{{ $barang_masuk->keterangan ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Card: Stock Batches related (FIFO inventory monitoring) --}}
        <div class="col-md-6 mb-4">
            <div class="card glass-card">
                <div class="card-header-custom">
                    <i class="ph ph-layers-fill text-primary" style="font-size: 1.25rem;"></i>
                    <h5 class="card-header-title">Alokasi Batch Stok (FIFO)</h5>
                </div>
                <div class="card-body-custom">
                    <p class="text-muted small mb-3">Monitoring alokasi persediaan fisik untuk batch barang ini. Stok sisa akan diambil terlebih dahulu ketika terjadi transaksi barang keluar.</p>
                    
                    <div class="batch-table-container">
                        <table class="table align-middle batch-table">
                            <thead>
                                <tr>
                                    <th>Kode Batch</th>
                                    <th class="text-center" style="width: 25%;">Jumlah Awal</th>
                                    <th class="text-center" style="width: 25%;">Jumlah Sisa</th>
                                    <th class="text-center" style="width: 20%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($barang_masuk->stockBatch)
                                @php $batch = $barang_masuk->stockBatch; @endphp
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $batch->batch_code }}</td>
                                    <td class="text-center fw-semibold text-secondary">{{ number_format($batch->jumlah_awal) }}</td>
                                    <td class="text-center fw-bold {{ $batch->jumlah_sisa > 0 ? 'text-success' : 'text-muted' }}">
                                        {{ number_format($batch->jumlah_sisa) }}
                                    </td>
                                    <td class="text-center">
                                        @if($batch->jumlah_sisa == 0)
                                            <span class="badge bg-light text-muted border fw-semibold">Habis</span>
                                        @elseif($batch->jumlah_sisa == $batch->jumlah_awal)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success fw-semibold">Utuh</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning fw-semibold">Terpakai</span>
                                        @endif
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="ph ph-warning-circle d-block mb-1" style="font-size: 1.5rem;"></i>
                                        Tidak ada alokasi batch stok aktif untuk transaksi ini.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
