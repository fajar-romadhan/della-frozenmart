@extends('layouts.app')
@section('title', 'Edit Data Pengguna')
@section('page-title', 'Edit Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center">
                    <a href="{{ route('pengguna.index') }}" class="btn btn-sm btn-link text-decoration-none me-2 p-0"><i class="bi bi-arrow-left fs-5"></i></a>
                    <h5 class="mb-0 fw-bold">Form Edit Pengguna</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pengguna.update', $pengguna) }}" method="POST" id="formEditUser">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" placeholder="Masukkan nama lengkap" value="{{ old('name', $pengguna->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" placeholder="contoh: user@frozenmart.com" value="{{ old('email', $pengguna->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label fw-bold">Role Sistem</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" id="role" required
                                    {{ $pengguna->id === auth()->id() ? 'disabled' : '' }}>
                                <option value="admin" {{ old('role', $pengguna->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manager" {{ old('role', $pengguna->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="owner" {{ old('role', $pengguna->role) === 'owner' ? 'selected' : '' }}>Owner</option>
                            </select>
                            {{-- If role dropdown is disabled, we need to send it as a hidden field so it gets processed --}}
                            @if($pengguna->id === auth()->id())
                                <input type="hidden" name="role" value="{{ $pengguna->role }}">
                                <div class="form-text text-muted" style="font-size: 0.78rem;">
                                    Anda tidak dapat mengubah role akun Anda sendiri.
                                </div>
                            @endif
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnUpdateUser"><i class="bi bi-save"></i> Perbarui Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
