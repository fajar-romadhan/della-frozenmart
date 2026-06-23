@extends('layouts.app')
@section('title', 'Akses Ditolak (403)')
@section('page-title', 'Akses Ditolak')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center p-5 max-w-md">
        <div class="display-1 fw-extrabold text-danger mb-3" style="font-size: 6rem; letter-spacing: -2px;">
            403
        </div>
        <div class="mb-4">
            <span class="badge bg-danger bg-opacity-10 text-danger fs-6 px-3 py-2">
                <i class="bi bi-shield-slash-fill me-1"></i> Akses Dilarang
            </span>
        </div>
        <h3 class="fw-bold mb-3">Maaf, Anda Tidak Memiliki Akses</h3>
        <p class="text-muted mb-4 mx-auto" style="max-width: 380px;">
            Halaman yang Anda coba akses memiliki hak istimewa yang dibatasi. Hubungi administrator sistem jika Anda merasa ini adalah kesalahan.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="bi bi-house-door"></i> Kembali ke Dashboard
            </a>
            <button onclick="history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
            </button>
        </div>
    </div>
</div>
@endsection
