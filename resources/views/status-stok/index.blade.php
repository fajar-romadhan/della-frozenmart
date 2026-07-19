@extends('layouts.app')
@section('title', 'Status Stok')
@section('page-title', 'Status Stok Produk')

@section('content')
<style>
    /* Premium Table Styling */
    .status-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .status-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .status-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #ffffff !important;
        background-color: #1e293b !important; /* Premium Navy Blue */
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 8px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: normal !important;
    }
    .status-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .status-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .status-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    <div class="mb-4">
        <h4 class="fw-bold mb-1" style="color: #0f172a; font-family: var(--font-display);">Status Stok Seluruh Produk</h4>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Ringkasan kondisi stok berdasarkan analisis Safety Stock</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Stok Aman</span>
                    <span class="stat-value">{{ $amanCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Warning</span>
                    <span class="stat-value">{{ $warningCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="bi bi-cart-x"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Perlu Order</span>
                    <span class="stat-value">{{ $orderCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="status-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 status-table">
                <thead>
                    <tr>
                        <th>PRODUK</th>
                        <th class="text-center" style="width: 140px;">STOK</th>
                        <th class="text-center" style="width: 160px;">SAFETY STOCK</th>
                        <th class="text-center" style="width: 160px;">REORDER POINT (ROP)</th>
                        <th class="text-center" style="width: 140px;">STATUS</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse($analyses as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->product->nama_produk ?? '-' }}</td>
                            <td class="text-center fw-bold">{{ number_format($item->stok_saat_ini) }}</td>
                            <td class="text-center">{{ number_format($item->safety_stock, 2, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($item->reorder_point, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($item->status_stok == 'Aman')
                                    <span class="badge bg-success">Aman</span>
                                @elseif($item->status_stok == 'Warning')
                                    <span class="badge bg-warning text-dark">Warning</span>
                                @else
                                    <span class="badge bg-danger">Order</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-bar-chart fs-1 d-block mb-2"></i>Belum ada data analisis. Lakukan analisis terlebih dahulu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
