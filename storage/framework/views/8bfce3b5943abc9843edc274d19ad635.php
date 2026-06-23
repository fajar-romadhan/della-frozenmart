
<nav class="sidebar" id="appSidebar">
    
    <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-logo">
        <div class="logo-icon-wrapper">
            <i class="ph ph-warehouse"></i>
        </div>
        <div class="sidebar-logo-text-wrapper">
            <span class="logo-title">Sistem Persediaan</span>
            <span class="logo-subtitle">Frozen Food</span>
        </div>
    </a>

    
    <div class="sidebar-menu-scroll">
    <div class="sidebar-menu">
        <?php $role = auth()->user()->role ?? 'admin'; ?>

        
        
        
        <div class="sidebar-category">
            <span class="category-text">Menu Utama</span>
            <div class="category-line"></div>
        </div>
        
        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
            <i class="ph ph-house"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>

        
        
        
        <?php if($role === 'admin'): ?>
            <div class="sidebar-category">
                <span class="category-text">Master Data</span>
                <div class="category-line"></div>
            </div>

            <a href="<?php echo e(route('produk.index')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('produk.*') ? 'active' : ''); ?>">
                <i class="ph ph-package"></i>
                <span class="sidebar-text">Kelola Data Produk</span>
            </a>
            
            <a href="<?php echo e(route('supplier.index')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('supplier.*') ? 'active' : ''); ?>">
                <i class="ph ph-truck"></i>
                <span class="sidebar-text">Kelola Supplier</span>
            </a>
        <?php endif; ?>

        
        
        
        <?php if($role === 'admin' || $role === 'manager'): ?>
            <div class="sidebar-category">
                <span class="category-text">Transaksi</span>
                <div class="category-line"></div>
            </div>

            <a href="<?php echo e(route('barang-masuk.index')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('barang-masuk.*') ? 'active' : ''); ?>">
                <i class="ph ph-arrow-circle-down"></i>
                <span class="sidebar-text">Barang Masuk</span>
            </a>

            <a href="<?php echo e(route('barang-keluar.index')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('barang-keluar.*') ? 'active' : ''); ?>">
                <i class="ph ph-arrow-circle-up"></i>
                <span class="sidebar-text">Barang Keluar</span>
            </a>

            <a href="<?php echo e(route('stok-opname.index')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('stok-opname.*') ? 'active' : ''); ?>">
                <i class="ph ph-clipboard-text"></i>
                <span class="sidebar-text">Stok Opname</span>
            </a>

            <?php if($role === 'manager'): ?>
                <a href="<?php echo e(route('import-penjualan.index')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('import-penjualan.*') ? 'active' : ''); ?>">
                    <i class="ph ph-file-arrow-up"></i>
                    <span class="sidebar-text">Import Penjualan</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>


        
        
        
        <div class="sidebar-category">
            <span class="category-text">Analisa</span>
            <div class="category-line"></div>
        </div>

        <?php
            $isAnalisaActive = request()->routeIs('analisis.*') || request()->routeIs('notifikasi.*') || request()->routeIs('status-stok*');
            $unreadCount = auth()->user()->notifications()->where('status_baca', false)->count();
        ?>
        <div class="sidebar-dropdown">
            <button class="sidebar-icon sidebar-dropdown-toggle <?php echo e($isAnalisaActive ? 'active' : ''); ?>" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseAnalisa" 
                    aria-expanded="<?php echo e($isAnalisaActive ? 'true' : 'false'); ?>">
                <i class="ph ph-chart-bar"></i>
                <span class="sidebar-text">Analisa Persediaan</span>
                <i class="ph ph-caret-down dropdown-arrow"></i>
                <?php if($unreadCount > 0): ?>
                    <span class="sidebar-notif-badge parent-notif-badge"><?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?></span>
                <?php endif; ?>
            </button>
            <div class="collapse <?php echo e($isAnalisaActive ? 'show' : ''); ?>" id="collapseAnalisa">
                <div class="sidebar-submenu-list">
                    <?php if($role === 'admin' || $role === 'manager'): ?>
                        <a href="<?php echo e(route('analisis.index')); ?>" class="sidebar-submenu-item <?php echo e(request()->routeIs('analisis.*') ? 'active' : ''); ?>">
                            <i class="ph ph-circle"></i>
                            <span class="sidebar-text">Analisa Persediaan</span>
                        </a>
                    <?php endif; ?>

                    <?php if($role === 'owner'): ?>
                        <a href="<?php echo e(route('status-stok')); ?>" class="sidebar-submenu-item <?php echo e(request()->routeIs('status-stok*') ? 'active' : ''); ?>">
                            <i class="ph ph-circle"></i>
                            <span class="sidebar-text">Status Stok</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo e(route('notifikasi.index')); ?>" class="sidebar-submenu-item <?php echo e(request()->routeIs('notifikasi.*') ? 'active' : ''); ?>">
                        <i class="ph ph-circle"></i>
                        <span class="sidebar-text">Notifikasi Stok</span>
                        <?php if($unreadCount > 0): ?>
                            <span class="sidebar-notif-badge" style="position: relative; top: 0; right: 0; margin-left: auto; border: none; transform: none;"><?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>

        
        
        
        <div class="sidebar-category">
            <span class="category-text">Laporan</span>
            <div class="category-line"></div>
        </div>

        <a href="<?php echo e(route('laporan.barang-masuk')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('laporan.barang-masuk') ? 'active' : ''); ?>">
            <i class="ph ph-file-text"></i>
            <span class="sidebar-text">Laporan Barang Masuk</span>
        </a>

        <?php if($role === 'admin' || $role === 'owner'): ?>
            <a href="<?php echo e(route('laporan.barang-keluar')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('laporan.barang-keluar') ? 'active' : ''); ?>">
                <i class="ph ph-file-text"></i>
                <span class="sidebar-text">Laporan Barang Keluar</span>
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('laporan.penjualan')); ?>" class="sidebar-icon <?php echo e(request()->routeIs('laporan.penjualan') ? 'active' : ''); ?>">
            <i class="ph ph-shopping-cart"></i>
            <span class="sidebar-text"><?php echo e($role === 'owner' ? 'Laporan Penjualan' : 'Laporan Penjualan Produk'); ?></span>
        </a>
    </div>
    </div>

    
    <div class="sidebar-bottom">
        <form action="<?php echo e(route('logout')); ?>" method="POST" id="sidebarLogout" style="width: 100%;">
            <?php echo csrf_field(); ?>
            <button type="submit" class="sidebar-logout-card">
                <i class="ph ph-sign-out"></i>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</nav>
<?php /**PATH E:\JOB\TITI-WEB STOCK\della-frozenmart\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>