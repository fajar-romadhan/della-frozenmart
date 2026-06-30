@extends('layouts.app')
@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-link text-decoration-none me-2 p-0"><i class="bi bi-arrow-left fs-5"></i></a>
                    <h5 class="mb-0 fw-bold">Ganti Password Akun</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('password.update') }}" method="POST" id="formChangePassword">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-bold">Password Saat Ini</label>
                            <div class="input-group">
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" placeholder="Masukkan password lama Anda" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password', 'toggle_current')">
                                    <i class="bi bi-eye-slash" id="toggle_current"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-bold">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" placeholder="Masukkan password baru (minimal 8 karakter)" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password', 'toggle_new')">
                                    <i class="bi bi-eye-slash" id="toggle_new"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-bold">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" class="form-control" 
                                       id="new_password_confirmation" placeholder="Ulangi password baru Anda" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password_confirmation', 'toggle_confirm')">
                                    <i class="bi bi-eye-slash" id="toggle_confirm"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btnUpdatePassword"><i class="bi bi-key"></i> Perbarui Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input && icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }
    }
</script>
@endsection
