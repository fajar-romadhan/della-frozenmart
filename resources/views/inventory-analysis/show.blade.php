@extends('layouts.app')
@section('title', 'Detail Analisis')
@section('page-title', 'Detail Analisis Persediaan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('analisis.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-1">Analisis: {{ $product->nama_produk }}</h4>
            <p class="text-muted mb-0">{{ $product->kode_produk }} · Terakhir dianalisis: {{ $analysis->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Stok Saat Ini</span>
                    <span class="stat-value">{{ number_format($analysis->stok_saat_ini) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Safety Stock</span>
                    <span class="stat-value">{{ rtrim(rtrim(number_format($analysis->safety_stock, 2, ',', '.'), '0'), ',') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Reorder Point</span>
                    <span class="stat-value">{{ rtrim(rtrim(number_format($analysis->reorder_point, 2, ',', '.'), '0'), ',') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card {{ $analysis->status_stok == 'Aman' ? 'stat-success' : ($analysis->status_stok == 'Warning' ? 'stat-warning' : 'stat-danger') }}">
                <div class="stat-icon"><i class="bi bi-{{ $analysis->status_stok == 'Aman' ? 'check-circle' : ($analysis->status_stok == 'Warning' ? 'exclamation-triangle' : 'x-circle') }}"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Status</span>
                    <span class="stat-value">{{ $analysis->status_stok }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title"><i class="bi bi-calculator me-2"></i>Parameter Perhitungan</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted">Rata-rata Penjualan/Hari</td><td class="fw-bold">{{ rtrim(rtrim(number_format($analysis->rata_rata_penjualan, 4, ',', '.'), '0'), ',') }}</td></tr>
                        <tr><td class="text-muted">Std. Dev. Penjualan</td><td class="fw-bold">{{ rtrim(rtrim(number_format($analysis->standar_deviasi, 4, ',', '.'), '0'), ',') }}</td></tr>
                        <tr><td class="text-muted">Lead Time (Hari)</td><td class="fw-bold">{{ $analysis->lead_time ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Service Level</td><td class="fw-bold">{{ ($analysis->service_level ?? 0.95) * 100 }}%</td></tr>
                        <tr><td class="text-muted">Safety Stock</td><td class="fw-bold text-success">{{ rtrim(rtrim(number_format($analysis->safety_stock, 2, ',', '.'), '0'), ',') }}</td></tr>
                        <tr><td class="text-muted">Reorder Point (ROP)</td><td class="fw-bold text-warning">{{ rtrim(rtrim(number_format($analysis->reorder_point, 2, ',', '.'), '0'), ',') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title"><i class="bi bi-lightbulb me-2 text-warning"></i>Rekomendasi</h5></div>
                <div class="card-body">
                    @if($analysis->status_stok == 'Aman')
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Stok Aman.</strong> Stok saat ini ({{ number_format($analysis->stok_saat_ini) }}) berada di atas Reorder Point ({{ rtrim(rtrim(number_format($analysis->reorder_point, 2, ',', '.'), '0'), ',') }}). Tidak perlu melakukan pemesanan saat ini.
                        </div>
                    @elseif($analysis->status_stok == 'Warning')
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong> Stok mendekati Reorder Point. Pertimbangkan untuk segera melakukan pemesanan.
                        </div>
                        <a href="{{ route('pemesanan-supplier.create', ['product_id' => $product->id]) }}" class="btn btn-warning"><i class="bi bi-cart-plus me-2"></i>Buat Pemesanan</a>
                    @else
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>Segera Pesan!</strong> Stok sudah di bawah Safety Stock. Lakukan pemesanan ke supplier sekarang.
                        </div>
                        <a href="{{ route('pemesanan-supplier.create', ['product_id' => $product->id]) }}" class="btn btn-danger"><i class="bi bi-cart-plus me-2"></i>Buat Pemesanan Sekarang</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
