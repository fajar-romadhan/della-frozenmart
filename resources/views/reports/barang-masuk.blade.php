@extends('layouts.app')
@section('title', 'Laporan Barang Masuk')
@section('page-title', 'Laporan Barang Masuk')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb & Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">Laporan Barang Masuk</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">Laporan Barang Masuk</h4>
        </div>
    </div>



    {{-- Filter Panel --}}
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.barang-masuk') }}" class="row g-3 align-items-end" id="formFilterBarangMasuk">
                {{-- Periode Tanggal --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-bold text-muted">Periode Tanggal</label>
                    <div class="input-group">
                        <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                        <span class="input-group-text bg-light text-muted small">s/d</span>
                        <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                    </div>
                </div>

                {{-- Pencarian Produk --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-bold text-muted">Produk</label>
                    <select name="product_id" class="form-select">
                        <option value="">Semua Produk</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->kode_produk }} - {{ $prod->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian Supplier --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-bold text-muted">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="ph ph-funnel me-2"></i> Filter</button>
                    @if(request()->filled('tanggal_dari') || request()->filled('tanggal_sampai') || request()->filled('product_id') || request()->filled('supplier_id'))
                        <a href="{{ route('laporan.barang-masuk') }}" class="btn btn-outline-secondary"><i class="ph ph-arrow-counter-clockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>


    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-0 pt-3 pb-0">
            <h5 class="mb-0 fw-bold"><i class="ph ph-file-text me-1 text-primary"></i> Data Laporan Barang Masuk</h5>
            <div class="no-print">
                <a href="{{ route('export.barang-masuk.pdf', request()->all()) }}" class="btn btn-sm btn-outline-danger"><i class="ph ph-file-pdf me-1"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableBarangMasuk">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th style="width: 140px;">TANGGAL MASUK</th>
                            <th style="width: 130px;">KODE PRODUK</th>
                            <th>NAMA PRODUK</th>
                            <th>SUPPLIER</th>
                            <th class="text-center" style="width: 110px;">QTY MASUK<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(pcs)</span></th>
                            <th class="text-end" style="width: 130px;">HARGA BELI<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(Rp)</span></th>
                            <th class="text-end" style="width: 140px;">TOTAL NILAI<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(Rp)</span></th>
                            <th style="width: 110px;">NO. BATCH</th>
                            <th class="text-center" style="width: 90px;">LOKASI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedItems as $index => $item)
                            @php
                                $totalBaris = $item->jumlah * ($item->harga_beli ?? 0);
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $paginatedItems->firstItem() + $index }}</td>
                                <td>{{ $item->tanggal_masuk->translatedFormat('d M Y') }}</td>
                                <td><span class="badge bg-light text-dark border fw-bold">{{ $item->product->kode_produk ?? '-' }}</span></td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $item->product->nama_produk ?? '-' }}</span>
                                    @if($item->keterangan)
                                        <div class="text-muted small italic fs-7 mt-1">Ket: {{ $item->keterangan }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                                <td class="text-center text-success fw-bold">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-dark">Rp {{ number_format($totalBaris, 0, ',', '.') }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary font-monospace">{{ $item->batch_code ?? '-' }}</span></td>
                                <td class="text-center"><span class="badge bg-light text-secondary border">{{ $item->id_lokasi ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="ph ph-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data barang masuk untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($paginatedItems->hasPages())
            <div class="card-footer bg-white border-top no-print py-3">
                {{ $paginatedItems->links() }}
            </div>
        @endif
    </div>

    {{-- Bottom Branded Banner --}}
    <div class="text-muted text-center small mt-4 no-print">
        <i class="ph ph-info-semibold me-1"></i> Laporan barang masuk terhitung dinamis dari input form penerimaan gudang dan log faktur yang terimpor.
    </div>
</div>

<style>
    /* Styling for Premium Custom Widgets */
    .bg-slate-light { background-color: #f8fafc; }
    .bg-blue-light { background-color: #f0f9ff; }
    .bg-warning-light { background-color: #fffbeb; }
    .bg-emerald-light { background-color: #f0fdf4; }

    .text-slate-dark { color: #0f172a !important; }
    .text-blue-dark { color: #0369a1 !important; }
    .text-warning-dark { color: #a16207 !important; }
    .text-emerald-dark { color: #15803d !important; }
    .text-muted-dark { color: #475569; font-weight: 500; }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .icon-slate { background-color: #e2e8f0; color: #0f172a; }
    .icon-blue { background-color: #e0f2fe; color: #0369a1; }
    .icon-warning { background-color: #fef3c7; color: #a16207; }
    .icon-emerald { background-color: #dcfce7; color: #15803d; }

    #tableBarangMasuk th {
        background-color: #1e293b;
        color: #ffffff;
        font-family: var(--font-display);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 12px 14px;
        border-bottom: none;
        vertical-align: middle;
    }

    #tableBarangMasuk td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.82rem;
    }

    .fs-7 {
        font-size: 0.75rem !important;
    }
    
    .font-normal {
        font-weight: 400 !important;
    }
</style>
@endsection
