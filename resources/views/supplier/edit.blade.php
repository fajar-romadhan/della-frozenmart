@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary me-3"><i class="ph ph-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-1">Edit Supplier</h4>
                    <p class="text-muted mb-0">Perbarui data pemasok: {{ $supplier->nama_supplier }}</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('supplier.update', $supplier) }}" method="POST" id="formEditSupplier">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_supplier" class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" id="nama_supplier" class="form-control @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
                            @error('nama_supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kontak" class="form-label fw-semibold">Nama Kontak Person</label>
                                <input type="text" name="kontak" id="kontak" class="form-control @error('kontak') is-invalid @enderror" value="{{ old('kontak', $supplier->kontak) }}">
                                @error('kontak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telepon" class="form-label fw-semibold">No. Telepon</label>
                                <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $supplier->telepon) }}">
                                @error('telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $supplier->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status_aktif" class="form-label fw-semibold">Status Supplier <span class="text-danger">*</span></label>
                                <select name="status_aktif" id="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                                    <option value="1" {{ old('status_aktif', $supplier->status_aktif ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status_aktif', $supplier->status_aktif ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status_aktif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $supplier->alamat) }}</textarea>
                            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-semibold">Keterangan Tambahan</label>
                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $supplier->keterangan) }}</textarea>
                            @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('supplier.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnUpdateSupplier"><i class="ph ph-check bold"></i> Update Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
