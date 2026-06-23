@extends('layouts.app')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-1">Edit Produk</h4>
                    <p class="text-muted mb-0">{{ $produk->kode_produk }} — {{ $produk->nama_produk }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('produk.update', $produk) }}" method="POST" id="formEditProduk">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama_produk" id="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
                            @error('nama_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $produk->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="satuan" class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                                <select name="satuan" id="satuan" class="form-select @error('satuan') is-invalid @enderror" required>
                                    @foreach(['PCS','PACK','BOX','KG','GRAM','LITER','LUSIN','BOTOL','BUNGKUS','KARTON'] as $s)
                                        <option value="{{ $s }}" {{ old('satuan', $produk->satuan) == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="stok_minimum" class="form-label fw-semibold">Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" name="stok_minimum" id="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" value="{{ old('stok_minimum', $produk->stok_minimum) }}" min="0" required>
                                @error('stok_minimum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_kedaluwarsa" class="form-label fw-semibold">Tanggal Kedaluwarsa</label>
                                <input type="date" name="tanggal_kedaluwarsa" id="tanggal_kedaluwarsa" class="form-control @error('tanggal_kedaluwarsa') is-invalid @enderror" value="{{ old('tanggal_kedaluwarsa', $produk->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($produk->tanggal_kedaluwarsa)->format('Y-m-d') : '') }}">
                                @error('tanggal_kedaluwarsa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status_aktif" class="form-label fw-semibold">Status</label>
                                <select name="status_aktif" id="status_aktif" class="form-select">
                                    <option value="1" {{ old('status_aktif', $produk->status_aktif) ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !old('status_aktif', $produk->status_aktif) ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('produk.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnUpdateProduk"><i class="bi bi-check-lg"></i> Update Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
