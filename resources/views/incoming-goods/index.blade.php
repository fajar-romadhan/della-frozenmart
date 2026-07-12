@extends('layouts.app')
@section('title', 'Barang Masuk')
@section('page-title', 'Barang Masuk')

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
    
    .btn-add-incoming {
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
    
    .btn-add-incoming:hover {
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
    
    /* Stat Cards with Premium Hover Animations */
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
    
    .bg-primary-soft {
        background-color: rgba(37, 99, 235, 0.08) !important;
    }
    
    .bg-success-soft {
        background-color: rgba(16, 185, 129, 0.08) !important;
    }
    
    .bg-warning-soft {
        background-color: rgba(245, 158, 11, 0.08) !important;
    }
    
    .bg-purple-soft {
        background-color: rgba(139, 92, 246, 0.08) !important;
    }
    
    .text-purple {
        color: #8b5cf6 !important;
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
        font-size: 1.80rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 6px;
        white-space: nowrap;
        word-break: keep-all;
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
    
    .incoming-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .incoming-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    
    .incoming-table th {
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
    
    .incoming-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    
    .incoming-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .incoming-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Column width hints */
    .col-date { white-space: nowrap; }
    .col-id { white-space: nowrap; }
    
    .incoming-table th.col-qty, .incoming-table td.col-qty { text-align: center !important; }
    .incoming-table th.col-price, .incoming-table td.col-price { text-align: right !important; white-space: nowrap; }
    .incoming-table th.col-total, .incoming-table td.col-total { text-align: right !important; white-space: nowrap; }
    .incoming-table th.col-loc, .incoming-table td.col-loc { text-align: center !important; }
    .col-action { text-align: center !important; }
    
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
            <h1 class="page-title-main mb-1">Barang Masuk</h1>
            <p class="page-subtitle mb-0">Kelola data transaksi barang masuk ke dalam persediaan.</p>
        </div>
        <a href="{{ route('barang-masuk.create') }}" class="btn-add-incoming" id="btnTambahBarangMasuk">
            <i class="ph ph-plus bold"></i> Tambah Barang Masuk
        </a>
    </div>

    {{-- Statistics Cards Section --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-primary-soft">
                    <i class="ph ph-arrow-square-down text-primary"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Transaksi</div>
                    <div class="stat-value">{{ number_format($totalTransaksi) }}</div>
                    <div class="stat-subtitle">Transaksi</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-success-soft">
                    <i class="ph ph-package text-success"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Produk Diterima</div>
                    <div class="stat-value">{{ number_format($totalProdukDiterima) }}</div>
                    <div class="stat-subtitle">Pcs</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-warning-soft">
                    <i class="ph ph-coins text-warning"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Nilai Pembelian</div>
                    <div class="stat-value" style="font-size: clamp(1.05rem, 1.2vw, 1.25rem); margin-top: 4px; margin-bottom: 4px; white-space: nowrap; word-break: keep-all;"><span style="font-size: 0.9rem; font-weight: 700; color: #475569; margin-right: 2px;">Rp</span>{{ number_format($totalNilaiPembelian, 0, ',', '.') }}</div>
                    <div class="stat-subtitle">Rupiah</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-purple-soft">
                    <i class="ph ph-house-line text-purple"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Lokasi</div>
                    <div class="stat-value">{{ number_format($totalLokasi) }}</div>
                    <div class="stat-subtitle">Lokasi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Filter Panel --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('barang-masuk.index') }}" class="row g-3" id="formFilterBarangMasuk">
            <div class="col-md-2">
                <label class="filter-label">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}">
            </div>
            <div class="col-md-2">
                <label class="filter-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}">
            </div>
            <div class="col-md-3">
                <label class="filter-label">Produk</label>
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-50"><i class="ph ph-funnel"></i> Filter</button>
                <a href="{{ route('barang-masuk.index') }}" class="btn btn-light btn-sm w-50">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="incoming-table-card">
        <div class="table-responsive">
            <table class="table align-middle incoming-table" id="tableBarangMasuk">
                <thead>
                    <tr>
                        <th class="col-date">Tanggal</th>
                        <th class="col-id">ID_Produk</th>
                        <th class="col-name">Nama Produk</th>
                        <th class="col-supplier">Nama Supplier</th>
                        <th class="col-qty">Jumlah (pcs)</th>
                        <th class="col-price">Harga Satuan (Rp)</th>
                        <th class="col-total">Total (Rp)</th>
                        <th class="col-loc">ID_Lokasi</th>
                        <th class="col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingGoods as $item)
                     <tr>
                        <td class="col-date">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td class="col-id fw-semibold text-muted">
                            <div>{{ $item->product->kode_produk ?? '-' }}</div>
                            <div class="mt-1" style="font-size: 0.72rem; font-weight: 500;">
                                <span class="badge bg-light text-secondary border"><i class="ph ph-stack-simple"></i> {{ $item->batch_code }}</span>
                            </div>
                        </td>
                        <td class="col-name fw-bold">{{ $item->product->nama_produk ?? '-' }}</td>
                        <td class="col-supplier fw-semibold">{{ $item->supplier->nama_supplier ?? '-' }}</td>
                        <td class="col-qty text-center">
                            <span class="fw-bold text-success">+{{ number_format($item->jumlah) }}</span>
                            @if($item->stockBatch)
                                @php $batch = $item->stockBatch; @endphp
                                <div class="mt-1">
                                    @if($batch->jumlah_sisa == 0)
                                        <span class="badge border" style="font-size: 0.72rem; padding: 4px 8px; background-color: #f8fafc; border-color: #e2e8f0 !important; color: #64748b; font-weight: 500;">
                                            Sisa: 0 <span style="color: #ef4444; font-weight: 700; margin-left: 2px;">(Habis)</span>
                                        </span>
                                    @elseif($batch->jumlah_sisa == $batch->jumlah_awal)
                                        <span class="badge border" style="font-size: 0.72rem; padding: 4px 8px; background-color: #f0fdf4; border-color: #bbf7d0 !important; color: #166534; font-weight: 500;">
                                            Sisa: {{ number_format($batch->jumlah_sisa) }} <span style="color: #15803d; font-weight: 700; margin-left: 2px;">(Utuh)</span>
                                        </span>
                                    @else
                                        <span class="badge border" style="font-size: 0.72rem; padding: 4px 8px; background-color: #fffbeb; border-color: #fde68a !important; color: #92400e; font-weight: 500;">
                                            Sisa: {{ number_format($batch->jumlah_sisa) }} <span style="color: #b45309; font-weight: 700; margin-left: 2px;">(Sisa)</span>
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="col-price fw-semibold">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="col-total fw-bold text-dark">Rp {{ number_format($item->jumlah * $item->harga_beli, 0, ',', '.') }}</td>
                        <td class="col-loc"><span class="badge bg-light text-secondary fw-semibold border">{{ $item->id_lokasi ?? '-' }}</span></td>
                        <td class="col-action text-center">
                            <a href="{{ route('barang-masuk.show', $item->id) }}" class="btn btn-sm btn-light border p-1" style="border-radius: 6px;" title="Detail Transaksi">
                                <i class="ph ph-eye text-primary" style="font-size: 1.1rem; vertical-align: middle;"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="ph ph-info fs-1 d-block mb-2"></i>
                            Belum ada data transaksi barang masuk yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Container --}}
        @if($incomingGoods->hasPages() || $incomingGoods->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan {{ $incomingGoods->firstItem() ?? 0 }} - {{ $incomingGoods->lastItem() ?? 0 }} dari {{ $incomingGoods->total() ?? 0 }} data
            </div>
            <div>
                {{ $incomingGoods->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
