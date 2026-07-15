@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('page-title', 'Barang Keluar')

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
    
    .btn-add-outgoing {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
    
    .btn-add-outgoing:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
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
        border-color: rgba(220, 38, 38, 0.15);
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
    
    .bg-danger-soft {
        background-color: rgba(220, 38, 38, 0.08) !important;
    }
    
    .bg-success-soft {
        background-color: rgba(16, 185, 129, 0.08) !important;
    }
    
    .bg-warning-soft {
        background-color: rgba(245, 158, 11, 0.08) !important;
    }
    
    .bg-orange-soft {
        background-color: rgba(234, 88, 12, 0.08) !important;
    }
    
    .text-orange {
        color: #ea580c !important;
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
        color: #ef4444;
    }
    
    .outgoing-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .outgoing-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    
    .outgoing-table th {
        font-weight: 600;
        font-size: 0.78rem;
        color: #ffffff;
        background-color: #7f1d1d !important;
        border-bottom: none;
        padding: 10px 8px;
        text-align: left;
        line-height: 1.4;
        white-space: normal !important;
    }
    
    .outgoing-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    
    .outgoing-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .outgoing-table tbody tr:hover {
        background-color: #fef2f2;
    }

    /* Column width hints */
    .col-no { width: 40px; text-align: center; }
    .col-date { white-space: nowrap; }

    .outgoing-table th.col-qty, .outgoing-table td.col-qty { text-align: center !important; }
    .outgoing-table th.col-jenis, .outgoing-table td.col-jenis { text-align: center !important; }
    .outgoing-table th.col-action, .outgoing-table td.col-action { text-align: center !important; }
    .outgoing-table th.col-no, .outgoing-table td.col-no { text-align: center !important; }

    /* Badge styling */
    .badge-jenis {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    
    .badge-penjualan {
        background-color: rgba(37, 99, 235, 0.08);
        color: #2563eb;
    }
    
    .badge-rusak {
        background-color: rgba(220, 38, 38, 0.08);
        color: #dc2626;
    }
    
    .badge-kedaluwarsa {
        background-color: rgba(245, 158, 11, 0.08);
        color: #d97706;
    }
    
    .badge-penyesuaian {
        background-color: rgba(100, 116, 139, 0.08);
        color: #64748b;
    }
    
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
        background-color: #dc2626;
        border-color: #dc2626;
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
            <h1 class="page-title-main mb-1">Barang Keluar</h1>
            <p class="page-subtitle mb-0">Kelola data riwayat pengeluaran barang dari persediaan.</p>
        </div>
        <a href="{{ route('barang-keluar.create') }}" class="btn-add-outgoing" id="btnTambahBarangKeluar">
            <i class="ph ph-minus-circle bold"></i> Input Barang Keluar
        </a>
    </div>

    {{-- Statistics Cards Section --}}
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-danger-soft">
                    <i class="ph ph-arrow-square-up text-danger"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Transaksi</div>
                    <div class="stat-value">{{ number_format($totalTransaksi) }}</div>
                    <div class="stat-subtitle">Transaksi keluar</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-success-soft">
                    <i class="ph ph-package text-success"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Produk Keluar</div>
                    <div class="stat-value">{{ number_format($totalProdukKeluar) }}</div>
                    <div class="stat-subtitle">Pcs</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-warning-soft">
                    <i class="ph ph-shopping-cart text-warning"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-title">Total Penjualan</div>
                    <div class="stat-value">{{ number_format($totalPenjualan) }}</div>
                    <div class="stat-subtitle">Pcs terjual</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Filter Panel --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('barang-keluar.index') }}" class="row g-3" id="formFilterBarangKeluar">
            <div class="col-md-2">
                <label class="filter-label">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}" min="2026-01-01">
            </div>
            <div class="col-md-2">
                <label class="filter-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}" min="2026-01-01">
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
                <label class="filter-label">Jenis Keluar</label>
                <select name="jenis_keluar" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisKeluarOptions as $jk)
                        <option value="{{ $jk }}" {{ request('jenis_keluar') == $jk ? 'selected' : '' }}>{{ ucfirst($jk) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-danger btn-sm w-50"><i class="ph ph-funnel"></i> Filter</button>
                <a href="{{ route('barang-keluar.index') }}" class="btn btn-light btn-sm w-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="outgoing-table-card">
        <div class="table-responsive">
            <table class="table align-middle outgoing-table" id="tableBarangKeluar">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th class="col-date">Tanggal</th>
                        <th class="col-product">Produk</th>
                        <th class="col-qty">Jumlah</th>
                        <th class="col-jenis">Jenis</th>
                        <th class="col-user">Oleh</th>
                        <th class="col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outgoingGoods as $i => $item)
                    <tr>
                        <td class="col-no text-muted">{{ $outgoingGoods->firstItem() + $i }}</td>
                        <td class="col-date">{{ \Carbon\Carbon::parse($item->tanggal_keluar)->format('d/m/Y') }}</td>
                        <td class="col-product fw-bold">
                            <div>{{ $item->product->nama_produk ?? '-' }}</div>
                            @if($item->outgoingGoodDetails && $item->outgoingGoodDetails->count() > 0)
                                <div class="mt-1 d-flex flex-wrap gap-1 align-items-center" style="font-weight: 500;">
                                    @foreach($item->outgoingGoodDetails as $detail)
                                        <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.72rem; padding: 2px 6px;" title="Diambil dari Batch ini">
                                            <i class="ph ph-stack-simple text-danger" style="vertical-align: middle;"></i> 
                                            {{ $detail->stockBatch->batch_code ?? 'Batch' }} 
                                            <span class="text-danger fw-bold">(-{{ number_format($detail->jumlah_diambil) }} pcs)</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="col-qty"><span class="text-danger fw-bold">-{{ number_format($item->jumlah) }}</span></td>
                        <td class="col-jenis">
                            @php 
                                $jenisIcons = [
                                    'penjualan' => 'ph-shopping-cart',
                                    'rusak' => 'ph-x-circle',
                                    'kedaluwarsa' => 'ph-clock-countdown',
                                    'penyesuaian' => 'ph-arrows-clockwise'
                                ];
                            @endphp
                            <span class="badge-jenis badge-{{ $item->jenis_keluar }}">
                                <i class="ph {{ $jenisIcons[$item->jenis_keluar] ?? 'ph-circle' }}"></i>
                                {{ ucfirst($item->jenis_keluar) }}
                            </span>
                        </td>
                        <td class="col-user text-muted">{{ $item->user->name ?? '-' }}</td>
                        <td class="col-action text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('barang-keluar.show', $item) }}" class="btn btn-sm btn-light border p-1" style="border-radius: 6px;" title="Detail Transaksi">
                                    <i class="ph ph-eye text-danger" style="font-size: 1.1rem; vertical-align: middle;"></i>
                                </a>
                                @if(auth()->user()->role === 'admin')
                                <form action="{{ route('barang-keluar.destroy', $item) }}" method="POST" class="d-inline form-hapus-keluar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light border p-1 btn-hapus-keluar"
                                        style="border-radius: 6px;"
                                        title="Hapus Transaksi"
                                        data-nama="{{ $item->product->nama_produk ?? '-' }}"
                                        data-jumlah="{{ number_format($item->jumlah) }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal_keluar)->format('d/m/Y') }}">
                                        <i class="ph ph-trash text-danger" style="font-size: 1.1rem; vertical-align: middle;"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="ph ph-info fs-1 d-block mb-2"></i>
                            Belum ada data transaksi barang keluar yang cocok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Container --}}
        @if($outgoingGoods->hasPages() || $outgoingGoods->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan {{ $outgoingGoods->firstItem() ?? 0 }} - {{ $outgoingGoods->lastItem() ?? 0 }} dari {{ $outgoingGoods->total() ?? 0 }} data
            </div>
            <div>
                {{ $outgoingGoods->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapusKeluar" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 40px; height: 40px; background: #fee2e2; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="ph ph-trash text-danger" style="font-size: 1.3rem;"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-danger mb-0" id="modalHapusLabel">Hapus Transaksi Barang Keluar?</h5>
                </div>
            </div>
            <div class="modal-body px-4 pb-0">
                <p class="text-muted mb-2" style="font-size: 0.9rem;">Anda akan menghapus transaksi berikut:</p>
                <div class="rounded p-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="fw-semibold" id="modalNamaProduk" style="font-size: 0.95rem;"></div>
                    <small class="text-muted" id="modalDetailHapus"></small>
                </div>
                <div class="alert alert-warning d-flex gap-2 align-items-start py-2 px-3" style="font-size: 0.82rem; border-radius: 8px;">
                    <i class="ph ph-warning-circle mt-1 flex-shrink-0"></i>
                    <span><strong>Stok akan dikembalikan otomatis</strong> ke batch FIFO asal. Tindakan ini akan tercatat di Log Aktivitas.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-2 pb-4 px-4 gap-2">
                <button type="button" class="btn btn-light fw-semibold px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger fw-semibold px-4" id="btnKonfirmasiHapus">
                    <i class="ph ph-trash me-1"></i>Ya, Hapus & Kembalikan Stok
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let formHapusTarget = null;

    document.querySelectorAll('.btn-hapus-keluar').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const nama    = this.dataset.nama;
            const jumlah  = this.dataset.jumlah;
            const tanggal = this.dataset.tanggal;
            formHapusTarget = this.closest('form');

            document.getElementById('modalNamaProduk').textContent = nama;
            document.getElementById('modalDetailHapus').textContent = tanggal + ' · ' + jumlah + ' pcs';

            const modal = new bootstrap.Modal(document.getElementById('modalHapusKeluar'));
            modal.show();
        });
    });

    document.getElementById('btnKonfirmasiHapus').addEventListener('click', function() {
        if (formHapusTarget) {
            formHapusTarget.submit();
        }
    });
</script>
@endpush

@endsection
