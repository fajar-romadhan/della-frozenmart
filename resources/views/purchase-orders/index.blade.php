@extends('layouts.app')
@section('title', 'Laporan Pemesanan Produk')
@section('page-title', 'Laporan Pemesanan Produk ke Supplier')

@section('content')
<style>
    /* Premium Table Styling */
    .po-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .po-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .po-table th {
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
    .po-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .po-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .po-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a; font-family: var(--font-display);">Laporan Pemesanan Produk</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Daftar transaksi pemesanan barang kepada supplier</p>
        </div>
        @if(auth()->user()->role !== 'owner')
        <a href="{{ route('pemesanan-supplier.create') }}" class="btn btn-primary fw-bold" style="border-radius: 8px; font-size: 0.88rem; padding: 10px 20px;"><i class="bi bi-plus-lg"></i> Buat Pemesanan</a>
        @endif
    </div>

    <div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08);">
        <div class="card-body">
            <form method="GET" action="{{ route('pemesanan-supplier.index') }}" class="row g-3">
                <div class="col-md-9">
                    <select name="status_pemesanan" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status_pemesanan') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="dipesan" {{ request('status_pemesanan') == 'dipesan' ? 'selected' : '' }}>Dipesan</option>
                        <option value="diterima" {{ request('status_pemesanan') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="dibatalkan" {{ request('status_pemesanan') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 8px;"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="po-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 po-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>TANGGAL</th>
                        <th>PRODUK</th>
                        <th>SUPPLIER</th>
                        <th class="text-center">JUMLAH</th>
                        <th>STATUS</th>
                        <th>OLEH</th>
                        <th class="text-center" style="width: 80px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $i => $po)
                    <tr>
                        <td class="text-center text-muted fw-semibold">{{ $purchaseOrders->firstItem() + $i }}</td>
                        <td>{{ \Carbon\Carbon::parse($po->tanggal_pemesanan)->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $po->product->nama_produk ?? '-' }}</td>
                        <td>{{ $po->supplier->nama_supplier ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ number_format($po->jumlah_pesan) }}</td>
                        <td>
                            @php $statusColors = ['draft'=>'secondary','dipesan'=>'primary','diterima'=>'success','dibatalkan'=>'danger']; @endphp
                            <span class="badge bg-{{ $statusColors[$po->status_pemesanan] ?? 'secondary' }}">{{ ucfirst($po->status_pemesanan) }}</span>
                        </td>
                        <td class="text-muted">{{ $po->user->name ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('pemesanan-supplier.show', $po) }}" class="btn btn-sm btn-outline-primary fw-semibold px-3" style="border-radius: 6px; font-size: 0.78rem;"><i class="bi bi-eye"></i> Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-cart fs-1 d-block mb-2"></i>Belum ada pemesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchaseOrders->hasPages())
        <div class="card-footer bg-white border-top p-3">{{ $purchaseOrders->links() }}</div>
        @endif
    </div>
</div>
@endsection
