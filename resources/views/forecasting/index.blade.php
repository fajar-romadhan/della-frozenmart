@extends('layouts.app')
@section('title', 'Peramalan Stok (Forecasting)')
@section('page-title', 'Peramalan Stok')

@section('content')
<style>
    /* Styling Premium Peramalan */
    .forecasting-container {
        font-family: var(--font-display, 'Inter', sans-serif);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .glass-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.04);
    }

    .form-label-premium {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-premium {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #0f172a;
        transition: all 0.2s ease;
    }

    .form-control-premium:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        outline: none;
    }

    .btn-premium {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-premium:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
    }

    .btn-premium:active {
        transform: translateY(1px);
    }

    .btn-history-back {
        background: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-history-back:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
    }

    /* Stats summary chips */
    .stat-chip-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-chip {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.07);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
    }

    .stat-chip-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .stat-chip-details {
        display: flex;
        flex-direction: column;
    }

    .stat-chip-label {
        font-size: 0.73rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-chip-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2px;
    }

    /* Math formulas block styling */
    .formula-card {
        background: #f8fafc;
        border-left: 4px solid #3b82f6;
        border-radius: 8px;
        padding: 18px;
        margin-top: 20px;
    }

    .formula-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .formula-math {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 600;
        font-size: 0.88rem;
        color: #2563eb;
        background: #ffffff;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        display: block;
        margin: 6px 0;
        overflow-x: auto;
    }

    .result-badge {
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
    }

    /* Loading skeleton */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 40px 0;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
        color: #2563eb;
    }

    /* High-contrast custom badges */
    .badge-periode-forecast {
        background-color: #e0e7ff !important; /* Indigo pastel */
        color: #4f46e5 !important; /* Indigo text */
        font-weight: 600;
        font-size: 0.72rem;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
        text-transform: uppercase;
    }
    .badge-musim-forecast {
        background-color: #fef3c7 !important; /* Amber pastel */
        color: #d97706 !important; /* Amber text */
        font-weight: 600;
        font-size: 0.72rem;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
        text-transform: uppercase;
    }
</style>

<div class="container-fluid forecasting-container">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="btn-history-back me-3">
            <i class="ph ph-arrow-left"></i> Kembali
        </a>
        <div class="flex-grow-1">
            <h1 class="fw-bold mb-1 text-slate-800" style="font-size: 1.8rem; font-weight: 800;">Peramalan Kebutuhan Stok (Forecasting)</h1>
            <p class="text-muted mb-0">Modul Peramalan Rantai Pasok Terkoreksi Lost Sales & Seasonal Index (Tahun Depan {{ $forecastYear }})</p>
        </div>
    </div>

    {{-- Form Setup --}}
    <div class="glass-card">
        <h5 class="fw-bold mb-3 text-slate-800" style="font-size: 1.05rem;"><i class="ph ph-sliders-horizontal text-primary"></i> Parameter Peramalan</h5>
        <form id="formForecasting">
            @csrf
            <div class="row g-3">
                {{-- Product Select --}}
                <div class="col-md-4">
                    <label class="form-label-premium" for="product_id">Pilih Produk</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text" style="background-color: #f8fafc; border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px;">
                            <i class="ph ph-magnifying-glass text-muted"></i>
                        </span>
                        <input type="text" id="search_product" class="form-control" style="border-left: none; border-color: #cbd5e1; border-radius: 0 8px 8px 0; font-size: 0.85rem; padding: 10px 14px;" placeholder="Cari nama/kode produk...">
                    </div>
                    <select class="form-select form-control-premium" id="product_id" name="product_id" required>
                        <option value="" disabled selected>-- Pilih Produk Frozen Food --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_produk }} ({{ $p->kode_produk }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Growth Rate --}}
                <div class="col-md-2" style="min-width: 130px;">
                    <label class="form-label-premium" for="growth_rate">Pertumbuhan Proyeksi (%)</label>
                    <input type="number" class="form-control form-control-premium" id="growth_rate" name="growth_rate" value="10" min="0" max="100" required>
                </div>

                {{-- Lead Time --}}
                <div class="col-md-2" style="min-width: 130px;">
                    <label class="form-label-premium" for="lead_time">Lead Time Supplier (Hari)</label>
                    <input type="number" class="form-control form-control-premium" id="lead_time" name="lead_time" value="3" min="1" max="30" required>
                </div>

                {{-- Service Level --}}
                <div class="col-md-2" style="min-width: 140px;">
                    <label class="form-label-premium" for="service_level">Service Level Target</label>
                    <select class="form-select form-control-premium" id="service_level" name="service_level" required>
                        <option value="90">90% (Z = 1.28)</option>
                        <option value="95" selected>95% (Z = 1.65)</option>
                        <option value="99">99% (Z = 2.33)</option>
                    </select>
                </div>

                {{-- Button Submit --}}
                <div class="col-md-2 d-flex align-items-end" style="min-width: 200px;">
                    <button type="submit" class="btn-premium w-100 justify-content-center" id="btnCalculate">
                        <i class="ph ph-lightning"></i> Mulai Peramal
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Card Tabel Perbandingan 10 Produk Utama --}}
    <div class="glass-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-slate-800" style="font-size: 1.05rem;">
                <i class="ph ph-scales text-primary"></i> Tabel Perbandingan Proyeksi 10 Produk Utama
            </h5>
            <span class="badge bg-primary-light text-primary px-3 py-2" style="border-radius: 6px; font-weight: 600; font-size: 0.75rem;">
                Parameter Standar: Pertumbuhan 10%, Lead Time 3 Hari, Service Level 95%
            </span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Produk</th>
                        <th class="text-center">Periode</th>
                        <th class="text-center">Musim</th>
                        <th class="text-end">Data Tahun Sebelumnya (2026)</th>
                        <th class="text-end text-success fw-bold" style="background-color: #f0fdf4;">Hasil Ramalan (2027)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comparisonData as $index => $item)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-slate-800">
                                <div>{{ $item['nama'] }}</div>
                                <span style="background: #f1f5f9; color: #475569; padding: 2px 7px; border-radius: 5px; font-size: 0.7rem; font-weight: 600;">{{ $item['kode'] }}</span>
                            </td>
                             <td class="text-center">
                                <span class="badge-periode-forecast">Tahun Depan (2027)</span>
                             </td>
                             <td class="text-center">
                                <span class="badge-musim-forecast">Seasonal Index (Aktif)</span>
                             </td>
                            <td class="text-end fw-medium">{{ number_format($item['sales_total']) }} pcs</td>
                            <td class="text-end text-success fw-bold" style="background-color: #f0fdf4;">{{ number_format($item['rec_total']) }} pcs</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Loading Spinner --}}
    <div class="loading-spinner" id="loadingArea">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-3 fw-bold">Sistem sedang merangkai basis data transaksi, menghitung Lost Sales & indeks musiman...</p>
    </div>

    {{-- Result Section (Hidden initially) --}}
    <div id="resultArea" style="display: none;">
        {{-- Stat Row --}}
        <div class="stat-chip-row">
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(37, 99, 235, 0.08); color: #2563eb;">
                    <i class="ph ph-shopping-bag"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Total Terjual ({{ $historicalYear }})</span>
                    <span class="stat-chip-value" id="statSales">0 pcs</span>
                </div>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                    <i class="ph ph-chart-line-up"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Koreksi Permintaan</span>
                    <span class="stat-chip-value" id="statCorrected">0 pcs</span>
                </div>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                    <i class="ph ph-wave-sine"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Deviasi Fluktuasi (σ)</span>
                    <span class="stat-chip-value" id="statStdDev">0</span>
                </div>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(239, 68, 68, 0.08); color: #ef4444;">
                    <i class="ph ph-shield-check"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Safety Stock Global</span>
                    <span class="stat-chip-value" id="statSafetyStock">0 pcs</span>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Chart Card --}}
            <div class="col-lg-8">
                <div class="glass-card" style="height: calc(100% - 24px);">
                    <h5 class="fw-bold mb-3 text-slate-800" style="font-size: 1.02rem;"><i class="ph ph-chart-bar-horizontal text-primary"></i> Tren Penjualan {{ $historicalYear }} vs Proyeksi {{ $forecastYear }}</h5>
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="forecastChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Explanation & Method details --}}
            <div class="col-lg-4">
                <div class="glass-card" style="height: calc(100% - 24px); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h5 class="fw-bold mb-2 text-slate-800" style="font-size: 1.02rem;"><i class="ph ph-book-open text-primary"></i> Latar Belakang Metode</h5>
                        <p class="text-muted small" style="line-height: 1.45;">
                            Modul ini menerapkan <strong>Demand Unconstraining</strong> untuk memulihkan bias data penjualan akibat kekosongan stok, dikombinasikan dengan <strong>Seasonal Indexing</strong> dan perhitungan <strong>Safety Stock Statistik</strong>.
                        </p>
                        
                        <div class="formula-card">
                            <span class="formula-title">1. Estimasi Permintaan Riil</span>
                            <span class="formula-math">D_m = Sales_m + (ADR_m * Stockout_Days_m)</span>

                            <span class="formula-title" style="margin-top: 8px; display: block;">2. Indeks Musiman (Seasonal)</span>
                            <span class="formula-math">SI_m = D_m / Average_Monthly_Demand</span>

                            <span class="formula-title" style="margin-top: 8px; display: block;">3. Safety Stock Statistik</span>
                            <span class="formula-math">SS = Z * σ_D * sqrt(LeadTime / 30)</span>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <div class="p-3 rounded" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                            <i class="ph ph-info-semibold"></i> Angka <strong>Rekomendasi</strong> memproyeksikan stok optimal tahun depan guna menekan risiko lost sales di bawah 5%.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Details --}}
        <div class="glass-card mt-4">
            <h5 class="fw-bold mb-3 text-slate-800" style="font-size: 1.02rem;"><i class="ph ph-table text-primary"></i> Tabel Rincian Perhitungan Per-Bulan</h5>
            <div class="table-responsive">
                <table class="table align-middle" id="tableResult">
                    <thead class="table-light">
                        <tr>
                            <th>Bulan</th>
                            <th class="text-center">Penjualan Aktual ({{ $historicalYear }})</th>
                            <th class="text-center">Hari Stok Kosong</th>
                            <th class="text-center">Lost Sales Terbuang</th>
                            <th class="text-center">Permintaan Riil ({{ $historicalYear }})</th>
                            <th class="text-center">Indeks Musiman</th>
                            <th class="text-center text-primary fw-bold">Prediksi Kasar ({{ $forecastYear }})</th>
                            <th class="text-center text-danger fw-bold">Safety Stock</th>
                            <th class="text-center text-success fw-bold" style="background-color: #f0fdf4;">Rekomendasi Stok ({{ $forecastYear }})</th>
                        </tr>
                    </thead>
                    <tbody id="tableResultBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Actionable PO recommendation --}}
        <div class="glass-card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold text-success-dark mb-1" style="font-size: 1.05rem;"><i class="ph ph-check-square-offset"></i> Rekomendasi Siap Ditindaklanjuti</h5>
                    <p class="text-secondary-dark mb-0 small">Berdasarkan hasil analisa musiman, Anda disarankan untuk segera membuat pemesanan jika stok barang saat ini mendekati ROP.</p>
                </div>
                @if(auth()->user()->role === 'manager')
                <a href="{{ route('pemesanan-supplier.create') }}" class="btn btn-success fw-bold" style="border-radius: 8px; padding: 10px 20px;"><i class="ph ph-shopping-cart-simple"></i> Buat Pemesanan Supplier</a>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formForecasting');
        const loadingArea = document.getElementById('loadingArea');
        const resultArea = document.getElementById('resultArea');
        let chartInstance = null;

        // Dynamic Product Dropdown Search Filter
        const searchProductInput = document.getElementById('search_product');
        const selectProduct = document.getElementById('product_id');
        const originalOptions = Array.from(selectProduct.options);

        searchProductInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            // Clear current options
            selectProduct.innerHTML = '';
            
            // Re-add matching options
            originalOptions.forEach(opt => {
                if (opt.value === "" || opt.text.toLowerCase().includes(query)) {
                    selectProduct.appendChild(opt.cloneNode(true));
                }
            });

            // Auto-select the first non-placeholder option if search matches anything
            if (query.length > 0 && selectProduct.options.length > 1) {
                for (let i = 0; i < selectProduct.options.length; i++) {
                    if (!selectProduct.options[i].disabled && selectProduct.options[i].value !== "") {
                        selectProduct.options[i].selected = true;
                        break;
                    }
                }
            } else if (query.length === 0) {
                // If search is cleared, select the placeholder
                selectProduct.value = "";
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Hide result, show loader
            resultArea.style.display = 'none';
            loadingArea.style.display = 'block';

            const formData = new FormData(form);

            fetch('{{ route("peramalan.calculate") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_csrf') || '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                loadingArea.style.display = 'none';
                if(res.status === 'success') {
                    // Fill Stats
                    document.getElementById('statSales').textContent = res.totals.sales.toLocaleString('id-ID') + ' pcs';
                    document.getElementById('statCorrected').textContent = res.totals.corrected.toLocaleString('id-ID') + ' pcs';
                    document.getElementById('statStdDev').textContent = res.std_dev.toLocaleString('id-ID');
                    document.getElementById('statSafetyStock').textContent = res.safety_stock_global.toLocaleString('id-ID') + ' pcs';

                    // Populate Table
                    const tbody = document.getElementById('tableResultBody');
                    let tbodyHtml = '';
                    
                    const labels = [];
                    const salesData = [];
                    const correctedData = [];
                    const recData = [];

                    res.monthly_data.forEach(item => {
                        labels.push(item.name);
                        salesData.push(item.sales);
                        correctedData.push(item.corrected_demand);
                        recData.push(item.recommendation_2027);

                        tbodyHtml += `
                            <tr>
                                <td class="fw-bold">${item.name}</td>
                                <td class="text-center">${item.sales.toLocaleString('id-ID')} pcs</td>
                                <td class="text-center text-danger fw-semibold">${item.stockout_days} Hari</td>
                                <td class="text-center text-muted">${item.lost_sales.toLocaleString('id-ID')} pcs</td>
                                <td class="text-center fw-bold">${item.corrected_demand.toLocaleString('id-ID')} pcs</td>
                                <td class="text-center text-secondary">${item.seasonal_index.toFixed(2)}</td>
                                <td class="text-center text-primary fw-bold">${item.forecast_2027.toLocaleString('id-ID')} pcs</td>
                                <td class="text-center text-danger fw-bold">${item.safety_stock_2027.toLocaleString('id-ID')} pcs</td>
                                <td class="text-center text-success fw-bold" style="background-color: #f9fbf9;">${item.recommendation_2027.toLocaleString('id-ID')} pcs</td>
                            </tr>
                        `;
                    });

                    tbody.innerHTML = tbodyHtml;

                    // Render Chart.js
                    const ctx = document.getElementById('forecastChart').getContext('2d');
                    if (chartInstance) {
                        chartInstance.destroy();
                    }

                    chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Penjualan Aktual (' + res.historical_year + ')',
                                    data: salesData,
                                    backgroundColor: '#cbd5e1',
                                    borderWidth: 0,
                                    borderRadius: 6,
                                    barPercentage: 0.6
                                },
                                {
                                    label: 'Permintaan Terkoreksi (' + res.historical_year + ')',
                                    data: correctedData,
                                    backgroundColor: 'rgba(59, 130, 246, 0.4)',
                                    borderColor: '#3b82f6',
                                    borderWidth: 1.5,
                                    borderRadius: 6,
                                    barPercentage: 0.6
                                },
                                {
                                    label: 'Rekomendasi Proyeksi (' + res.forecast_year + ')',
                                    type: 'line',
                                    data: recData,
                                    borderColor: '#10b981',
                                    backgroundColor: '#10b981',
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    tension: 0.25,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        font: { size: 11, weight: 600 }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: { font: { size: 10 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 10, weight: 600 } }
                                }
                            }
                        }
                    });

                    // Show Results area
                    resultArea.style.display = 'block';
                    resultArea.scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert('Gagal memproses perhitungan peramalan.');
                }
            })
            .catch(err => {
                console.error(err);
                loadingArea.style.display = 'none';
                alert('Terjadi kesalahan pada sistem saat memproses peramalan.');
            });
        });
    });
</script>
@endsection
