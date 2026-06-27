@extends('layouts.app')
@section('title', 'Pemesanan Produk')
@section('page-title', 'Pemesanan Produk')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs & Header --}}
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Transaksi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pemesanan-supplier.index') }}" class="text-decoration-none">Pemesanan Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buat Pemesanan</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0" style="color: #0f172a; font-family: var(--font-display);">Pemesanan Produk</h4>
    </div>

    <form action="{{ route('pemesanan-supplier.store') }}" method="POST">
        @csrf
        
        {{-- Section 1: Data Pemesanan --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08) !important;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="ph ph-calendar-blank me-2 text-primary"></i>Data Pemesanan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Tanggal Pemesanan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="ph ph-calendar"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" value="{{ date('d/m/Y') }}" readonly disabled style="font-weight: 500;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="supplier_id" class="form-label small fw-bold text-muted">Pilih Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required style="border-radius: 8px;">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Detail Pemesanan --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08) !important;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="ph ph-shopping-bag me-2 text-primary"></i>Detail Pemesanan</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="product_id" class="form-label small fw-bold text-muted">Nama Produk <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required style="border-radius: 8px;">
                            <option value="">Pilih Produk</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id', $selectedProductId ?? '') == $p->id ? 'selected' : '' }}>
                                    {{ $p->kode_produk }} - {{ $p->nama_produk }} (Stok: {{ $p->stok_saat_ini }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah_pesan" class="form-label small fw-bold text-muted">Jumlah Produk (pcs) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah_pesan" id="jumlah_pesan" class="form-control @error('jumlah_pesan') is-invalid @enderror" value="{{ old('jumlah_pesan') }}" min="1" required style="border-radius: 8px 0 0 8px;">
                            <span class="input-group-text bg-light">pcs</span>
                        </div>
                        @error('jumlah_pesan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label for="keterangan" class="form-label small fw-bold text-muted">Keterangan Tambahan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" rows="2" class="form-control" style="border-radius: 8px;" placeholder="Tulis catatan pemesanan jika ada...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('pemesanan-supplier.index') }}" class="btn btn-light px-4 fw-semibold" style="border-radius: 8px; border: 1px solid #cbd5e1;">Batal</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold" style="border-radius: 8px;"><i class="ph ph-floppy-disk me-1"></i> Simpan Pesanan</button>
        </div>
    </form>

    {{-- Info Banner (Keterangan) --}}
    <div class="alert alert-info border-0 d-flex align-items-start shadow-sm mb-4" role="alert" style="background-color: #f0fdfa; color: #0f766e; border-radius: 12px;">
        <i class="ph ph-info fs-5 me-3" style="color: #0d9488 !important; margin-top: 2px;"></i>
        <div>
            <span class="fw-bold small d-block mb-1 text-teal-dark">Keterangan:</span>
            <span class="small">Halaman ini digunakan untuk membuat pemesanan produk kepada supplier. Pilih produk, masukkan jumlah yang diinginkan, pilih supplier penyedia, lalu simpan pesanan untuk dicatat sebagai draf pemesanan.</span>
        </div>
    </div>
</div>

<style>
    .text-teal-dark { color: #0f766e !important; }
</style>
@endsection
