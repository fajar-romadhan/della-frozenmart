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
                <label class="filter-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="sesuai" {{ request('status') == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                    <option value="selisih" {{ request('status') == 'selisih' ? 'selected' : '' }}>Selisih</option>
                </select>
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
                        <th class="col-status">Status</th>
                        <th class="col-keterangan">Keterangan</th>
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
                        <td class="col-status">
                            @if($opname->selisih == 0)
                                <span class="badge-status badge-sesuai">
                                    <i class="ph ph-check-circle"></i> Sesuai
                                </span>
                            @elseif($opname->selisih < 0)
                                <span class="badge-status badge-kurang">
                                    <i class="ph ph-arrow-down"></i> Kurang
                                </span>
                            @else
                                <span class="badge-status badge-lebih">
                                    <i class="ph ph-arrow-up"></i> Lebih
                                </span>
                            @endif
                        </td>
                        <td class="col-keterangan text-muted">{{ Str::limit($opname->keterangan, 30) ?? '-' }}</td>
                        <td class="col-user text-muted">{{ $opname->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
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
@endsection
