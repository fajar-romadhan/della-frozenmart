<?php $__env->startSection('title', 'Kelola Data Produk'); ?>
<?php $__env->startSection('page-title', 'Kelola Data Produk'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* Styling overrides to match the premium mockup */
    .page-title-main {
        font-family: var(--font-display);
        font-weight: 700;
        color: #0f172a;
        font-size: 1.5rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
        color: #64748b;
    }
    
    .btn-add-product {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
        text-decoration: none;
    }
    
    .btn-add-product:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
    }

    .btn-add-product i {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .btn-add-product:hover i {
        transform: rotate(90deg);
    }
    
    .filter-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .filter-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
    }
    
    .search-input-wrapper {
        position: relative;
    }
    
    .search-input-wrapper input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 40px 10px 16px;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s;
        width: 100%;
        background-color: #ffffff;
    }
    
    .search-input-wrapper input::placeholder {
        color: #94a3b8;
    }
    
    .search-input-wrapper input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        outline: none;
    }
    
    .search-input-wrapper .search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 1.15rem;
        pointer-events: none;
    }
    
    .category-select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 0.95rem;
        color: #334155;
        width: 100%;
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 12px 12px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        transition: all 0.2s;
    }
    
    .category-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        outline: none;
    }
    
    .product-table-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
        margin-bottom: 24px;
        max-width: 100%;
        overflow: hidden;
    }
    
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        display: block;
    }

    /* Style the horizontal scrollbar specifically for the table container */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
        border: 2px solid #f1f5f9;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .product-table {
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
    }
    
    .product-table th {
        font-weight: 700;
        font-size: 0.72rem;
        color: #475569;
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 8px;
        text-align: left;
        line-height: 1.3;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: normal !important;
    }
    
    .product-table th:first-child {
        border-top-left-radius: 12px;
        padding-left: 14px;
    }
    
    .product-table th:last-child {
        border-top-right-radius: 12px;
        padding-right: 14px;
    }
    
    .product-table td {
        font-size: 0.78rem;
        padding: 10px 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }
    
    .product-table td:first-child { padding-left: 14px; }
    .product-table td:last-child { padding-right: 14px; }
    
    /* Expired product row styling (Soft Red/Pink) */
    .row-expired {
        background-color: #fff1f2 !important;
    }
    
    .row-expired td {
        color: #e11d48 !important;
        background-color: #fff1f2 !important;
    }
    
    .row-expired .btn-edit-dropdown {
        border-color: #fecdd3 !important;
        color: #e11d48 !important;
        background-color: #ffffff;
    }
    
    .row-expired .btn-edit-dropdown:hover {
        background-color: #ffe4e6 !important;
    }
    
    /* Edit dropdown button styling */
    .btn-edit-dropdown {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        box-shadow: none;
    }
    
    .btn-edit-dropdown::after {
        display: none !important; /* Hide default Bootstrap caret */
    }
    
    .btn-edit-dropdown:hover, .btn-edit-dropdown[aria-expanded="true"] {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Micro-animation: rotate caret when dropdown is active */
    .btn-edit-dropdown i {
        font-size: 0.75rem;
        transition: transform 0.20s ease;
        color: #64748b;
    }
    
    .btn-edit-dropdown:hover i {
        color: #1e293b;
    }

    .btn-edit-dropdown[aria-expanded="true"] i {
        transform: rotate(180deg);
        color: #1e293b;
    }
    
    /* Dropdown action menu styling */
    .action-dropdown-menu {
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.08);
        padding: 6px;
        min-width: 220px;
        background-color: #ffffff;
    }
    
    .action-dropdown-menu .dropdown-item {
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.88rem;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s;
    }
    
    .action-dropdown-menu .dropdown-item:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    
    .action-dropdown-menu .dropdown-item i {
        font-size: 1.1rem;
        color: #64748b;
    }
    
    /* Pagination design styling to match mockup */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
    }
    
    .pagination-info {
        font-size: 0.88rem;
        color: #64748b;
        font-weight: 500;
    }
    
    .pagination {
        display: flex;
        gap: 6px;
        margin: 0;
    }
    
    .pagination .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
        background-color: #ffffff;
        transition: all 0.2s;
        box-shadow: none;
    }
    
    .pagination .page-item .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: #ffffff;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid py-2">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title-main mb-1">Kelola Data Produk</h1>
            <p class="page-subtitle mb-0">Kelola data produk yang tersedia pada sistem persediaan.</p>
        </div>
        <a href="<?php echo e(route('produk.create')); ?>" class="btn-add-product" id="btnTambahProduk">
            <i class="ph ph-plus bold"></i> Tambah Produk
        </a>
    </div>

    
    <div class="filter-card">
        <form method="GET" action="<?php echo e(route('produk.index')); ?>" class="row g-3" id="formFilterProduk">
            <div class="col-md-7">
                <label for="inputSearchProduk" class="filter-label">Cari Produk</label>
                <div class="search-input-wrapper">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode produk..." value="<?php echo e(request('search')); ?>" id="inputSearchProduk">
                    <i class="ph ph-magnifying-glass search-icon"></i>
                </div>
            </div>
            <div class="col-md-5">
                <label for="filterKategori" class="filter-label">Kategori Produk (<?php echo e($categories->count()); ?>)</label>
                <select name="category_id" class="category-select" id="filterKategori">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->nama_kategori); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </form>
    </div>

    
    <div class="product-table-card">
        <div class="table-responsive">
            <table class="table align-middle product-table" id="tableProduk">
                <thead>
                    <tr>
                        <th class="col-id">ID PRODUK</th>
                        <th class="col-code">KODE</th>
                        <th class="col-name">NAMA PRODUK</th>
                        <th class="col-category">KATEGORI</th>
                        <th class="col-unit">SATUAN</th>
                        <th class="col-stock">STOK</th>
                        <th class="col-date">TANGGAL MASUK</th>
                        <th class="col-date">KEDALUWARSA</th>
                        <th class="col-supplier">SUPPLIER</th>
                        <th class="col-action">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isExpired = $product->tanggal_kedaluwarsa && $product->tanggal_kedaluwarsa->isPast();
                    ?>
                    <tr class="<?php echo e($isExpired ? 'row-expired' : ''); ?>">
                        <td class="col-id">
                            <span class="badge bg-light text-dark border fw-bold font-monospace" style="font-size: 0.75rem; text-transform: none;"><?php echo e($product->kode_produk); ?></span>
                        </td>
                        <td class="col-code">
                            <span class="badge bg-light text-secondary border fw-semibold font-monospace" style="font-size: 0.75rem; text-transform: none;"><?php echo e($product->short_code); ?></span>
                        </td>
                        <td class="col-name fw-bold product-name-cell"><?php echo e($product->nama_produk); ?></td>
                        <td class="col-category">
                            <?php if($product->category): ?>
                                <?php if($product->category->nama_kategori == 'Belum Dikategorikan'): ?>
                                    <span class="badge bg-light text-secondary border fw-semibold" style="text-transform: none; font-size: 0.72rem;">
                                        <?php echo e($product->category->nama_kategori); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 fw-semibold" style="text-transform: none; font-size: 0.72rem;">
                                        <?php echo e($product->category->nama_kategori); ?>

                                    </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-unit"><span class="text-secondary fw-semibold"><?php echo e(ucfirst(strtolower($product->satuan))); ?></span></td>
                        <td class="col-stock">
                            <?php if($product->stok_saat_ini < 0): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 fw-bold" style="font-size: 0.78rem;">
                                    <?php echo e(number_format($product->stok_saat_ini)); ?>

                                </span>
                            <?php elseif($product->stok_saat_ini == 0): ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 fw-bold" style="font-size: 0.78rem;">
                                    Habis
                                </span>
                            <?php elseif($product->stok_saat_ini <= 5): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 fw-bold" style="font-size: 0.78rem;">
                                    <?php echo e(number_format($product->stok_saat_ini)); ?>

                                </span>
                            <?php else: ?>
                                <span class="fw-bold text-success" style="font-size: 0.8rem;"><?php echo e(number_format($product->stok_saat_ini)); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="col-date text-secondary"><?php echo e($product->latestIncomingGood?->tanggal_masuk?->format('d/m/Y') ?? '-'); ?></td>
                        <td class="col-date">
                            <?php if($product->tanggal_kedaluwarsa): ?>
                                <?php if($product->tanggal_kedaluwarsa->isPast()): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 fw-semibold" style="font-size: 0.72rem; text-transform: none;">
                                        <?php echo e($product->tanggal_kedaluwarsa->format('d/m/Y')); ?> (Expired)
                                    </span>
                                <?php elseif($product->tanggal_kedaluwarsa->diffInDays(now()) <= 30): ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 fw-semibold" style="font-size: 0.72rem; text-transform: none;">
                                        <?php echo e($product->tanggal_kedaluwarsa->format('d/m/Y')); ?> (Near)
                                    </span>
                                <?php else: ?>
                                    <span class="text-secondary"><?php echo e($product->tanggal_kedaluwarsa->format('d/m/Y')); ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-supplier">
                            <?php if($product->latestIncomingGood?->supplier): ?>
                                <span class="fw-semibold text-dark"><?php echo e($product->latestIncomingGood->supplier->nama_supplier); ?></span>
                            <?php else: ?>
                                <span class="text-muted" style="font-style: italic; font-size: 0.75rem;">Tidak Diketahui</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-action">
                            <div class="dropdown">
                                <button class="btn-edit-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span>Edit</span>
                                    <i class="ph ph-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end action-dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('produk.edit', $product)); ?>?focus=nama">
                                            <i class="ph ph-pencil-simple"></i> Edit Nama Produk
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('produk.edit', $product)); ?>?focus=kategori">
                                            <i class="ph ph-tag"></i> Edit Kategori
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('produk.edit', $product)); ?>?focus=satuan">
                                            <i class="ph ph-package"></i> Edit Satuan
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('produk.edit', $product)); ?>?focus=stok">
                                            <i class="ph ph-database"></i> Edit Stok
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('produk.edit', $product)); ?>?focus=kedaluwarsa">
                                            <i class="ph ph-calendar"></i> Edit Tanggal Kedaluwarsa
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('produk.edit', $product)); ?>?focus=supplier">
                                            <i class="ph ph-truck"></i> Edit Supplier
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="<?php echo e(route('produk.destroy', $product)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ph ph-trash text-danger"></i> Hapus Produk
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="ph ph-info fs-1 d-block mb-2"></i>
                            Belum ada data produk yang cocok.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        
        <?php if($products->hasPages() || $products->total() > 0): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan <?php echo e($products->firstItem() ?? 0); ?> - <?php echo e($products->lastItem() ?? 0); ?> dari <?php echo e($products->total() ?? 0); ?> data
            </div>
            <div>
                <?php echo e($products->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when category is selected
        const filterKategori = document.getElementById('filterKategori');
        if (filterKategori) {
            filterKategori.addEventListener('change', function() {
                document.getElementById('formFilterProduk').submit();
            });
        }

        // Optional debounce search for smoother UX (if needed)
        const searchInput = document.getElementById('inputSearchProduk');
        let timeout = null;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    document.getElementById('formFilterProduk').submit();
                }, 750); // debounce 750ms
            });
            // Focus at the end of text when search is submitted
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.focus();
            searchInput.value = val;
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\ANTIGRAVITY\della-frozenmart\resources\views/produk/index.blade.php ENDPATH**/ ?>