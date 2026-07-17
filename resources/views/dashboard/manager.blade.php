@extends('layouts.app')
@section('title', 'Dashboard Manager')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Row -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-primary mb-1">Selamat datang kembali, {{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0">Ulasan operasional, analisis persediaan, dan safety stock</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-success">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-shopping-bag"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Penjualan Bulan Ini</span>
                    <h3 class="stat-value mb-0">{{ number_format($totalPenjualanBulanIni ?? 0, 0, ',', '.') }} pcs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-primary">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-receipt"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Transaksi</span>
                    <h3 class="stat-value mb-0">{{ number_format($jumlahTransaksiBulanIni ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-danger">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-shield-warning"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Harus Dipesan (Order)</span>
                    <h3 class="stat-value mb-0 text-danger">{{ $produkHarusDipesan->count() }} Produk</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card hover-animate bg-white border-0 stat-warning">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-clock" style="color: #1e293b;"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Terakhir Dianalisis</span>
                    <h3 class="stat-value mb-0" style="font-size: 1rem;">
                        {{ $analisisTerbaru->first() ? $analisisTerbaru->first()->created_at->diffForHumans() : '-' }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="row">
        <!-- Left Side (12 Columns) -->
        <div class="col-lg-12">
            <!-- 1. Statistik Penjualan (AJAX Chart) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-chart-bar text-primary me-1"></i> Tren Penjualan Harian & Bulanan</h5>
                    <div class="chart-controls">
                        <!-- Month Filter -->
                        <select id="filterSalesMonth" class="chart-select">
                            <option value="">Sepanjang Tahun</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <!-- Year Filter -->
                        <select id="filterSalesYear" class="chart-select">
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 2. Actionable Urgent Order List -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-danger"><i class="ph ph-bell-ringing text-danger me-1"></i> Produk Mendesak Harus Segera Dipesan</h5>
                    <a href="{{ route('pemesanan-supplier.index') }}" class="btn btn-sm btn-light py-1" style="font-size: 0.8rem">Semua Order</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive border-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Stok Saat Ini</th>
                                    <th class="text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produkHarusDipesan->take(4) as $item)
                                    <tr>
                                        <td><span class="badge bg-light text-dark fw-bold">{{ $item->product->kode_produk ?? '-' }}</span></td>
                                        <td class="fw-semibold">{{ $item->product->nama_produk ?? '-' }}</td>
                                        <td class="text-center fw-bold text-danger">{{ number_format($item->stok_saat_ini, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('pemesanan-supplier.create', ['product_id' => $item->product_id]) }}" class="btn btn-sm btn-danger py-1" style="font-size: 0.78rem">
                                                <i class="ph ph-shopping-cart-simple"></i> Buat Order
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="ph ph-check-circle text-success fs-1 mb-2"></i>
                                            <p class="mb-0 fw-semibold">Seluruh stok produk berada di atas batas aman.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Tables Row: Safety Stock & Updates -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="ph ph-chart-line-up text-primary me-1"></i> Analisis ROP Terbaru</h6>
                            <a href="{{ route('analisis.index') }}" class="btn btn-sm btn-light py-1" style="font-size: 0.78rem">Detail</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive border-0">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">ROP</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($analisisTerbaru->take(5) as $analisis)
                                            <tr>
                                                <td class="fw-semibold">{{ $analisis->product->nama_produk ?? '-' }}</td>
                                                <td class="text-center">{{ number_format($analisis->reorder_point, 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    @if($analisis->status_stok === 'Aman')
                                                        <span class="badge bg-success">Aman</span>
                                                    @elseif($analisis->status_stok === 'Warning')
                                                        <span class="badge bg-warning text-dark">Warning</span>
                                                    @else
                                                        <span class="badge bg-danger">Order</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Belum ada analisis.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 mb-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="ph ph-calendar-blank text-primary me-1"></i> Update Stok Terakhir</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive border-0">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Stok</th>
                                            <th>Diupdate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($produkStokDiperbarui->take(5) as $prod)
                                            <tr>
                                                <td class="fw-semibold">{{ $prod->nama_produk }}</td>
                                                <td class="text-center fw-bold">{{ number_format($prod->stok_saat_ini, 0, ',', '.') }}</td>
                                                <td>{{ $prod->updated_at->diffForHumans() }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Aktivitas Terakhir -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-clock-counter-clockwise text-primary me-1"></i> Aktivitas Terakhir</h5>
                </div>
                <div class="card-body p-0">
                    <div style="padding: 10px 16px 20px 16px;">
                        <div class="activity-timeline">
                            @forelse($aktivitasTerbaru as $act)
                                <div class="activity-item">
                                    <div class="activity-badge bg-{{ $act['warna'] }}">
                                        <i class="{{ $act['icon'] }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-header">
                                            <span class="activity-title">{{ $act['judul'] }}</span>
                                        </div>
                                        <p class="activity-desc">{{ $act['deskripsi'] }}</p>
                                        <div class="activity-meta">
                                            <span class="activity-user">
                                                <i class="ph ph-user"></i> {{ $act['pengguna'] }}
                                            </span>
                                            <span class="activity-time">
                                                <i class="ph ph-calendar-blank"></i> {{ $act['waktu']->translatedFormat('d M Y, H:i') }} ({{ $act['waktu']->diffForHumans() }})
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">Belum ada aktivitas.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 2. Sales Chart AJAX Filter Handler (Dynamic)
    const ctx = document.getElementById('salesChart').getContext('2d');
    let salesChart;

    const filterYear = document.getElementById('filterSalesYear');
    const filterMonth = document.getElementById('filterSalesMonth');

    function fetchSalesData() {
        const year = filterYear.value;
        const month = filterMonth.value;
        let url = `{{ route('dashboard.sales-data') }}?year=${year}`;
        if (month) {
            url += `&month=${month}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(resData => {
                const labels = resData.labels;
                const dataPoints = resData.data;

                if (salesChart) {
                    salesChart.destroy();
                }

                salesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Produk Terjual (pcs)',
                            data: dataPoints,
                            backgroundColor: 'rgba(91, 141, 238, 0.85)',
                            hoverBackgroundColor: 'rgba(59, 109, 217, 0.95)',
                            borderRadius: 6,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(100, 110, 140, 0.08)'
                                },
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error("Error loading sales chart data:", err));
    }

    if (filterYear && filterMonth) {
        filterYear.addEventListener('change', fetchSalesData);
        filterMonth.addEventListener('change', fetchSalesData);
        fetchSalesData();
    }
});
</script>
@endpush
