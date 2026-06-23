@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-1">Tambah Kategori</h4>
                    <p class="text-muted mb-0">Buat kategori baru untuk mengelompokkan produk</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('kategori.store') }}" method="POST" id="formTambahKategori">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori') }}" placeholder="Contoh: Makanan Beku" required>
                            @error('nama_kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Deskripsi kategori (opsional)">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('kategori.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
