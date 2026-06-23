@extends('layouts.app')
@section('title', 'Kategori Produk')
@section('page-title', 'Kategori Produk')

@section('content')
<style>
    /* Premium Table Styling */
    .category-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .category-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    .category-table th {
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
    .category-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    .category-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .category-table tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a; font-family: var(--font-display);">Kategori Produk</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola kategori untuk pengelompokan produk</p>
        </div>
        <a href="{{ route('kategori.create') }}" class="btn btn-primary fw-bold" style="border-radius: 8px; font-size: 0.88rem; padding: 10px 20px;" id="btnTambahKategori">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </a>
    </div>

    <div class="category-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 category-table" id="tableKategori">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>NAMA KATEGORI</th>
                        <th>DESKRIPSI</th>
                        <th class="text-center" style="width: 160px;">JUMLAH PRODUK</th>
                        <th class="text-center" style="width: 130px;">AKSI</th>
                    </tr>
                </thead>
                    <tbody>
                        @forelse($categories as $i => $category)
                        <tr>
                            <td class="text-muted">{{ $categories->firstItem() + $i }}</td>
                            <td class="fw-semibold">
                                <i class="bi bi-tag-fill text-primary me-2"></i>{{ $category->nama_kategori }}
                            </td>
                            <td class="text-muted">{{ Str::limit($category->deskripsi, 60) ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2">{{ $category->products_count }} produk</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('kategori.edit', $category) }}" class="btn btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('kategori.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                Belum ada kategori.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
        <div class="card-footer bg-white border-top">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection
