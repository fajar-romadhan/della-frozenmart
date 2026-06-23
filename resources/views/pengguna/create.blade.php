@extends('layouts.app')
@section('title', 'Tambah Pengguna Baru')
@section('page-title', 'Tambah Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center">
                    <a href="{{ route('pengguna.index') }}" class="btn btn-sm btn-link text-decoration-none me-2 p-0"><i class="bi bi-arrow-left fs-5"></i></a>
                    <h5 class="mb-0 fw-bold">Form Tambah Pengguna</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pengguna.store') }}" method="POST" id="formCreateUser">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" placeholder="Masukkan nama lengkap pengguna" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" placeholder="contoh: user@frozenmart.com" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Role Sistem</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" id="role" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" placeholder="Minimal 8 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                   id="password_confirmation" placeholder="Ulangi password" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnSimpanUser"><i class="bi bi-save"></i> Simpan Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
