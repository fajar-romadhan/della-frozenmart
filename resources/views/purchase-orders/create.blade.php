@extends('layouts.app')
@section('title', 'Buat Pemesanan')
@section('page-title', 'Buat Pemesanan Supplier')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('pemesanan-supplier.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-1">Buat Pemesanan Baru</h4>
                    <p class="text-muted mb-0">Buat draft pemesanan ke supplier</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('pemesanan-supplier.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">Pilih Produk</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', $selectedProductId ?? '') == $p->id ? 'selected' : '' }}>{{ $p->kode_produk }} - {{ $p->nama_produk }} (Stok: {{ $p->stok_saat_ini }})</option>
                                @endforeach
                            </select>
                            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_pesan" class="form-label fw-semibold">Jumlah Pesan <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_pesan" id="jumlah_pesan" class="form-control @error('jumlah_pesan') is-invalid @enderror" value="{{ old('jumlah_pesan') }}" min="1" required>
                            @error('jumlah_pesan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control">{{ old('keterangan') }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('pemesanan-supplier.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Draft</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
