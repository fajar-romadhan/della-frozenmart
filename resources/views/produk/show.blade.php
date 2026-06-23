@extends('layouts.app')
@section('title', 'Detail Produk')
@section('page-title', 'Detail Produk')

@section('content')
<style>
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
        padding: 10px 8px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: normal !important;
    }
    .premium-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
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
        <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary me-3" style="border-radius: 8px;"><i class="bi bi-arrow-left"></i></a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-1" style="color: #0f172a; font-family: var(--font-display);">{{ $produk->nama_produk }}</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ $produk->kode_produk }} · {{ $produk->category->nama_kategori ?? '-' }}</p>
        </div>
        <a href="{{ route('produk.edit', $produk) }}" class="btn btn-warning fw-bold" style="border-radius: 8px; font-size: 0.88rem;"><i class="bi bi-pencil"></i> Edit</a>
    </div>

    {{-- Info Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Stok Saat Ini</span>
                    <span class="stat-value">{{ number_format($produk->stok_saat_ini) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Stok Minimum</span>
                    <span class="stat-value">{{ number_format($produk->stok_minimum) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-rulers"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Satuan</span>
                    <span class="stat-value">{{ $produk->satuan }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card {{ $produk->status_aktif ? 'stat-success' : 'stat-danger' }}">
                <div class="stat-icon"><i class="bi bi-{{ $produk->status_aktif ? 'check-circle' : 'x-circle' }}"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Status</span>
                    <span class="stat-value">{{ $produk->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Stock Batches --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08); overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);">
                <div class="card-header-premium"><h5><i class="bi bi-layers me-2 text-primary"></i>BATCH STOK</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 premium-table">
                            <thead>
                                <tr>
                                    <th>KODE BATCH</th>
                                    <th>TANGGAL MASUK</th>
                                    <th>STOK AWAL</th>
                                    <th>STOK SISA</th>
                                    <th>KEDALUWARSA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockBatches as $batch)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $batch->batch_code }}</td>
                                    <td>{{ \Carbon\Carbon::parse($batch->tanggal_masuk)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($batch->jumlah_awal) }}</td>
                                    <td><span class="fw-bold {{ $batch->jumlah_sisa > 0 ? 'text-success' : 'text-muted' }}">{{ number_format($batch->jumlah_sisa) }}</span></td>
                                    <td>{{ $batch->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($batch->tanggal_kedaluwarsa)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada stock batch.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($stockBatches->hasPages())
                <div class="card-footer bg-white border-top p-3">{{ $stockBatches->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Recent Incoming --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08); overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);">
                <div class="card-header-premium"><h5><i class="bi bi-box-arrow-in-down me-2 text-success"></i>RIWAYAT BARANG MASUK</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 premium-table">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>JUMLAH MASUK</th>
                                    <th>SUPPLIER</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIncoming as $inc)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($inc->tanggal_masuk)->format('d/m/Y') }}</td>
                                    <td class="fw-bold text-success">+{{ number_format($inc->jumlah) }}</td>
                                    <td>{{ $inc->supplier->nama_supplier ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat.</td></tr>
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
