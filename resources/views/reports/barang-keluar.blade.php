@extends('layouts.app')
@section('title', 'Laporan Barang Keluar')
@section('page-title', 'Laporan Barang Keluar')

@section('content')
<div class="container-fluid">


    {{-- Filter Panel --}}
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.barang-keluar') }}" class="row g-3 align-items-end" id="formFilterBarangKeluar">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Periode Tanggal</label>
                    <div class="input-group">
                        <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}" min="2026-01-01">
                        <span class="input-group-text bg-light">s/d</span>
                        <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}" min="2026-01-01">
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Produk</label>
                    <select name="product_id" class="form-select">
                        <option value="">Semua Produk</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->kode_produk }} - {{ $prod->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="ph ph-funnel me-2"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Barang Keluar --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-success-light" style="border: 1px solid #d1fae5 !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new-custom icon-success me-3">
                        <i class="ph ph-arrow-circle-up"></i>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Total Barang Keluar</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success-dark">{{ number_format($totalTransaksi) }} <span class="fs-6 fw-normal text-muted">Transaksi</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Produk --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-blue-light" style="border: 1px solid #dbeafe !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new-custom icon-blue me-3">
                        <i class="ph ph-package"></i>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Total Produk</span>
                        <h3 class="fw-bold mb-0 mt-1 text-blue-dark">{{ number_format($totalProduk) }} <span class="fs-6 fw-normal text-muted">Produk</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Qty Keluar --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-warning-light" style="border: 1px solid #fef9c3 !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new-custom icon-warning me-3">
                        <i class="ph ph-truck"></i>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Total Qty Keluar</span>
                        <h3 class="fw-bold mb-0 mt-1 text-warning-dark">{{ number_format($totalQty) }} <span class="fs-6 fw-normal text-muted">Pcs</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Nilai --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm bg-purple-light" style="border: 1px solid #f3e8ff !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon-new-custom icon-purple me-3">
                        <span class="fw-bold" style="font-size: 0.95rem; font-family: var(--font-display);">Rp</span>
                    </div>
                    <div>
                        <span class="text-muted-dark small d-block">Total Nilai (FIFO)</span>
                        <h3 class="fw-bold mb-0 mt-1 text-purple-dark" style="white-space: nowrap; font-size: clamp(1.1rem, 1.3vw, 1.4rem);">Rp {{ number_format($totalNilai, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-0 pt-3 pb-0">
            <h5 class="mb-0 fw-bold"><i class="ph ph-file-text me-1 text-primary"></i> Data Laporan Barang Keluar</h5>
            <div class="no-print">
                <a href="{{ route('export.barang-keluar.pdf', request()->all()) }}" class="btn btn-sm btn-outline-danger"><i class="ph ph-file-pdf me-1"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableBarangKeluar">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th style="width: 150px;">TANGGAL KELUAR</th>
                            <th style="width: 140px;">NO. TRANSAKSI</th>
                            <th>PRODUK</th>
                            <th class="text-center" style="width: 130px;">QTY KELUAR<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(pcs)</span></th>
                            <th class="text-center" style="width: 160px;">
                                TANGGAL MASUK <i class="ph ph-question text-muted" style="cursor: help;" title="Menunjukkan tanggal masuk batch stok terpakai. Jika ada lebih dari 1 tanggal, artinya pengambilan barang memotong beberapa batch (kiriman) karena stok kiriman lama habis."></i><br>
                                <span class="text-muted font-normal small" style="font-size: 0.65rem;">(PENERAPAN FIFO)</span>
                            </th>
                            <th class="text-end" style="width: 160px;">TOTAL NILAI<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(Rp)</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedItems as $index => $item)
                            <tr onclick="showFifoDetails({{ $index }})" class="clickable-row" style="cursor: pointer;" title="Klik untuk melihat kalkulasi FIFO detail">
                                <td class="text-center text-muted fw-semibold">{{ $paginatedItems->firstItem() + $index }}</td>
                                <td>{{ $item['tanggal_keluar']->translatedFormat('d M Y H:i') }}</td>
                                <td><span class="badge bg-light text-dark border fw-bold">{{ $item['transaction_code'] }}</span></td>
                                <td class="fw-bold text-dark">{{ $item['nama_produk'] }}</td>
                                <td class="text-center text-success fw-bold">{{ number_format($item['jumlah']) }}</td>
                                <td class="text-center text-muted small">{{ $item['tanggal_barang_masuk'] }}</td>
                                <td class="text-end fw-bold text-dark">Rp {{ number_format($item['nilai'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ph ph-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data barang keluar untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($paginatedItems->hasPages())
            <div class="card-footer bg-white border-top no-print">
                {{ $paginatedItems->links() }}
            </div>
        @endif
    </div>

    {{-- Bottom Section: FIFO Details Visualizer --}}
    <div class="card border-0 shadow-sm mb-4" id="fifoDetailsSection" style="display: none; background-color: #fffaf0; border: 1px solid #ffe4b5 !important;">
        <div class="card-header border-0 pb-0" style="background-color: #fffaf0;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-warning-dark mb-0"><i class="ph ph-scales me-2"></i> Informasi Penerapan Metode FIFO (First In, First Out)</h6>
                <button type="button" class="btn-close" onclick="closeFifoDetails()"></button>
            </div>
        </div>
        <div class="card-body">
            <h6 class="fw-bold mb-3" style="font-size: 0.88rem; color: var(--text-primary);" id="fifoDetailTitle">Contoh Perhitungan FIFO - Produk: - (Transaksi: -)</h6>
            
            <div class="row g-4 align-items-center">
                {{-- Left Table --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm bg-white p-0">
                        <div class="p-2 border-bottom bg-light">
                            <span class="fw-bold small text-muted"><i class="ph ph-list-numbers me-1"></i> Riwayat Stok Masuk (Urutan Masuk)</span>
                        </div>
                        <div class="table-responsive" style="max-height: 200px;">
                            <table class="table table-sm align-middle mb-0" style="font-size: 0.78rem;" id="tableFifoLeft">
                                <thead>
                                    <tr>
                                        <th>Tanggal Masuk</th>
                                        <th>No. Transaksi Masuk</th>
                                        <th class="text-center">Qty Masuk</th>
                                        <th class="text-center">Sisa Stok Sebelum Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Filled dynamically via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Arrow --}}
                <div class="col-lg-1 text-center d-none d-lg-block">
                    <i class="ph ph-arrow-right fs-1 text-warning-dark"></i>
                </div>

                {{-- Right Table --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm bg-white p-0">
                        <div class="p-2 border-bottom bg-light">
                            <span class="fw-bold small text-muted"><i class="ph ph-check-square me-1"></i> Pemakaian FIFO untuk Transaksi</span>
                        </div>
                        <div class="table-responsive" style="max-height: 200px;">
                            <table class="table table-sm align-middle mb-0" style="font-size: 0.78rem;" id="tableFifoRight">
                                <thead>
                                    <tr>
                                        <th>Dari Stok Masuk</th>
                                        <th class="text-center">Qty Terpakai</th>
                                        <th class="text-center">Sisa Setelah Dipakai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Filled dynamically via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Box Far-Right --}}
                <div class="col-lg-2">
                    <div class="card border-0 shadow-sm text-center p-3" style="background-color: #fff7ed; border: 1px solid #ffedd5 !important;">
                        <span class="text-muted small fw-bold d-block mb-1">Nilai Barang Keluar</span>
                        <h4 class="fw-bold text-warning-dark mb-0" id="fifoNilaiText" style="white-space: nowrap;">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<style>
    /* Styling for Premium Custom Widgets */
    .bg-success-light { background-color: #ecfdf5; }
    .bg-blue-light { background-color: #eff6ff; }
    .bg-warning-light { background-color: #fffbeb; }
    .bg-purple-light { background-color: #faf5ff; }

    .text-success-dark { color: #065f46 !important; }
    .text-blue-dark { color: #1e40af !important; }
    .text-warning-dark { color: #854d0e !important; }
    .text-purple-dark { color: #6b21a8 !important; }
    .text-muted-dark { color: #475569; font-weight: 500; }

    .stat-icon-new-custom {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .icon-success { background-color: #d1fae5; color: #065f46; }
    .icon-blue { background-color: #dbeafe; color: #1e40af; }
    .icon-warning { background-color: #fde68a; color: #854d0e; }
    .icon-purple { background-color: #f3e8ff; color: #6b21a8; }

    #tableBarangKeluar th {
        background-color: #1e293b;
        color: #ffffff;
        font-family: var(--font-display);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 12px 14px;
        border-bottom: none;
    }

    #tableBarangKeluar td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.82rem;
    }

    .clickable-row {
        transition: background-color 0.15s ease;
    }
    .clickable-row:hover {
        background-color: #f8fafc !important;
    }

    .font-normal {
        font-weight: 400 !important;
    }
</style>

<script>
    // Embed the items' FIFO data dynamically from PHP
    const outgoingGoodsData = @json($paginatedItems->items());

    function showFifoDetails(index) {
        const item = outgoingGoodsData[index];
        if (!item || !item.fifo_details) return;

        const details = item.fifo_details;

        // Set title
        document.getElementById('fifoDetailTitle').textContent = `Contoh Perhitungan FIFO - Produk: ${details.product_name} (Transaksi: ${details.transaction_code})`;
        
        // Fill Left Table
        const leftTbody = document.querySelector('#tableFifoLeft tbody');
        leftTbody.innerHTML = '';
        if (details.available_batches && details.available_batches.length > 0) {
            details.available_batches.forEach(b => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${b.tanggal_masuk}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">${b.batch_code}</span></td>
                    <td class="text-center">${b.qty_masuk}</td>
                    <td class="text-center fw-bold text-muted">${b.sisa_sebelum}</td>
                `;
                leftTbody.appendChild(tr);
            });
        } else {
            leftTbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada riwayat batch stok.</td></tr>';
        }

        // Fill Right Table
        const rightTbody = document.querySelector('#tableFifoRight tbody');
        rightTbody.innerHTML = '';
        if (details.batches_used && details.batches_used.length > 0) {
            details.batches_used.forEach(b => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="badge bg-light text-dark border fw-bold">${b.batch_code} (${b.tanggal_masuk})</span></td>
                    <td class="text-center text-success fw-bold">${b.qty_terpakai}</td>
                    <td class="text-center text-muted">${b.sisa_setelah_dipakai}</td>
                `;
                rightTbody.appendChild(tr);
            });
        } else {
            rightTbody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted">Menggunakan stok default (sistem seeder/migrasi lama).</td></tr>';
        }

        // Set Nilai Text
        document.getElementById('fifoNilaiText').textContent = details.nilai_keluar_formatted;

        // Display the panel
        document.getElementById('fifoDetailsSection').style.display = 'block';

        // Scroll to the panel smoothly
        document.getElementById('fifoDetailsSection').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeFifoDetails() {
        document.getElementById('fifoDetailsSection').style.display = 'none';
    }
</script>
@endsection
