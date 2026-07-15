@extends('layouts.app')
@section('title', 'Log Aktivitas Sistem')
@section('page-title', 'Log Aktivitas Sistem')

@section('content')
<style>
    /* Premium Table & Filter Styling */
    .log-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .log-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .log-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #ffffff !important;
        background-color: #1e293b !important; /* Premium Navy Blue */
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 10px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap !important;
    }
    .log-table td {
        font-size: 0.78rem;
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .log-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .log-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-custom {
        font-weight: 600;
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
    }
</style>

<div class="container-fluid py-2">
    {{-- Filter Card --}}
    <div class="card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.08);">
        <div class="card-header bg-white" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding: 14px 20px;">
            <h5 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #0f172a;">
                <i class="ph ph-funnel text-primary fs-5 align-middle me-1"></i> Filter Log Aktivitas
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.activity-log') }}" class="row g-3" id="formFilterActivityLog">
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.82rem; color: #334155;">Pengguna</label>
                    <select name="user_id" class="form-select select2" style="border-radius: 8px; font-size: 0.8rem;">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.82rem; color: #334155;">Tipe Aktivitas</label>
                    <select name="tipe" class="form-select" style="border-radius: 8px; font-size: 0.8rem;">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('tipe') === $type ? 'selected' : '' }}>
                                {{ strtoupper($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold" style="font-size: 0.82rem; color: #334155;">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" class="form-control" style="border-radius: 8px; font-size: 0.8rem;" value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold" style="font-size: 0.82rem; color: #334155;">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" class="form-control" style="border-radius: 8px; font-size: 0.8rem;" value="{{ request('tanggal_sampai') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 8px; font-size: 0.8rem; height: 38px;">
                        <i class="ph ph-funnel align-middle"></i> Filter
                    </button>
                    <a href="{{ route('laporan.activity-log') }}" class="btn btn-outline-secondary w-100 fw-bold" style="border-radius: 8px; font-size: 0.8rem; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Table --}}
    <div class="log-table-card">
        <div class="card-header bg-white d-flex align-items-center justify-content-between" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding: 14px 20px;">
            <h5 class="mb-0 fw-bold" style="font-size: 0.95rem; color: #0f172a;">
                <i class="ph ph-clock-counter-clockwise text-primary fs-5 align-middle me-1"></i> Riwayat Audit Log Aktivitas Sistem
            </h5>
            <span class="badge bg-secondary" style="font-size: 0.75rem; border-radius: 6px;">
                Total: {{ $logs->total() }} Aktivitas
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 log-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th style="width: 150px;">WAKTU</th>
                        <th style="width: 180px;">PENGGUNA</th>
                        <th style="width: 120px;">TIPE</th>
                        <th style="width: 200px;">AKTIVITAS</th>
                        <th>DESKRIPSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $badgeColors = [
                            'login' => 'bg-secondary bg-opacity-10 text-secondary',
                            'logout' => 'bg-secondary bg-opacity-10 text-secondary',
                            'create' => 'bg-success bg-opacity-10 text-success',
                            'update' => 'bg-warning bg-opacity-10 text-warning',
                            'delete' => 'bg-danger bg-opacity-10 text-danger',
                            'export' => 'bg-info bg-opacity-10 text-info',
                            'import' => 'bg-info bg-opacity-10 text-info',
                            'incoming' => 'bg-primary bg-opacity-10 text-primary',
                            'outgoing' => 'bg-danger bg-opacity-10 text-danger',
                            'opname' => 'bg-dark bg-opacity-10 text-dark',
                        ];
                        ];
                    @endphp

                    @forelse($logs as $i => $log)
                        <tr>
                            <td class="text-muted text-center">{{ $logs->firstItem() + $i }}</td>
                            <td>
                                <div class="fw-semibold">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size: 0.8rem;">{{ $log->user->name ?? 'System' }}</div>
                            </td>
                            <td>
                                <span class="badge-custom {{ $badgeColors[strtolower($log->tipe)] ?? 'bg-secondary bg-opacity-10 text-secondary' }}">
                                    {{ $log->tipe }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $log->judul }}</td>
                            <td class="text-wrap" style="max-width: 300px; font-size: 0.75rem; line-height: 1.35;">{{ $log->deskripsi }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="ph ph-info fs-3 d-block mb-2"></i>
                                Tidak ditemukan data log aktivitas yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top-0 d-flex justify-content-center py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
