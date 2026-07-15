@extends('layouts.app')
@section('title', 'Dashboard Owner')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Row -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-primary mb-1">Selamat datang kembali, {{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0">Ringkasan eksekutif, analisis penjualan harian, dan kesehatan operasional</p>
        </div>
    </div>

    <!-- Executive Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card hover-animate bg-white border-0 stat-primary">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-trend-up"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Penjualan Bulan Ini</span>
                    <h3 class="stat-value mb-0">{{ number_format($penjualanBulanIni ?? 0, 0, ',', '.') }} pcs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card hover-animate bg-white border-0 stat-success">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-download-simple"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Barang Masuk Bulan Ini</span>
                    <h3 class="stat-value mb-0">{{ number_format($barangMasukBulanIni ?? 0, 0, ',', '.') }} pcs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card hover-animate bg-white border-0 stat-danger">
                <div class="stat-icon shadow-sm">
                    <i class="ph ph-warning-octagon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Stok Perlu Dipesan (ROP)</span>
                    <h3 class="stat-value mb-0 text-danger">{{ $statusOverview['order'] ?? 0 }} Produk</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="row">
        <!-- Left Side (8 Columns) -->
        <div class="col-lg-8">
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

        <!-- Right Side (4 Columns) -->
        <div class="col-lg-4">
            <!-- 1. Doughnut Chart: Stock Status Overview -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="ph ph-pie-chart text-primary me-1"></i> Komposisi Status Stok</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div style="position: relative; height:220px; width:220px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    @php
                        $totalStokCount = ($statusOverview['aman'] ?? 0) + ($statusOverview['warning'] ?? 0) + ($statusOverview['order'] ?? 0);
                        $persenAman = $totalStokCount > 0 ? round((($statusOverview['aman'] ?? 0) / $totalStokCount) * 100) : 0;
                        $persenWarning = $totalStokCount > 0 ? round((($statusOverview['warning'] ?? 0) / $totalStokCount) * 100) : 0;
                        $persenOrder = $totalStokCount > 0 ? round((($statusOverview['order'] ?? 0) / $totalStokCount) * 100) : 0;
                    @endphp
                    <div class="mt-4 w-100">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="ph ph-circle-fill text-success me-2"></i> Aman</span>
                            <span class="fw-bold">{{ $statusOverview['aman'] ?? 0 }} <span class="text-muted small fw-normal">({{ $persenAman }}%)</span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="ph ph-circle-fill text-warning me-2"></i> Warning</span>
                            <span class="fw-bold">{{ $statusOverview['warning'] ?? 0 }} <span class="text-muted small fw-normal">({{ $persenWarning }}%)</span></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="ph ph-circle-fill text-danger me-2"></i> Order</span>
                            <span class="fw-bold text-danger">{{ $statusOverview['order'] ?? 0 }} <span class="text-danger small fw-semibold">({{ $persenOrder }}%)</span></span>
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
    const ctxSales = document.getElementById('salesChart').getContext('2d');
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

                salesChart = new Chart(ctxSales, {
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

    // 3. Status Doughnut Chart (Static representation)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Aman', 'Warning', 'Order'],
            datasets: [{
                data: [
                    {{ $statusOverview['aman'] ?? 0 }},
                    {{ $statusOverview['warning'] ?? 0 }},
                    {{ $statusOverview['order'] ?? 0 }}
                ],
                backgroundColor: ['#10b981', '#fbbf24', '#f87171'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const value = context.raw;
                            const dataset = context.dataset;
                            const total = dataset.data.reduce((sum, val) => sum + val, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                            return label + value + ' (' + percentage + ')';
                        }
                    }
                }
            },
            cutout: '72%'
        },
        plugins: [{
            id: 'centerText',
            afterDraw: function(chart) {
                var ctx = chart.ctx;
                ctx.restore();
                
                const data = chart.data.datasets[0].data;
                const total = data.reduce((a, b) => a + b, 0);
                
                let text = "0%";
                let subtext = "Aman";
                let color = "#10b981"; // Green
                
                if (total > 0) {
                    if (data[2] > 0) { // Order
                        text = Math.round((data[2] / total) * 100) + "%";
                        subtext = "Perlu Order";
                        color = "#ef4444"; // Red
                    } else if (data[1] > 0) { // Warning
                        text = Math.round((data[1] / total) * 100) + "%";
                        subtext = "Warning";
                        color = "#f59e0b"; // Yellow
                    } else { // Aman
                        text = Math.round((data[0] / total) * 100) + "%";
                        subtext = "Stok Aman";
                        color = "#10b981"; // Green
                    }
                }
                
                // Find center of chart Area
                const xCenter = (chart.chartArea.left + chart.chartArea.right) / 2;
                const yCenter = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                
                // Draw main text
                ctx.font = "bold 1.8rem 'Plus Jakarta Sans', sans-serif";
                ctx.textBaseline = "middle";
                ctx.textAlign = "center";
                ctx.fillStyle = color;
                ctx.fillText(text, xCenter, yCenter - 10);
                
                // Draw subtext
                ctx.font = "600 0.72rem 'Plus Jakarta Sans', sans-serif";
                ctx.fillStyle = "#64748b";
                ctx.fillText(subtext, xCenter, yCenter + 15);
                
                ctx.save();
            }
        }]
    });
});
</script>
@endpush

@push('styles')
<style>

</style>
@endpush
