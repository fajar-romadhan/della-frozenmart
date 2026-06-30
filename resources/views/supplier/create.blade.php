@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary me-3"><i class="ph ph-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-1">Tambah Supplier Baru</h4>
                    <p class="text-muted mb-0">Isi formulir di bawah untuk menambahkan supplier / pemasok baru</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('supplier.store') }}" method="POST" id="formTambahSupplier">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_supplier" class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="nama_supplier" id="nama_supplier" class="form-control @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier') }}" placeholder="Contoh: PT. Sumber Frozen" required>
                            @error('nama_supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="telepon" class="form-label fw-semibold">No. Telepon</label>
                                <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}" placeholder="Contoh: 0812-3456-7890">
                                @error('telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: info@sumberfrozen.co.id">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status_aktif" class="form-label fw-semibold">Status Supplier <span class="text-danger">*</span></label>
                                <select name="status_aktif" id="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                                    <option value="1" {{ old('status_aktif') == '1' || old('status_aktif') === null ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status_aktif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" placeholder="Contoh: Jl. Raya Bekasi No. 123, Jakarta Timur">{{ old('alamat') }}</textarea>
                            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        


                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('supplier.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnSimpanSupplier"><i class="ph ph-check bold"></i> Simpan Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
