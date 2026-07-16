@extends('layouts.app')
@section('title', 'Peramalan Penjualan')
@section('page-title', 'Peramalan Penjualan')

@section('content')
<style>
    /* Premium Styling Peramalan Penjualan */
    .forecasting-container {
        font-family: var(--font-display, 'Outfit', 'Inter', sans-serif);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .glass-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.04);
    }

    .form-label-premium {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-premium {
        border: 1.5px solid #e2e8f0;
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #0f172a;
        transition: all 0.2s ease;
    }

    .form-control-premium:focus {
        border-color: #dc2626;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        outline: none;
    }

    .btn-premium {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-premium:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
        color: #ffffff;
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
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 14px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        transition: all 0.3s;
    }

    .stat-chip:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
    }

    .stat-chip-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-chip-details {
        display: flex;
        flex-direction: column;
    }

    .stat-chip-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-chip-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2px;
    }

    /* High-contrast custom badges */
    .badge-periode-forecast {
        background-color: #eff6ff !important; /* Blue pastel */
        color: #1d4ed8 !important; /* Blue text */
        font-weight: 600;
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid rgba(29, 78, 216, 0.08);
    }
    
    .badge-musim-forecast {
        background-color: #fffbeb !important; /* Amber pastel */
        color: #b45309 !important; /* Amber text */
        font-weight: 600;
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid rgba(180, 83, 9, 0.08);
    }

    .badge-kritis {
        background-color: #fef2f2;
        color: #b91c1c;
        font-weight: 700;
        font-size: 0.68rem;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid rgba(185, 28, 28, 0.12);
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .badge-stokout {
        background-color: #fff7ed;
        color: #c2410c;
        font-weight: 600;
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 5px;
        border: 1px solid rgba(194, 65, 12, 0.1);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .table-premium th {
        font-weight: 700;
        font-size: 0.8rem;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0;
        padding: 14px 16px;
    }

    .table-premium td {
        font-size: 0.88rem;
        padding: 14px 16px;
        color: #334155;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-premium tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-premium tbody tr:hover {
        background-color: #faf5f5; /* Light soft red tint on hover */
    }

    /* Advanced parameter toggle */
    .advanced-toggle {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s;
        cursor: pointer;
        user-select: none;
    }

    .advanced-toggle:hover {
        color: #dc2626;
    }

    /* Loading skeleton */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 50px 0;
    }

    .spinner-border-premium {
        width: 3rem;
        height: 3rem;
        color: #dc2626;
    }
</style>

<div class="container-fluid forecasting-container">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="btn-history-back me-3">
            <i class="ph ph-arrow-left"></i> Kembali
        </a>
        <div class="flex-grow-1">
            <h1 class="fw-bold mb-0 text-slate-800" style="font-size: 1.8rem; font-weight: 800;">Peramalan Penjualan</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Analisis proyeksi penjualan musiman dan estimasi kebutuhan stok bebas dari lost sales.</p>
        </div>
    </div>

    {{-- Form Setup --}}
    <div class="glass-card">
        <h5 class="fw-bold mb-4 text-slate-800" style="font-size: 1.05rem;"><i class="ph ph-sliders-horizontal text-danger"></i> Atur Parameter Analisis</h5>
        <form id="formForecasting">
            @csrf
            <div class="row g-3 align-items-end">
                {{-- Product Select --}}
                <div class="col-lg-5 col-md-6 col-12">
                    <label class="form-label-premium" for="product_id">Pilih Produk Yang Ingin Diramal</label>
                    <select class="form-select form-control-premium w-100" id="product_id" name="product_id" required>
                        <option value="all" selected>Semua Produk</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_produk }} ({{ $p->kode_produk }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Season Select --}}
                <div class="col-lg-4 col-md-6 col-12">
                    <label class="form-label-premium" for="season">Pilih Periode Musim Liburan / Hari Raya</label>
                    <select class="form-select form-control-premium w-100" id="season" name="season" required>
                        <option value="lebaran" selected>Lebaran (Hari Raya Idul Fitri)</option>
                        <option value="idul_adha">Hari Raya Idul Adha</option>
                        <option value="natal">Hari Raya Natal (Estimasi Data Proxy)</option>
                        <option value="tahun_baru">Liburan Tahun Baru</option>
                    </select>
                </div>

                {{-- Submit Button --}}
                <div class="col-lg-3 col-md-12 col-12">
                    <button type="submit" class="btn-premium w-100 justify-content-center py-2-5" id="btnCalculate" style="border-radius: 8px;">
                        <i class="ph ph-lightning bold"></i> Proses Peramalan
                    </button>
                </div>
            </div>

            {{-- Hidden parameters with default values --}}
            <input type="hidden" name="growth_rate" value="10">
            <input type="hidden" name="lead_time" value="3">
            <input type="hidden" name="service_level" value="95">
        </form>
    </div>

    {{-- Proxy Warning Alert Box --}}
    <div id="proxyWarningAlert" class="alert alert-warning border-0 shadow-sm py-3 px-4 mb-4" style="display: none; border-radius: 12px;">
        <div class="d-flex align-items-start gap-3">
            <div style="width: 36px; height: 36px; background: #fef3c7; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="ph ph-warning-circle text-warning font-semibold" style="font-size: 1.3rem;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-slate-800" style="font-size: 0.95rem;">Menggunakan Data Penjualan Proxy (Januari 2026)</h6>
                <p class="text-muted mb-0" style="font-size: 0.82rem;">Karena data penjualan Desember 2026 tidak tersedia di file Excel Jan-Mei 2026, sistem secara otomatis mensimulasikan data Natal/Desember menggunakan riwayat transaksi dari bulan Januari 2026 sebagai basis estimasi terpercaya.</p>
            </div>
        </div>
    </div>

    {{-- Loading Spinner --}}
    <div class="loading-spinner" id="loadingArea">
        <div class="spinner-border spinner-border-premium" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-3 fw-bold" style="font-size: 0.92rem;">Sistem sedang merekonstruksi stok harian secara maju, menghitung Lost Sales & indeks musiman...</p>
    </div>

    {{-- Result Section (Populated dynamically) --}}
    <div id="resultArea">
        {{-- Stat Row --}}
        <div class="stat-chip-row">
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(220, 38, 38, 0.06); color: #dc2626;">
                    <i class="ph ph-calendar"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Musim Terpilih</span>
                    <span class="stat-chip-value" id="statSeason" style="font-size: 1.15rem; font-weight: 800;">{{ $seasonLabel }}</span>
                </div>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(71, 85, 105, 0.06); color: #475569;">
                    <i class="ph ph-shopping-bag"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Total Penjualan Riil (2026)</span>
                    <span class="stat-chip-value" id="statSalesActual">0 pcs</span>
                </div>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(217, 119, 6, 0.06); color: #d97706;">
                    <i class="ph ph-warning"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Lost Sales Terhindari</span>
                    <span class="stat-chip-value" id="statLostSales">0 pcs</span>
                </div>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-icon" style="background: rgba(16, 185, 129, 0.06); color: #10b981;">
                    <i class="ph ph-trend-up"></i>
                </div>
                <div class="stat-chip-details">
                    <span class="stat-chip-label">Hasil Ramalan Stok (2027)</span>
                    <span class="stat-chip-value" id="statForecast" style="color: #10b981;">0 pcs</span>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            {{-- Chart Card --}}
            <div class="col-lg-12">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3 text-slate-800" style="font-size: 1.02rem;" id="chartTitle">
                        <i class="ph ph-chart-bar-horizontal text-danger"></i> Tren Penjualan Musiman 2026 vs Proyeksi Kebutuhan 2027
                    </h5>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="forecastChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comparison Table --}}
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-slate-800" style="font-size: 1.05rem;" id="tableTitle">
                    <i class="ph ph-table text-danger"></i> Hasil Analisis Peramalan Penjualan
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle table-premium" id="tableComparison">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Produk</th>
                            <th>Periode Musim</th>
                            <th class="text-end">Data Tahun Sebelumnya (2026)</th>
                            <th class="text-end fw-bold text-success" style="background-color: #f0fdf4;">Hasil Ramalan (2027)</th>
                        </tr>
                    </thead>
                    <tbody id="tableComparisonBody">
                        @foreach($comparisonData as $index => $item)
                            <tr data-product-id="{{ $item['id'] }}">
                                <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-slate-800">{{ $item['nama'] }}</span>
                                        @if($item['is_critical'])
                                            <span class="badge-kritis" title="10 produk utama dengan riwayat stockout/kekurangan">10 Kritis</span>
                                        @endif
                                    </div>
                                    <small class="text-muted fw-semibold">{{ $item['kode'] }}</small>
                                </td>
                                <td>
                                    <span class="badge-periode-forecast">{{ $historicalPeriod }}</span>
                                    <i class="ph ph-arrow-right mx-2 text-muted" style="font-size: 0.8rem; vertical-align: middle;"></i>
                                    <span class="badge-musim-forecast">{{ $forecastPeriod }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-semibold text-slate-700">{{ number_format($item['sales_actual']) }} pcs</span>
                                    @if($item['stockout_days'] > 0)
                                        <div class="mt-1">
                                            <span class="badge-stokout" title="Stok habis selama beberapa hari pada periode ini">
                                                <i class="ph ph-clock-countdown text-orange"></i> {{ $item['stockout_days'] }} Hari Kosong (+{{ number_format($item['lost_sales']) }} pcs lost sales)
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success rec-cell" style="background-color: #f0fdf4; font-size: 0.95rem;">
                                    {{ number_format($item['hasil_ramalan']) }} pcs
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
        const proxyWarningAlert = document.getElementById('proxyWarningAlert');
        let chartInstance = null;

        // Populate initial total stats on load
        function updateSummaryStats(salesActual, lostSales, forecastVal, seasonLabelText) {
            document.getElementById('statSeason').textContent = seasonLabelText;
            document.getElementById('statSalesActual').textContent = salesActual.toLocaleString('id-ID') + ' pcs';
            document.getElementById('statLostSales').textContent = lostSales.toLocaleString('id-ID') + ' pcs';
            document.getElementById('statForecast').textContent = forecastVal.toLocaleString('id-ID') + ' pcs';
        }

        // Render initial Chart.js using backend compiled values
        function renderInitialChart() {
            const tbodyRows = document.querySelectorAll('#tableComparisonBody tr');
            const labels = [];
            const salesData = [];
            const correctedData = [];
            const recData = [];

            // We grab top 10 products from table
            let count = 0;
            tbodyRows.forEach(row => {
                if (count >= 10) return;
                const nameNode = row.querySelector('td:nth-child(2) .fw-bold');
                const salesNode = row.querySelector('td:nth-child(4) span');
                const recNode = row.querySelector('td:nth-child(5)');

                if (nameNode && salesNode && recNode) {
                    const name = nameNode.textContent.trim();
                    labels.push(name.length > 15 ? name.substring(0, 15) + '...' : name);
                    
                    const salesVal = parseInt(salesNode.textContent.replace(/\D/g, '')) || 0;
                    salesData.push(salesVal);
                    
                    // Estimate corrected
                    const isStockout = row.querySelector('.badge-stokout');
                    let lostVal = 0;
                    if (isStockout) {
                        const lostMatch = isStockout.textContent.match(/\+(\d+(?:\.\d+)?)\s*pcs/);
                        if (lostMatch) lostVal = parseInt(lostMatch[1]) || 0;
                    }
                    correctedData.push(salesVal + lostVal);
                    
                    const recVal = parseInt(recNode.textContent.replace(/\D/g, '')) || 0;
                    recData.push(recVal);
                    count++;
                }
            });

            // Calculate total sums
            let sumSales = 0;
            let sumLost = 0;
            let sumRec = 0;
            tbodyRows.forEach(row => {
                const salesNode = row.querySelector('td:nth-child(4) span');
                const recNode = row.querySelector('td:nth-child(5)');
                if (salesNode && recNode) {
                    const salesVal = parseInt(salesNode.textContent.replace(/\D/g, '')) || 0;
                    sumSales += salesVal;
                    
                    const isStockout = row.querySelector('.badge-stokout');
                    if (isStockout) {
                        const lostMatch = isStockout.textContent.match(/\+(\d+(?:\.\d+)?)\s*pcs/);
                        if (lostMatch) sumLost += parseInt(lostMatch[1]) || 0;
                    }
                    sumRec += (parseInt(recNode.textContent.replace(/\D/g, '')) || 0);
                }
            });
            updateSummaryStats(sumSales, sumLost, sumRec, '{{ $seasonLabel }}');

            const ctx = document.getElementById('forecastChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Penjualan Aktual ({{ $historicalPeriod }})',
                            data: salesData,
                            backgroundColor: '#cbd5e1',
                            borderRadius: 6,
                            barPercentage: 0.6
                        },
                        {
                            label: 'Permintaan Terkoreksi (+Lost Sales)',
                            data: correctedData,
                            backgroundColor: 'rgba(59, 130, 246, 0.4)',
                            borderColor: '#3b82f6',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.6
                        },
                        {
                            label: 'Hasil Ramalan Musim ({{ $forecastPeriod }})',
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
        }

        renderInitialChart();

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loader, hide result area
            resultArea.style.opacity = '0.3';
            loadingArea.style.display = 'block';

            const formData = new FormData(form);
            const selectedSeason = document.getElementById('season').value;

            // Trigger proxy warning if Natal is selected
            if (selectedSeason === 'natal') {
                proxyWarningAlert.style.display = 'block';
            } else {
                proxyWarningAlert.style.display = 'none';
            }

            fetch('{{ route("peramalan.calculate") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                loadingArea.style.display = 'none';
                resultArea.style.opacity = '1';

                if (res.status === 'success') {
                    // Update Summary
                    updateSummaryStats(res.totals.sales, res.totals.lost_sales, res.totals.forecast, res.season_label);

                    // Update Title texts
                    const prodNameText = document.getElementById('product_id').options[document.getElementById('product_id').selectedIndex].text;
                    const cleanProdName = prodNameText.split(' (')[0];
                    document.getElementById('chartTitle').innerHTML = `<i class="ph ph-chart-bar-horizontal text-danger"></i> Tren Penjualan Musiman 2026 vs Proyeksi Kebutuhan 2027 - ${cleanProdName}`;

                    // Update Table
                    const tbody = document.getElementById('tableComparisonBody');
                    let tbodyHtml = '';

                    res.forecast_data.forEach((item, index) => {
                        const criticalBadge = item.is_critical ? '<span class="badge-kritis" title="10 produk utama dengan riwayat stockout/kekurangan">10 Kritis</span>' : '';
                        const stockoutBadge = item.stockout_days > 0 
                            ? `<div class="mt-1"><span class="badge-stokout" title="Stok habis selama beberapa hari pada periode ini"><i class="ph ph-clock-countdown text-orange"></i> ${item.stockout_days} Hari Kosong (+${item.lost_sales.toLocaleString('id-ID')} pcs lost sales)</span></div>`
                            : '';

                        tbodyHtml += `
                            <tr data-product-id="${item.id}">
                                <td class="text-center fw-semibold text-muted">${index + 1}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-slate-800">${item.nama}</span>
                                        ${criticalBadge}
                                    </div>
                                    <small class="text-muted fw-semibold">${item.kode}</small>
                                </td>
                                <td>
                                    <span class="badge-periode-forecast">${res.historical_period}</span>
                                    <i class="ph ph-arrow-right mx-2 text-muted" style="font-size: 0.8rem; vertical-align: middle;"></i>
                                    <span class="badge-musim-forecast">${res.forecast_period}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-semibold text-slate-700">${item.sales_actual.toLocaleString('id-ID')} pcs</span>
                                    ${stockoutBadge}
                                </td>
                                <td class="text-end fw-bold text-success rec-cell" style="background-color: #f0fdf4; font-size: 0.95rem;">
                                    ${item.hasil_ramalan.toLocaleString('id-ID')} pcs
                                </td>
                            </tr>
                        `;
                    });

                    tbody.innerHTML = tbodyHtml;

                    // Update Chart.js
                    if (chartInstance) {
                        chartInstance.destroy();
                    }

                    const ctx = document.getElementById('forecastChart').getContext('2d');
                    
                    let chartType = res.is_all ? 'bar' : 'line';
                    let datasets = [];

                    if (res.is_all) {
                        datasets = [
                            {
                                label: 'Penjualan Aktual (' + res.historical_period + ')',
                                data: res.chart.sales,
                                backgroundColor: '#cbd5e1',
                                borderRadius: 6,
                                barPercentage: 0.6
                            },
                            {
                                label: 'Permintaan Terkoreksi (+Lost Sales)',
                                data: res.chart.corrected,
                                backgroundColor: 'rgba(59, 130, 246, 0.4)',
                                borderColor: '#3b82f6',
                                borderWidth: 1.5,
                                borderRadius: 6,
                                barPercentage: 0.6
                            },
                            {
                                label: 'Hasil Ramalan Musim (' + res.forecast_period + ')',
                                type: 'line',
                                data: res.chart.recommendation,
                                borderColor: '#10b981',
                                backgroundColor: '#10b981',
                                borderWidth: 3,
                                pointRadius: 4,
                                tension: 0.25,
                                fill: false
                            }
                        ];
                    } else {
                        // For single product, show linear timeline progression inside the month
                        datasets = [
                            {
                                label: 'Penjualan Harian Aktual',
                                data: res.chart.sales,
                                borderColor: '#94a3b8',
                                backgroundColor: 'rgba(148, 163, 184, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.2
                            },
                            {
                                label: 'Permintaan Terkoreksi Harian (+Lost Sales)',
                                data: res.chart.corrected,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.2
                            },
                            {
                                label: 'Hasil Ramalan Harian (' + res.forecast_period + ')',
                                data: res.chart.recommendation,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderWidth: 3,
                                fill: false,
                                tension: 0.2
                            }
                        ];
                    }

                    chartInstance = new Chart(ctx, {
                        type: chartType,
                        data: {
                            labels: res.chart.labels,
                            datasets: datasets
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

                    // Scroll to results
                    resultArea.scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert('Gagal memproses perhitungan peramalan.');
                }
            })
            .catch(err => {
                console.error(err);
                loadingArea.style.display = 'none';
                resultArea.style.opacity = '1';
                alert('Terjadi kesalahan pada sistem saat memproses peramalan.');
            });
        });
    });
</script>
@endsection
