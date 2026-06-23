@extends('layouts.app')
@section('title', 'Import Data Penjualan')
@section('page-title', 'Import Data Penjualan')

@section('content')
<style>
    /* Premium Table Styling */
    .import-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .import-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .import-table th {
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
    .import-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .import-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .import-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>
<div class="container-fluid py-2">
    @if(session('error') || $errors->any())
        <div class="alert alert-danger">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    @if(session('success_import'))
        <div class="alert alert-success">
            <h5><i class="bi bi-check-circle-fill"></i> Import Berhasil!</h5>
            <p>File <strong>{{ session('success_import')['nama_file'] }}</strong> telah diimport.</p>
            <ul>
                <li>Baris Berhasil: {{ session('success_import')['jumlah_berhasil'] }}</li>
                <li>Baris Gagal: {{ session('success_import')['jumlah_gagal'] }}</li>
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-upload"></i> Upload Data Penjualan</h5>
                </div>
                <div class="card-body">
                    <form id="form-upload" action="{{ route('import-penjualan.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required id="file-input">
                            <div class="form-text">
                                Format kolom: <strong>Tanggal, Nama Barang, Jumlah</strong>. Header pada baris pertama.
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="kurangi_stok" id="kurangi_stok" checked>
                            <label class="form-check-label fw-bold" for="kurangi_stok">Kurangi Stok Produk (FIFO)</label>
                            <div class="form-text text-danger" style="font-size: 0.75rem;">
                                Mengurangi stok produk saat ini secara otomatis sesuai jumlah penjualan menggunakan metode FIFO.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="btn-preview">
                            <i class="bi bi-search"></i> Preview Import
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Import Penjualan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 import-table">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>NAMA FILE</th>
                                    <th>BERHASIL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($importLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y') }}</td>
                                        <td title="{{ $log->nama_file }}">{{ Str::limit($log->nama_file, 15) }}</td>
                                        <td><span class="badge bg-success">{{ $log->jumlah_berhasil }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Belum ada riwayat import</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm" id="preview-card" style="display: none;">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-eye"></i> Preview Hasil Import</h5>
                    <form action="{{ route('import-penjualan.store') }}" method="POST" id="form-store">
                        @csrf
                        {{-- Hidden field to pass checkbox value to store --}}
                        <input type="hidden" name="kurangi_stok" id="hidden_kurangi_stok" value="1">
                        <button type="submit" class="btn btn-light btn-sm fw-bold" id="btn-simpan" disabled>
                            <i class="bi bi-save"></i> Simpan Import
                        </button>
                    </form>
                </div>
                <div class="card-body bg-light border-bottom">
                    <div class="row text-center">
                        <div class="col-4">
                            <h6>Total Baris</h6>
                            <h4 id="stat-total" class="text-primary">0</h4>
                        </div>
                        <div class="col-4">
                            <h6>Data Valid</h6>
                            <h4 id="stat-valid" class="text-success">0</h4>
                        </div>
                        <div class="col-4">
                            <h6>Data Error</h6>
                            <h4 id="stat-error" class="text-danger">0</h4>
                        </div>
                    </div>
                </div>
                
                <ul class="nav nav-tabs px-3 pt-3" id="importTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-success fw-bold" id="valid-tab" data-bs-toggle="tab" data-bs-target="#valid" type="button" role="tab">Data Valid</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger fw-bold" id="error-tab" data-bs-toggle="tab" data-bs-target="#error" type="button" role="tab">Data Error</button>
                    </li>
                </ul>
                <div class="tab-content" id="importTabContent">
                    <div class="tab-pane fade show active" id="valid" role="tabpanel">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0 import-table">
                                <thead class="sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">NO</th>
                                        <th>TANGGAL</th>
                                        <th>NAMA BARANG</th>
                                        <th class="text-end">JUMLAH PENJUALAN</th>
                                        <th class="text-end">STOK SAAT INI</th>
                                    </tr>
                                </thead>
                                <tbody id="table-valid-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="error" role="tabpanel">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0 import-table">
                                <thead class="sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 130px;">NO BARIS EXCEL</th>
                                        <th>TANGGAL</th>
                                        <th>NAMA BARANG</th>
                                        <th>PESAN ERROR</th>
                                    </tr>
                                </thead>
                                <tbody id="table-error-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="loading-indicator" class="text-center py-5" style="display:none;">
                <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mt-3 text-muted">Sedang memproses file excel...</h5>
            </div>
            
            <div id="initial-empty-state" class="text-center py-5 text-muted">
                <i class="bi bi-file-earmark-spreadsheet" style="font-size: 5rem; opacity: 0.2"></i>
                <h4>Belum ada data preview</h4>
                <p>Silakan upload file data penjualan di sebelah kiri.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formUpload = document.getElementById('form-upload');
    const btnPreview = document.getElementById('btn-preview');
    const previewCard = document.getElementById('preview-card');
    const loadingIndicator = document.getElementById('loading-indicator');
    const emptyState = document.getElementById('initial-empty-state');
    const kurangiStokCheckbox = document.getElementById('kurangi_stok');
    const hiddenKurangiStok = document.getElementById('hidden_kurangi_stok');

    // Keep hidden input in sync with checkbox
    kurangiStokCheckbox.addEventListener('change', function() {
        hiddenKurangiStok.value = this.checked ? "1" : "0";
    });
    
    formUpload.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('file-input');
        if (!fileInput.files.length) return;
        
        const formData = new FormData(this);
        
        btnPreview.disabled = true;
        btnPreview.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
        
        emptyState.style.display = 'none';
        previewCard.style.display = 'none';
        loadingIndicator.style.display = 'block';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            btnPreview.disabled = false;
            btnPreview.innerHTML = '<i class="bi bi-search"></i> Preview Import';
            loadingIndicator.style.display = 'none';
            
            if (data.success) {
                renderPreview(data.data);
                previewCard.style.display = 'block';
            } else {
                alert('Error: ' + data.message);
                emptyState.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btnPreview.disabled = false;
            btnPreview.innerHTML = '<i class="bi bi-search"></i> Preview Import';
            loadingIndicator.style.display = 'none';
            emptyState.style.display = 'block';
            alert('Terjadi kesalahan jaringan.');
        });
    });
    
    function renderPreview(data) {
        document.getElementById('stat-total').textContent = data.total_rows;
        document.getElementById('stat-valid').textContent = data.valid_rows.length;
        document.getElementById('stat-error').textContent = data.error_rows.length;
        
        document.getElementById('btn-simpan').disabled = data.valid_rows.length === 0;
        
        // Render Valid Table
        const validBody = document.getElementById('table-valid-body');
        validBody.innerHTML = '';
        if (data.valid_rows.length > 0) {
            data.valid_rows.forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center fw-semibold text-muted">${index + 1}</td>
                    <td>${row.tanggal}</td>
                    <td>${row.nama_barang}</td>
                    <td class="text-end fw-bold">${row.jumlah}</td>
                    <td class="text-end">${row.stok_saat_ini || 0}</td>
                `;
                validBody.appendChild(tr);
            });
        } else {
            validBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data valid</td></tr>';
        }
        
        // Render Error Table
        const errorBody = document.getElementById('table-error-body');
        errorBody.innerHTML = '';
        if (data.error_rows.length > 0) {
            data.error_rows.forEach((row) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center fw-semibold text-muted">${row.row_number}</td>
                    <td>${row.tanggal || '-'}</td>
                    <td>${row.nama_barang || '-'}</td>
                    <td class="text-danger">${row.error}</td>
                `;
                errorBody.appendChild(tr);
            });
        } else {
            errorBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Tidak ada data error</td></tr>';
        }
    }
});
</script>
@endpush
