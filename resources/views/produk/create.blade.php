@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-1">Tambah Produk Baru</h4>
                    <p class="text-muted mb-0">Isi formulir di bawah untuk menambahkan produk</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('produk.store') }}" method="POST" id="formTambahProduk">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama_produk" id="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk') }}" placeholder="Contoh: Nugget Ayam 500gr" required>
                            @error('nama_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="satuan" class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                                <select name="satuan" id="satuan" class="form-select @error('satuan') is-invalid @enderror" required>
                                    <option value="">Pilih Satuan</option>
                                    @foreach(['PCS','PACK','BOX','KG','GRAM','LITER','LUSIN','BOTOL','BUNGKUS','KARTON'] as $s)
                                        <option value="{{ $s }}" {{ old('satuan') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_kedaluwarsa" class="form-label fw-semibold">Tanggal Kedaluwarsa</label>
                            <input type="date" name="tanggal_kedaluwarsa" id="tanggal_kedaluwarsa" class="form-control @error('tanggal_kedaluwarsa') is-invalid @enderror" value="{{ old('tanggal_kedaluwarsa') }}" min="2026-01-01">
                            @error('tanggal_kedaluwarsa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('produk.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnSimpanProduk"><i class="bi bi-check-lg"></i> Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
