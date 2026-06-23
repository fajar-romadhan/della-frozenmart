@extends('layouts.app')
@section('title', 'Detail Supplier')
@section('page-title', 'Detail Supplier')

@section('content')
<style>
    .badge-status-active {
        background-color: #e6fcf5 !important;
        color: #0ca678 !important;
        font-weight: 600;
        font-size: 0.72rem;
        padding: 4px 10px;
        border-radius: 12px;
        display: inline-block;
    }
    
    .badge-status-inactive {
        background-color: #fff5f5 !important;
        color: #f03e3e !important;
        font-weight: 600;
        font-size: 0.72rem;
        padding: 4px 10px;
        border-radius: 12px;
        display: inline-block;
    }

    .premium-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .premium-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #ffffff !important;
        background-color: #1e293b !important; /* Premium Navy Blue */
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 16px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: normal !important;
    }
    .premium-table td {
        font-size: 0.78rem;
        padding: 10px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .premium-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .card-header-premium {
        background-color: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding: 14px 20px;
    }
    .card-header-premium h5 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
    }
</style>

<div class="container-fluid py-2">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary me-3" style="border-radius: 8px;"><i class="ph ph-arrow-left"></i></a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-1" style="color: #0f172a; font-family: var(--font-display);">{{ $supplier->nama_supplier }}</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                @if($supplier->status_aktif)
                    <span class="badge badge-status-active">Aktif</span>
                @else
                    <span class="badge badge-status-inactive">Nonaktif</span>
                @endif
                · Pemasok Della Frozen Mart
            </p>
        </div>
        <a href="{{ route('supplier.edit', $supplier) }}" class="btn btn-warning fw-bold" style="border-radius: 8px; font-size: 0.88rem;"><i class="ph ph-pencil-simple"></i> Edit</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size:0.75rem; letter-spacing:1px;">Informasi Supplier</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nama Supplier</small>
                        <span class="fw-semibold">{{ $supplier->nama_supplier }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Kontak Person</small>
                        <span class="fw-semibold">{{ $supplier->kontak ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">No. Telepon</small>
                        <span class="fw-semibold text-primary">{{ $supplier->telepon ?? '-' }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <span class="fw-semibold">{{ $supplier->email ?? '-' }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Alamat</small>
                        <span class="text-muted">{{ $supplier->alamat ?? '-' }}</span>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted d-block">Keterangan</small>
                        <span class="text-muted">{{ $supplier->keterangan ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card h-100" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08); overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);">
                <div class="card-header-premium">
                    <h5>
                        <i class="ph ph-arrow-circle-down me-2 text-success" style="font-size: 1.2rem; vertical-align: middle;"></i>
                        RIWAYAT BARANG MASUK TERBARU
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle premium-table">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>PRODUK</th>
                                    <th class="text-end">JUMLAH</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIncoming as $inc)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($inc->tanggal_masuk)->format('d/m/Y') }}</td>
                                    <td class="fw-semibold">{{ $inc->product->nama_produk ?? '-' }}</td>
                                    <td class="text-end text-success fw-bold">+{{ number_format($inc->jumlah) }} {{ $inc->satuan ?? '' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="ph ph-info fs-3 d-block mb-2"></i>
                                        Belum ada riwayat barang masuk dari supplier ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
