@extends('layouts.app')
@section('title', 'Laporan Persediaan (Safety Stock & ROP)')
@section('page-title', 'Laporan Persediaan')

@section('content')
<style>
    /* Premium Table Styling */
    .report-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .report-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .report-table th {
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
    .report-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .report-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .report-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert" style="border-radius: 8px; font-size: 0.85rem;">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card mb-4 no-print shadow-sm" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08);">
        <div class="card-header bg-white" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding: 14px 20px;">
            <h5 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #0f172a;"><i class="bi bi-funnel text-primary"></i> Filter Status</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.persediaan') }}" class="row g-3" id="formFilterPersediaan">
                <div class="col-md-9">
                    <label class="form-label fw-bold" style="font-size: 0.85rem; color: #334155;">Status Stok</label>
                    <select name="status_stok" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Status</option>
                        <option value="Aman" {{ request('status_stok') === 'Aman' ? 'selected' : '' }}>Aman</option>
                        <option value="Warning" {{ request('status_stok') === 'Warning' ? 'selected' : '' }}>Warning</option>
                        <option value="Order" {{ request('status_stok') === 'Order' ? 'selected' : '' }}>Order</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 8px;"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Table --}}
    <div class="report-table-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding: 14px 20px;">
            <h5 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #0f172a;"><i class="bi bi-archive"></i> Laporan Analisis Persediaan</h5>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary me-2 fw-semibold" style="border-radius: 6px; font-size: 0.78rem;"><i class="bi bi-printer"></i> Cetak</button>
                <a href="{{ route('export.persediaan.pdf', request()->all()) }}" class="btn btn-sm btn-outline-danger me-2 fw-semibold" style="border-radius: 6px; font-size: 0.78rem;"><i class="bi bi-file-pdf"></i> Export PDF</a>
                <a href="{{ route('export.persediaan.excel', request()->all()) }}" class="btn btn-sm btn-outline-success fw-semibold" style="border-radius: 6px; font-size: 0.78rem;"><i class="bi bi-file-excel"></i> Export Excel</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 report-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>KODE PRODUK</th>
                        <th>NAMA PRODUK</th>
                        <th>KATEGORI</th>
                        <th class="text-center">STOK SAAT INI</th>
                        <th class="text-center">SAFETY STOCK</th>
                        <th class="text-center">REORDER POINT (ROP)</th>
                        <th class="text-center">LEAD TIME (HARI)</th>
                        <th class="text-center">STATUS</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse($analyses as $i => $item)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td><span class="badge bg-light text-dark fw-bold">{{ $item->product->kode_produk ?? '-' }}</span></td>
                                <td class="fw-semibold">{{ $item->product->nama_produk ?? '-' }}</td>
                                <td>{{ $item->product->category->nama_kategori ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ number_format($item->stok_saat_ini, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($item->safety_stock, 2, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($item->reorder_point, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $item->lead_time }}</td>
                                <td class="text-center">
                                    @if($item->status_stok === 'Aman')
                                        <span class="badge bg-success">Aman</span>
                                    @elseif($item->status_stok === 'Warning')
                                        <span class="badge bg-warning text-dark">Warning</span>
                                    @else
                                        <span class="badge bg-danger">Order</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data persediaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
