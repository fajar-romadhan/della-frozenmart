<?php $role = auth()->user()->role ?? 'admin'; ?>
<?php $__env->startSection('title', $role === 'owner' ? 'Laporan Penjualan' : 'Laporan Penjualan Produk'); ?>
<?php $__env->startSection('page-title', $role === 'owner' ? 'Laporan Penjualan' : 'Laporan Penjualan Produk'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="alert alert-info border-0 d-flex align-items-center mb-4 shadow-sm" role="alert" style="background-color: #f0fdfa; color: #0f766e;">
        <i class="ph ph-info-semibold fs-4 me-3" style="color: #0d9488 !important;"></i>
        <div class="small fw-semibold">
            <?php if($role === 'owner'): ?>
                Laporan penjualan dibuat secara otomatis oleh sistem berdasarkan transaksi penjualan yang dilakukan.
            <?php else: ?>
                Laporan volume penjualan produk dibuat secara otomatis oleh sistem berdasarkan transaksi penjualan yang dilakukan.
            <?php endif; ?>
        </div>
    </div>

    
    <div class="row g-4 mb-4 align-items-stretch">
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 no-print">
                <div class="card-body d-flex flex-column justify-content-center">
                    <form method="GET" action="<?php echo e(route('laporan.penjualan')); ?>" id="formFilterPenjualan">
                        <label class="form-label small fw-bold text-muted mb-2">Periode Tanggal</label>
                        <div class="input-group mb-3">
                            <input type="date" name="tanggal_dari" class="form-control" value="<?php echo e(request('tanggal_dari')); ?>">
                            <span class="input-group-text bg-light">s/d</span>
                            <input type="date" name="tanggal_sampai" class="form-control" value="<?php echo e(request('tanggal_sampai')); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class="ph ph-funnel me-2"></i> Terapkan Filter</button>
                    </form>
                </div>
            </div>
        </div>

        
        <?php if($role === 'owner'): ?>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="background-color: #f8fafc; border: 1px solid #e2e8f0 !important;">
                <div class="card-body py-3 d-flex flex-column justify-content-center">
                    <span class="text-muted-dark small fw-bold mb-3 d-block"><i class="ph ph-chart-line me-1 text-primary"></i> Ringkasan Penjualan</span>
                    <div class="row g-3">
                        
                        <div class="col-md-4 border-end-custom">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-success-light text-success me-2">
                                    <i class="ph ph-trend-up"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">TOTAL OMZET</span>
                                    <span class="fw-bold text-dark fs-6" style="white-space: nowrap;">Rp <?php echo e(number_format($totalOmzet, 0, ',', '.')); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 border-end-custom">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-purple-light text-purple me-2">
                                    <span class="fw-bold" style="font-size: 0.75rem; font-family: var(--font-display);">Rp</span>
                                </div>
                                <div>
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">TOTAL LABA KOTOR</span>
                                    <span class="fw-bold text-success fs-6" style="white-space: nowrap;">Rp <?php echo e(number_format($totalLabaKotor, 0, ',', '.')); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-blue-light text-blue me-2">
                                    <i class="ph ph-percent"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">MARGIN PROFIT</span>
                                    <span class="fw-bold text-primary fs-6"><?php echo e(number_format($margin, 2, ',', '.')); ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="background-color: #f8fafc; border: 1px solid #e2e8f0 !important;">
                <div class="card-body py-3 d-flex flex-column justify-content-center">
                    <span class="text-muted-dark small fw-bold mb-3 d-block"><i class="ph ph-chart-line me-1 text-primary"></i> Ringkasan Penjualan Produk</span>
                    <div class="row g-3">
                        
                        <div class="col-md-6 border-end-custom">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-blue-light text-blue me-2">
                                    <i class="ph ph-package"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">TOTAL PRODUK TERJUAL</span>
                                    <span class="fw-bold text-dark fs-6" style="white-space: nowrap;"><?php echo e(number_format($totalProdukTerjual)); ?> Produk</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-warning-light text-warning me-2">
                                    <i class="ph ph-shopping-cart"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">TOTAL QTY TERJUAL</span>
                                    <span class="fw-bold text-dark fs-6" style="white-space: nowrap;"><?php echo e(number_format($totalQty)); ?> Pcs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="row g-3 mb-4">
        <?php if($role === 'owner'): ?>
            
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm bg-blue-light" style="border: 1px solid #dbeafe !important;">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-new-custom icon-blue me-3">
                            <i class="ph ph-package"></i>
                        </div>
                        <div>
                            <span class="text-muted-dark small d-block">Total Produk Terjual</span>
                            <h3 class="fw-bold mb-0 mt-1 text-blue-dark"><?php echo e(number_format($totalProdukTerjual)); ?> <span class="fs-6 fw-normal text-muted">Produk</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm bg-warning-light" style="border: 1px solid #fef9c3 !important;">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-new-custom icon-warning me-3">
                            <i class="ph ph-shopping-cart"></i>
                        </div>
                        <div>
                            <span class="text-muted-dark small d-block">Total Qty Terjual</span>
                            <h3 class="fw-bold mb-0 mt-1 text-warning-dark"><?php echo e(number_format($totalQty)); ?> <span class="fs-6 fw-normal text-muted">Pcs</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm bg-purple-light" style="border: 1px solid #f3e8ff !important;">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-new-custom icon-purple me-3">
                            <span class="fw-bold" style="font-size: 0.95rem; font-family: var(--font-display);">Rp</span>
                        </div>
                        <div>
                            <span class="text-muted-dark small d-block">Total Omzet</span>
                            <h3 class="fw-bold mb-0 mt-1 text-purple-dark" style="white-space: nowrap; font-size: clamp(1.1rem, 1.3vw, 1.4rem);">Rp <?php echo e(number_format($totalOmzet, 0, ',', '.')); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm bg-success-light" style="border: 1px solid #d1fae5 !important;">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-new-custom icon-success me-3">
                            <i class="ph ph-trend-up"></i>
                        </div>
                        <div>
                            <span class="text-muted-dark small d-block">Total Laba Kotor</span>
                            <h3 class="fw-bold mb-0 mt-1 text-success-dark" style="white-space: nowrap; font-size: clamp(1.1rem, 1.3vw, 1.4rem);">Rp <?php echo e(number_format($totalLabaKotor, 0, ',', '.')); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-blue-light" style="border: 1px solid #dbeafe !important;">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-new-custom icon-blue me-3">
                            <i class="ph ph-package"></i>
                        </div>
                        <div>
                            <span class="text-muted-dark small d-block">Total Produk Terjual</span>
                            <h3 class="fw-bold mb-0 mt-1 text-blue-dark"><?php echo e(number_format($totalProdukTerjual)); ?> <span class="fs-6 fw-normal text-muted">Produk</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-warning-light" style="border: 1px solid #fef9c3 !important;">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon-new-custom icon-warning me-3">
                            <i class="ph ph-shopping-cart"></i>
                        </div>
                        <div>
                            <span class="text-muted-dark small d-block">Total Qty Terjual</span>
                            <h3 class="fw-bold mb-0 mt-1 text-warning-dark"><?php echo e(number_format($totalQty)); ?> <span class="fs-6 fw-normal text-muted">Pcs</span></h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-0 pt-3 pb-0">
            <h5 class="mb-0 fw-bold"><i class="ph ph-file-text me-1 text-primary"></i> Data <?php echo e($role === 'owner' ? 'Laporan Penjualan' : 'Laporan Penjualan Produk'); ?></h5>
            <div class="no-print">
                <a href="<?php echo e(route('export.penjualan.pdf', request()->all())); ?>" class="btn btn-sm btn-outline-danger me-2"><i class="ph ph-file-pdf me-1"></i> Export PDF</a>
                <a href="<?php echo e(route('export.penjualan.excel', request()->all())); ?>" class="btn btn-sm btn-outline-success"><i class="ph ph-file-xls me-1"></i> Export Excel</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablePenjualan">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th>NAMA PRODUK</th>
                            <th>KATEGORI</th>
                            <th>BRAND</th>
                            <?php if($role === 'owner'): ?>
                                <th class="text-end" style="width: 150px;">HARGA JUAL<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(Rp)</span></th>
                            <?php endif; ?>
                            <th class="text-center" style="width: 140px;">TOTAL QTY<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(pcs)</span></th>
                            <?php if($role === 'owner'): ?>
                                <th class="text-end" style="width: 180px;">TOTAL OMZET<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(Rp)</span></th>
                                <th class="text-end" style="width: 180px;">LABA KOTOR<br><span class="text-muted text-lowercase font-normal small" style="font-size: 0.65rem;">(Rp)</span></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $processedSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?php echo e($index + 1); ?></td>
                                <td class="fw-bold text-dark"><?php echo e($item['nama_produk']); ?></td>
                                <td><?php echo e($item['kategori']); ?></td>
                                <td><span class="badge bg-light text-dark border fw-semibold"><?php echo e($item['brand']); ?></span></td>
                                <?php if($role === 'owner'): ?>
                                    <td class="text-end fw-semibold text-dark"><?php echo e(number_format($item['harga_jual'], 0, ',', '.')); ?></td>
                                <?php endif; ?>
                                <td class="text-center fw-bold text-dark"><?php echo e(number_format($item['total_qty'])); ?></td>
                                <?php if($role === 'owner'): ?>
                                    <td class="text-end fw-bold text-primary-dark">Rp <?php echo e(number_format($item['total_omzet'], 0, ',', '.')); ?></td>
                                    <td class="text-end fw-bold text-success">Rp <?php echo e(number_format($item['laba_kotor'], 0, ',', '.')); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($role === 'owner' ? 8 : 5); ?>" class="text-center py-5 text-muted">
                                    <i class="ph ph-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data penjualan untuk filter ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <td colspan="<?php echo e($role === 'owner' ? 5 : 4); ?>" class="text-end">TOTAL</td>
                            <td class="text-center text-dark"><?php echo e(number_format($totalQty)); ?></td>
                            <?php if($role === 'owner'): ?>
                                <td class="text-end text-primary-dark">Rp <?php echo e(number_format($totalOmzet, 0, ',', '.')); ?></td>
                                <td class="text-end text-success">Rp <?php echo e(number_format($totalLabaKotor, 0, ',', '.')); ?></td>
                            <?php endif; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    
    <div class="text-muted text-center small mt-4 no-print">
        <i class="ph ph-info-semibold me-1"></i> Data pada laporan ini diambil secara otomatis oleh sistem dari seluruh transaksi penjualan yang telah selesai.
    </div>
</div>

<style>
    /* Styling for Premium Custom Widgets */
    .bg-success-light { background-color: #ecfdf5; }
    .bg-blue-light { background-color: #eff6ff; }
    .bg-warning-light { background-color: #fffbeb; }
    .bg-purple-light { background-color: #faf5ff; }

    .text-success-dark { color: #065f46 !important; }
    .text-blue-dark { color: #1e40af !important; }
    .text-warning-dark { color: #854d0e !important; }
    .text-purple-dark { color: #6b21a8 !important; }
    .text-muted-dark { color: #475569; font-weight: 500; }

    .stat-icon-new-custom {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .mini-stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .icon-success { background-color: #d1fae5; color: #065f46; }
    .icon-blue { background-color: #dbeafe; color: #1e40af; }
    .icon-warning { background-color: #fde68a; color: #854d0e; }
    .icon-purple { background-color: #f3e8ff; color: #6b21a8; }

    .border-end-custom {
        border-right: 1px solid #e2e8f0;
    }
    @media (max-width: 768px) {
        .border-end-custom {
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
        }
    }

    #tablePenjualan th {
        background-color: #f8fafc;
        color: #1e293b;
        font-family: var(--font-display);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
    }

    #tablePenjualan td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }

    .font-normal {
        font-weight: 400 !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\ANTIGRAVITY\della-frozenmart\resources\views/reports/penjualan.blade.php ENDPATH**/ ?>