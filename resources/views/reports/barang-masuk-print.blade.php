<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang Masuk - Della Frozen Mart</title>
    <!-- Include Bootstrap 5 & Google Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
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
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            border-bottom: 2px solid #cbd5e1;
            vertical-align: middle;
        }
        .table-print td {
            font-size: 0.75rem;
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        .summary-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .summary-val {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
        }
        .text-expired {
            color: #ef4444 !important;
            font-weight: 700;
        }
        .text-near-expired {
            color: #f59e0b !important;
            font-weight: 600;
        }
        .text-safe {
            color: #64748b;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .table-print th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
            <h4 class="fw-bold mb-0">LAPORAN BARANG MASUK</h4>
            <div class="text-muted small">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
        </div>
    </div>

    {{-- Info Filter --}}
    <div class="mb-4 d-flex gap-2">
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
        @if(request('product_id') && $incomingGoods->count() > 0 && $incomingGoods->first()->product)
            <span class="badge bg-primary py-2 px-3 fs-7">
                Produk: {{ $incomingGoods->first()->product->nama_produk }}
            </span>
        @endif
        @if(request('supplier_id') && $incomingGoods->count() > 0 && $incomingGoods->first()->supplier)
            <span class="badge bg-info py-2 px-3 fs-7 text-dark">
                Supplier: {{ $incomingGoods->first()->supplier->nama_supplier }}
            </span>
        @endif
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
                <span class="summary-label">Total Qty Masuk</span>
                <div class="summary-val mt-1 text-success">{{ number_format($totalQty, 0, ',', '.') }} Pcs</div>
            </div>
        </div>
        <div class="col-3">
            <div class="summary-box">
                <span class="summary-label">Total Nilai Pembelian</span>
                <div class="summary-val mt-1 text-dark">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <table class="table table-striped align-middle table-print">
        <thead>
            <tr>
                <th style="width: 40px;">NO</th>
                <th style="width: 100px;">TANGGAL MASUK</th>
                <th style="width: 90px;">KODE PRODUK</th>
                <th>NAMA PRODUK</th>
                <th>SUPPLIER</th>
                <th class="text-center" style="width: 80px;">QTY MASUK</th>
                <th class="text-end" style="width: 110px;">HARGA BELI</th>
                <th class="text-end" style="width: 110px;">TOTAL NILAI</th>
                <th style="width: 90px;">NO. BATCH</th>
                <th style="width: 70px;">LOKASI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomingGoods as $index => $item)
                @php
                    $totalBaris = $item->jumlah * ($item->harga_beli ?? 0);
                @endphp
                <tr>
                    <td class="text-muted text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_masuk->translatedFormat('d M Y') }}</td>
                    <td><span class="fw-bold">{{ $item->product->kode_produk ?? '-' }}</span></td>
                    <td>
                        <span class="fw-bold">{{ $item->product->nama_produk ?? '-' }}</span>
                        @if($item->keterangan)
                            <div class="text-muted small italic" style="font-size: 0.65rem;">Ket: {{ $item->keterangan }}</div>
                        @endif
                    </td>
                    <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                    <td class="text-center fw-bold text-success">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($totalBaris, 0, ',', '.') }}</td>
                    <td><span class="font-monospace text-secondary">{{ $item->batch_code ?? '-' }}</span></td>
                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->id_lokasi ?? '-' }}</span></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="5" class="text-end">TOTAL</td>
                <td class="text-center text-success">{{ number_format($totalQty, 0, ',', '.') }}</td>
                <td></td>
                <td class="text-end text-dark">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                <td colspan="2"></td>
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
