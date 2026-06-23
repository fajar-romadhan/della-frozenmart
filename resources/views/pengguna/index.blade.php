@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Pengguna')

@section('content')
<style>
    /* Premium Table Styling */
    .user-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .user-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .user-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #ffffff !important;
        background-color: #1e293b !important; /* Premium Navy Blue */
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 8px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: normal !important;
    }
    .user-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .user-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .user-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a; font-family: var(--font-display);">Manajemen Pengguna</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola pengguna sistem informasi Della Frozen Mart</p>
        </div>
        <a href="{{ route('pengguna.create') }}" class="btn btn-primary fw-bold" style="border-radius: 8px; font-size: 0.88rem; padding: 10px 20px;" id="btnTambahPengguna">
            <i class="bi bi-person-plus"></i> Tambah Pengguna
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08);">
        <div class="card-body">
            <form method="GET" action="{{ route('pengguna.index') }}" class="row g-3" id="formFilterPengguna">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white" style="border-radius: 8px 0 0 8px;"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" style="border-radius: 0 8px 8px 0;" placeholder="Cari nama atau email..." value="{{ request('search') }}" id="inputSearchUser">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="role" class="form-select" style="border-radius: 8px;" id="filterRole">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 8px;" id="btnFilter"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="user-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 user-table" id="tablePengguna">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>NAMA</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th class="text-center" style="width: 100px;">STATUS</th>
                        <th class="text-center" style="width: 200px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                        @forelse($users as $i => $u)
                            <tr>
                                <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                                <td class="fw-semibold">
                                    <div class="d-flex align-items-center">
                                        <div class="navbar-user-avatar me-2" style="width: 32px; height: 32px; font-size: 0.85rem">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        {{ $u->name }}
                                    </div>
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="badge 
                                        @if($u->role === 'admin') badge-role-admin
                                        @elseif($u->role === 'manager') badge-role-manager
                                        @else badge-role-owner
                                        @endif">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($u->status_aktif)
                                        <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        {{-- Reset Password --}}
                                        <form action="{{ route('pengguna.reset-password', $u) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset password pengguna ini ke \'password\'?')" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-info" title="Reset Password ke 'password'">
                                                <i class="bi bi-key"></i>
                                            </button>
                                        </form>

                                        {{-- Toggle Status --}}
                                        <form action="{{ route('pengguna.toggle-status', $u) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn {{ $u->status_aktif ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                    title="{{ $u->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                                                <i class="bi {{ $u->status_aktif ? 'bi-shield-slash' : 'bi-shield-check' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <a href="{{ route('pengguna.edit', $u) }}" class="btn btn-outline-primary" title="Edit Data"><i class="bi bi-pencil"></i></a>

                                        {{-- Delete --}}
                                        <form action="{{ route('pengguna.destroy', $u) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus Pengguna"
                                                    {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    Tidak ada pengguna yang cocok dengan pencarian Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
