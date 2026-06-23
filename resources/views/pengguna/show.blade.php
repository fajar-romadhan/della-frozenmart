@extends('layouts.app')
@section('title', 'Detail Pengguna')
@section('page-title', 'Detail Pengguna')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center">
                    <a href="{{ route('pengguna.index') }}" class="btn btn-sm btn-link text-decoration-none me-2 p-0"><i class="bi bi-arrow-left fs-5"></i></a>
                    <h5 class="mb-0 fw-bold">Profil Pengguna</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="navbar-user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.25rem; border-radius: 50%;">
                        {{ strtoupper(substr($pengguna->name, 0, 1)) }}
                    </div>
                    <h4 class="fw-bold mb-1">{{ $pengguna->name }}</h4>
                    <p class="text-muted mb-3">{{ $pengguna->email }}</p>
                    
                    <span class="badge mb-4
                        @if($pengguna->role === 'admin') badge-role-admin
                        @elseif($pengguna->role === 'manager') badge-role-manager
                        @else badge-role-owner
                        @endif" style="font-size: 0.85rem; padding: 8px 16px;">
                        {{ ucfirst($pengguna->role) }}
                    </span>

                    <hr class="my-4">

                    <div class="row text-start mb-4">
                        <div class="col-6 text-muted mb-2">Status Akun</div>
                        <div class="col-6 fw-bold mb-2">
                            @if($pengguna->status_aktif)
                                <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> Nonaktif</span>
                            @endif
                        </div>
                        
                        <div class="col-6 text-muted mb-2">Tanggal Terdaftar</div>
                        <div class="col-6 fw-semibold mb-2">
                            {{ $pengguna->created_at ? $pengguna->created_at->format('d F Y H:i') : '-' }}
                        </div>
                        
                        <div class="col-6 text-muted">Terakhir Diupdate</div>
                        <div class="col-6 fw-semibold">
                            {{ $pengguna->updated_at ? $pengguna->updated_at->format('d F Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('pengguna.edit', $pengguna) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Akun</a>
                        <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
