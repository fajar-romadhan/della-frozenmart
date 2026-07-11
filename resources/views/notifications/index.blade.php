@extends('layouts.app')
@section('title', 'Notifikasi Stok')
@section('page-title', 'Notifikasi Stok')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="mb-4">
        <h4 class="fw-bold text-primary mb-1">Notifikasi Stok</h4>
        <p class="text-muted mb-0">Menampilkan produk dengan status Warning, Order, dan Expired yang memerlukan perhatian.</p>
    </div>

    {{-- Stats Cards Row --}}
    <div class="row mb-4">
        {{-- Card 1: Total Notifikasi --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm card-total-notif">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new icon-total-notif me-3">
                        <i class="ph ph-bell"></i>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Total Notifikasi</span>
                        <h3 class="fw-bold mb-0 mt-1 text-primary-dark">{{ $totalNotif }} <span class="fs-6 fw-normal text-muted">Produk</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Status Warning --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm card-status-warning">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new icon-status-warning me-3">
                        <i class="ph ph-warning"></i>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Status Warning</span>
                        <h3 class="fw-bold mb-0 mt-1 text-warning-dark">{{ $totalWarning }} <span class="fs-6 fw-normal text-muted">Produk</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Status Order --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm card-status-order">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new icon-status-order me-3">
                        <i class="ph ph-x-circle"></i>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Status Order</span>
                        <h3 class="fw-bold mb-0 mt-1 text-danger-dark">{{ $totalOrder }} <span class="fs-6 fw-normal text-muted">Produk</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Status Expired --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm card-status-expired">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new icon-status-expired me-3">
                        <i class="ph ph-calendar-blank"></i>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Status Expired</span>
                        <h3 class="fw-bold mb-0 mt-1 text-purple-dark">{{ $totalExpired }} <span class="fs-6 fw-normal text-muted">Produk</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableNotifikasiStok">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">NO</th>
                            <th style="width: 140px;">KODE PRODUK</th>
                            <th>NAMA PRODUK</th>
                            <th class="text-center" style="width: 160px;">STOK SAAT INI<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(pcs)</span></th>
                            <th class="text-center" style="width: 180px;">SAFETY STOCK (SS)<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(pcs)</span></th>
                            <th class="text-center" style="width: 180px;">REORDER POINT (ROP)<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(pcs)</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notificationProducts as $index => $item)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold">{{ $item['kode_produk'] }}</span>
                                </td>
                                <td class="fw-bold text-dark">{{ $item['nama_produk'] }}</td>
                                <td class="text-center">
                                    @if($item['status'] === 'Warning')
                                        <span class="text-warning-custom fw-bold fs-5">{{ number_format($item['stok_saat_ini'], 0, ',', '.') }}</span>
                                    @elseif($item['status'] === 'Order')
                                        <span class="text-danger-custom fw-bold fs-5">{{ number_format($item['stok_saat_ini'], 0, ',', '.') }}</span>
                                    @elseif($item['status'] === 'Expired')
                                        <span class="text-purple fw-bold fs-5">{{ number_format($item['stok_saat_ini'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-dark fw-bold fs-5">{{ number_format($item['stok_saat_ini'], 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted fw-semibold">
                                    {{ $item['safety_stock'] !== null ? number_format($item['safety_stock'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-center text-muted fw-semibold">
                                    {{ $item['reorder_point'] !== null ? number_format($item['reorder_point'], 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="ph ph-check-circle text-success fs-1 d-block mb-3"></i>
                                    <h5 class="fw-bold">Tidak ada notifikasi stok</h5>
                                    <p class="mb-0 text-muted">Semua produk berada dalam kondisi aman.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Bottom Info Banner --}}
    <div class="alert alert-info-custom border-0 d-flex align-items-center mb-4 shadow-sm" role="alert">
        <i class="ph ph-info-semibold text-primary fs-4 me-3"></i>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span>Notifikasi diperbarui otomatis setelah proses analisa persediaan terakhir dilakukan.</span> 
            <span class="fw-semibold">Terakhir dihitung:</span> 
            <span class="badge bg-teal-badge text-teal-dark py-1 px-2 fw-semibold" style="font-size: 0.78rem;">
                {{ $terakhirDihitung ? $terakhirDihitung->translatedFormat('d M Y H:i') . ' WIB' : 'Belum pernah dihitung' }}
            </span>
        </div>
    </div>
</div>

<style>
    /* Custom Styling for Premium Feel */
    .bg-purple-soft {
        background-color: #faf5ff;
    }
    .text-purple {
        color: #8b5cf6 !important;
    }
    .text-purple-dark {
        color: #6b21a8 !important;
    }
    
    .text-muted-dark {
        color: #475569;
        font-weight: 500;
    }
    .text-primary-dark {
        color: #1e40af !important;
    }
    .text-warning-dark {
        color: #854d0e !important;
    }
    .text-danger-dark {
        color: #991b1b !important;
    }

    .card-total-notif {
        background-color: #eff6ff !important;
        border: 1px solid #dbeafe !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-status-warning {
        background-color: #fffbeb !important;
        border: 1px solid #fde68a !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-status-order {
        background-color: #fef2f2 !important;
        border: 1px solid #fee2e2 !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-status-expired {
        background-color: #faf5ff !important;
        border: 1px solid #f3e8ff !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-total-notif:hover, .card-status-warning:hover, .card-status-order:hover, .card-status-expired:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05) !important;
    }

    .stat-icon-new {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .icon-total-notif {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .icon-status-warning {
        background-color: #fde68a;
        color: #854d0e;
    }
    .icon-status-order {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .icon-status-expired {
        background-color: #f3e8ff;
        color: #6b21a8;
    }
    
    #tableNotifikasiStok th {
        background-color: #1e293b !important;
        color: #ffffff !important;
        font-family: var(--font-display);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        padding: 10px 8px;
        border-bottom: 2px solid #e2e8f0;
        white-space: normal !important;
    }
    
    #tableNotifikasiStok td {
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.78rem;
    }
    
    .text-warning-custom {
        color: #d97706 !important;
    }
    .text-danger-custom {
        color: #ef4444 !important;
    }
    
    .alert-info-custom {
        background-color: #f0fdfa;
        color: #0f766e;
        border-radius: var(--radius-md);
        padding: 16px 20px;
    }
    .alert-info-custom i {
        color: #0d9488 !important;
    }
    .bg-teal-badge {
        background-color: #ccfbf1;
    }
    .text-teal-dark {
        color: #115e59;
    }
    
    .font-normal {
        font-weight: 400 !important;
    }
</style>
@endsection
