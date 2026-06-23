@extends('layouts.app')
@section('title', 'Detail Barang Keluar')
@section('page-title', 'Detail Barang Keluar')

@section('content')
<style>
    /* Premium Detail Page */
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

    /* Detail Card */
    .detail-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .detail-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    }

    .detail-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-card-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .detail-card-header i {
        font-size: 1.3rem;
        color: #dc2626;
    }

    .detail-card-body {
        padding: 20px;
    }

    /* Info Row */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .info-value {
        font-size: 0.88rem;
        font-weight: 600;
        color: #1e293b;
        text-align: right;
        max-width: 60%;
    }

    /* Highlight row */
    .info-row-highlight {
        background: #fef2f2;
        border-radius: 8px;
        padding: 12px 14px;
        margin: 4px -4px;
    }

    /* Badge jenis */
    .badge-jenis-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .badge-penjualan-detail {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
    }

    .badge-rusak-detail {
        background: rgba(220, 38, 38, 0.08);
        color: #dc2626;
    }

    .badge-kedaluwarsa-detail {
        background: rgba(245, 158, 11, 0.08);
        color: #d97706;
    }

    .badge-penyesuaian-detail {
        background: rgba(100, 116, 139, 0.08);
        color: #64748b;
    }

    /* FIFO Table */
    .fifo-table {
        margin: 0;
        width: 100%;
    }

    .fifo-table th {
        font-weight: 600;
        font-size: 0.78rem;
        color: #ffffff;
        background-color: #7f1d1d !important;
        border-bottom: none;
        padding: 12px 16px;
        text-align: left;
    }

    .fifo-table td {
        font-size: 0.85rem;
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    .fifo-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .fifo-table tbody tr:hover {
        background-color: #fef2f2;
    }

    .fifo-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Batch code badge */
    .batch-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        background: rgba(37, 99, 235, 0.06);
        color: #2563eb;
        border: 1px solid rgba(37, 99, 235, 0.1);
    }

    /* Summary highlight */
    .qty-highlight {
        font-size: 1.6rem;
        font-weight: 800;
        color: #dc2626;
        line-height: 1;
    }

    /* Timeline stamp */
    .timestamp-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        padding: 14px 18px;
        margin-top: 16px;
    }

    .timestamp-row {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
        color: #64748b;
        padding: 4px 0;
    }

    .timestamp-row i {
        font-size: 1rem;
        color: #94a3b8;
    }

    .timestamp-row strong {
        color: #475569;
    }
</style>

<div class="container-fluid py-2">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('barang-keluar.index') }}" class="page-header-back me-3">
            <i class="ph ph-arrow-left" style="font-size: 1.2rem;"></i>
        </a>
        <div>
            <h1 class="page-title-main mb-1">Detail Barang Keluar</h1>
            <p class="page-subtitle mb-0">{{ $barang_keluar->product->nama_produk ?? '-' }} — {{ \Carbon\Carbon::parse($barang_keluar->tanggal_keluar)->format('d F Y') }}</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Informasi Transaksi --}}
        <div class="col-lg-5">
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="ph ph-info"></i>
                    <h3>Informasi Transaksi</h3>
                </div>
                <div class="detail-card-body">
                    <div class="info-row">
                        <span class="info-label"><i class="ph ph-cube me-1"></i> Produk</span>
                        <span class="info-value fw-bold">{{ $barang_keluar->product->nama_produk ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="ph ph-barcode me-1"></i> Kode Produk</span>
                        <span class="info-value" style="color: #64748b;">{{ $barang_keluar->product->kode_produk ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="ph ph-calendar-blank me-1"></i> Tanggal Keluar</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($barang_keluar->tanggal_keluar)->format('d F Y') }}</span>
                    </div>
                    <div class="info-row info-row-highlight">
                        <span class="info-label"><i class="ph ph-minus-circle me-1"></i> Jumlah Keluar</span>
                        <span class="qty-highlight">-{{ number_format($barang_keluar->jumlah) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="ph ph-tag me-1"></i> Jenis Keluar</span>
                        <span class="info-value">
                            @php 
                                $jenisIcons = [
                                    'penjualan' => 'ph-shopping-cart',
                                    'rusak' => 'ph-x-circle',
                                    'kedaluwarsa' => 'ph-clock-countdown',
                                    'penyesuaian' => 'ph-arrows-clockwise'
                                ];
                            @endphp
                            <span class="badge-jenis-detail badge-{{ $barang_keluar->jenis_keluar }}-detail">
                                <i class="ph {{ $jenisIcons[$barang_keluar->jenis_keluar] ?? 'ph-circle' }}"></i>
                                {{ ucfirst($barang_keluar->jenis_keluar) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="ph ph-note-pencil me-1"></i> Keterangan</span>
                        <span class="info-value" style="color: #64748b;">{{ $barang_keluar->keterangan ?? '-' }}</span>
                    </div>

                    <div class="timestamp-card">
                        <div class="timestamp-row">
                            <i class="ph ph-user"></i>
                            <span>Dicatat oleh: <strong>{{ $barang_keluar->user->name ?? '-' }}</strong></span>
                        </div>
                        <div class="timestamp-row">
                            <i class="ph ph-clock"></i>
                            <span>Waktu input: <strong>{{ $barang_keluar->created_at->format('d/m/Y H:i:s') }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Detail FIFO --}}
        <div class="col-lg-7">
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="ph ph-stack"></i>
                    <h3>Detail FIFO — Batch yang Dikurangi</h3>
                </div>
                <div class="detail-card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle fifo-table">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">#</th>
                                    <th style="width: 42%;">Kode Batch</th>
                                    <th style="width: 25%; text-align: center;">Jumlah Diambil</th>
                                    <th style="width: 25%; text-align: right; padding-right: 20px;">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalDeducted = $barang_keluar->outgoingGoodDetails->sum('jumlah_diambil'); @endphp
                                @forelse($barang_keluar->outgoingGoodDetails as $idx => $detail)
                                <tr>
                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="batch-badge">
                                            <i class="ph ph-stack-simple"></i>
                                            {{ $detail->stockBatch->batch_code ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-danger fw-bold" style="font-size: 0.95rem;">-{{ number_format($detail->jumlah_diambil) }}</span>
                                        <span class="text-muted" style="font-size: 0.78rem;"> pcs</span>
                                    </td>
                                    <td class="text-end" style="padding-right: 20px;">
                                        @if($totalDeducted > 0)
                                            @php $pct = round(($detail->jumlah_diambil / $totalDeducted) * 100, 1); @endphp
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <div style="width: 60px; height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden;">
                                                    <div style="width: {{ $pct }}%; height: 100%; background: linear-gradient(90deg, #dc2626, #ef4444); border-radius: 3px;"></div>
                                                </div>
                                                <span class="fw-semibold text-muted" style="font-size: 0.82rem; min-width: 40px; text-align: right;">{{ $pct }}%</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="ph ph-info fs-2 d-block mb-2"></i>
                                        Tidak ada detail FIFO untuk transaksi ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Footer --}}
                    @if($barang_keluar->outgoingGoodDetails->count() > 0)
                    <div style="padding: 14px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                <i class="ph ph-stack me-1"></i>
                                Total {{ $barang_keluar->outgoingGoodDetails->count() }} batch terpengaruh
                            </span>
                            <span style="font-size: 0.95rem; font-weight: 800; color: #dc2626;">
                                Total: -{{ number_format($totalDeducted) }} pcs
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
