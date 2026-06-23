<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Keluar - Della Frozen Mart</title>
    <!-- Include Bootstrap 5 & Phosphor Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            color: #1e293b;
            background: white;
            padding: 30px;
        }
        .header-print {
            border-bottom: 3px double #cbd5e1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-box {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e40af;
            letter-spacing: -0.02em;
        }
        .table-print th {
            background-color: #f1f5f9 !important;
            color: #0f172a;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-print td {
            font-size: 0.8rem;
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        .summary-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .summary-val {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    {{-- Branded Header --}}
    <div class="header-print d-flex justify-content-between align-items-center">
        <div>
            <div class="logo-box">Della Frozen Mart</div>
            <div class="text-muted small">Sistem Manajemen Persediaan & Penjualan</div>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0">LAPORAN BARANG KELUAR</h4>
            <div class="text-muted small">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
        </div>
    </div>

    {{-- Info Filter --}}
    <div class="mb-4">
        <span class="badge bg-secondary py-2 px-3 fs-7">
            Periode: 
            @if(request('tanggal_dari') || request('tanggal_sampai'))
                {{ request('tanggal_dari') ? \Carbon\Carbon::parse(request('tanggal_dari'))->translatedFormat('d M Y') : 'Awal' }}
                s/d
                {{ request('tanggal_sampai') ? \Carbon\Carbon::parse(request('tanggal_sampai'))->translatedFormat('d M Y') : 'Kini' }}
            @else
                Semua Periode
            @endif
        </span>
    </div>

    {{-- Metrics --}}
    <div class="row g-3 mb-4">
        <div class="col-3">
            <div class="summary-box">
                <span class="summary-label">Total Transaksi</span>
                <div class="summary-val mt-1">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-3">
            <div class="summary-box">
                <span class="summary-label">Total Jenis Produk</span>
                <div class="summary-val mt-1 text-primary">{{ number_format($totalProduk, 0, ',', '.') }} Produk</div>
            </div>
        </div>
        <div class="col-3">
            <div class="summary-box">
                <span class="summary-label">Total Qty Keluar</span>
                <div class="summary-val mt-1 text-warning">{{ number_format($totalQty, 0, ',', '.') }} Pcs</div>
            </div>
        </div>
        <div class="col-3">
            <div class="summary-box">
                <span class="summary-label">Total Nilai (FIFO COGS)</span>
                <div class="summary-val mt-1 text-danger">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <table class="table table-striped align-middle table-print">
        <thead>
            <tr>
                <th style="width: 50px;">NO</th>
                <th>TANGGAL KELUAR</th>
                <th>NO. TRANSAKSI</th>
                <th>PRODUK</th>
                <th class="text-center">QTY KELUAR</th>
                <th>TUJUAN / KETERANGAN</th>
                <th>TANGGAL MASUK (FIFO)</th>
                <th class="text-end">TOTAL NILAI (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedOutgoing as $index => $item)
                <tr>
                    <td class="text-muted">{{ $index + 1 }}</td>
                    <td>{{ $item['tanggal_keluar']->translatedFormat('d M Y H:i') }}</td>
                    <td><span class="fw-bold">{{ $item['transaction_code'] }}</span></td>
                    <td class="fw-bold">{{ $item['nama_produk'] }}</td>
                    <td class="text-center text-danger fw-bold">-{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                    <td>
                        @if($item['keterangan'])
                            {{ $item['keterangan'] }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $item['jenis_keluar'])) }}
                        @endif
                    </td>
                    <td>{{ $item['tanggal_barang_masuk'] }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($item['nilai'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="4" class="text-end">TOTAL</td>
                <td class="text-center text-danger">-{{ number_format($totalQty, 0, ',', '.') }}</td>
                <td colspan="2"></td>
                <td class="text-end text-danger">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer Signature Area --}}
    <div class="row mt-5 pt-4">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p class="mb-5">Petugas Gudang,</p>
            <div style="border-bottom: 1px solid #000; width: 180px; margin: 0 auto;"></div>
            <p class="mt-1 small text-muted">{{ auth()->user()->name ?? 'Administrator' }}</p>
        </div>
    </div>

    {{-- Auto Print Trigger --}}
    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.history.back();
            }, 1000);
        }
    </script>
</body>
</html>
