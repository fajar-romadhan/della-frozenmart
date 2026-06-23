@extends('layouts.app')
@section('title', 'Log Import Data')
@section('page-title', 'Log Import Data')

@section('content')
<style>
    /* Premium Table Styling */
    .log-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .log-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .log-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #ffffff !important;
        background-color: #1e293b !important; /* Premium Navy Blue */
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 8px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: normal !important;
    }
    .log-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .log-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .log-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    {{-- Filter Card --}}
    <div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08);">
        <div class="card-header bg-white" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding: 14px 20px;">
            <h5 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #0f172a;"><i class="bi bi-funnel text-primary"></i> Filter Jenis Import</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.import-log') }}" class="row g-3" id="formFilterImportLog">
                <div class="col-md-9">
                    <label class="form-label fw-bold" style="font-size: 0.85rem; color: #334155;">Jenis Import</label>
                    <select name="jenis_import" class="form-select" style="border-radius: 8px;">
                        <option value="">Semua Jenis</option>
                        <option value="penjualan" {{ request('jenis_import') === 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                        <option value="faktur_pembelian" {{ request('jenis_import') === 'faktur_pembelian' ? 'selected' : '' }}>Faktur Pembelian</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 8px;"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Table --}}
    <div class="log-table-card">
        <div class="card-header bg-white" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding: 14px 20px;">
            <h5 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #0f172a;"><i class="bi bi-journal-text"></i> Riwayat & Log Import Sistem</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 log-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>WAKTU IMPORT</th>
                        <th>OLEH</th>
                        <th>JENIS IMPORT</th>
                        <th>NAMA FILE</th>
                        <th class="text-center">TOTAL BARIS</th>
                        <th class="text-center">BERHASIL</th>
                        <th class="text-center">GAGAL</th>
                        <th class="text-center">DETAIL ERROR</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse($logs as $i => $log)
                            <tr>
                                <td class="text-muted">{{ $logs->firstItem() + $i }}</td>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }} <small class="text-muted">({{ $log->created_at->diffForHumans() }})</small></td>
                                <td class="fw-semibold">{{ $log->user->name ?? '-' }}</td>
                                <td>
                                    @if($log->jenis_import === 'penjualan')
                                        <span class="badge bg-success bg-opacity-10 text-success">Penjualan</span>
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Faktur Pembelian</span>
                                    @endif
                                </td>
                                <td class="fw-medium text-dark" title="{{ $log->nama_file }}">{{ Str::limit($log->nama_file, 30) }}</td>
                                <td class="text-center fw-bold">{{ number_format($log->jumlah_baris) }}</td>
                                <td class="text-center text-success fw-bold">{{ number_format($log->jumlah_berhasil) }}</td>
                                <td class="text-center text-danger fw-bold">{{ number_format($log->jumlah_gagal) }}</td>
                                <td class="text-center">
                                    @if($log->jumlah_gagal > 0 && $log->catatan_error)
                                        <button class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.8rem" 
                                                data-bs-toggle="modal" data-bs-target="#errorModal{{ $log->id }}">
                                            <i class="bi bi-exclamation-triangle"></i> Lihat ({{ $log->jumlah_gagal }})
                                        </button>

                                        <!-- Modal for Error Log Detail -->
                                        <div class="modal fade text-start" id="errorModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Catatan Error: {{ Str::limit($log->nama_file, 30) }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="fw-bold mb-2">Daftar baris bermasalah pada file excel:</p>
                                                        <div class="table-responsive" style="max-height: 400px;">
                                                            <table class="table table-bordered table-striped table-sm">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        <th>No Baris</th>
                                                                        <th>Tanggal</th>
                                                                        <th>Nama Barang</th>
                                                                        <th>Deskripsi Error</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $errorsList = json_decode($log->catatan_error, true);
                                                                    @endphp
                                                                    @if(is_array($errorsList))
                                                                        @foreach($errorsList as $err)
                                                                            <tr>
                                                                                <td class="fw-bold">{{ $err['row_number'] ?? '-' }}</td>
                                                                                <td>{{ $err['tanggal'] ?? '-' }}</td>
                                                                                <td>{{ $err['nama_barang'] ?? '-' }}</td>
                                                                                <td class="text-danger fw-medium">{{ $err['error'] ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="4" class="text-center text-muted">Format error tidak valid</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Bersih</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada log import data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
