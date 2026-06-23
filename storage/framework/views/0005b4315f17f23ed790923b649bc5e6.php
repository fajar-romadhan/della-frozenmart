
<header class="topbar" id="mainNavbar">
    <div class="d-flex align-items-center gap-3">
        
        <button class="topbar-hamburger d-lg-none" id="btnHamburger" onclick="toggleSidebar()" aria-label="Toggle sidebar">
            <i class="ph ph-list"></i>
        </button>

        
        <span class="topbar-brand">Della Frozen Mart</span>

        
        <nav class="topbar-nav d-none d-md-flex">
            <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">Dashboard</a>
        </nav>
    </div>

    <div class="topbar-right">
        
        <div class="topbar-search-wrap d-none d-lg-flex">
            <i class="ph ph-magnifying-glass"></i>
            <input type="text" class="topbar-search" placeholder="Cari menu atau produk..." readonly>
        </div>

        
        <div class="notif-dropdown-wrap" id="notifDropdownWrap">
            <button type="button" class="topbar-notif" id="navNotifBell" title="Notifikasi" onclick="toggleNotifDropdown(event)">
                <i class="ph ph-bell"></i>
                <?php
                    $navUnreadCount = auth()->user()->notifications()->where('status_baca', false)->count();
                ?>
                <?php if($navUnreadCount > 0): ?>
                    <span class="topbar-notif-badge"><?php echo e($navUnreadCount > 9 ? '9+' : $navUnreadCount); ?></span>
                <?php endif; ?>
            </button>

            
            <div class="notif-dropdown" id="notifDropdown">
                
                <div class="notif-dropdown-header">
                    <h6 class="notif-dropdown-title">Notifikasi Stok</h6>
                    <a href="<?php echo e(route('notifikasi.read-all')); ?>" class="notif-dropdown-mark-read"
                       onclick="event.preventDefault(); document.getElementById('formMarkAllRead').submit();">
                        Tandai semua sebagai dibaca
                    </a>
                    <form id="formMarkAllRead" action="<?php echo e(route('notifikasi.read-all')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form>
                </div>

                
                <div class="notif-dropdown-body" id="notifDropdownBody">
                    
                    <div class="notif-dropdown-loading" id="notifDropdownLoading">
                        <div class="notif-skeleton-item">
                            <div class="notif-skeleton-icon"></div>
                            <div class="notif-skeleton-text">
                                <div class="notif-skeleton-line w-40"></div>
                                <div class="notif-skeleton-line w-70"></div>
                            </div>
                        </div>
                        <div class="notif-skeleton-item">
                            <div class="notif-skeleton-icon"></div>
                            <div class="notif-skeleton-text">
                                <div class="notif-skeleton-line w-50"></div>
                                <div class="notif-skeleton-line w-60"></div>
                            </div>
                        </div>
                        <div class="notif-skeleton-item">
                            <div class="notif-skeleton-icon"></div>
                            <div class="notif-skeleton-text">
                                <div class="notif-skeleton-line w-35"></div>
                                <div class="notif-skeleton-line w-80"></div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="notif-dropdown-list" id="notifDropdownList" style="display: none;"></div>

                    
                    <div class="notif-dropdown-empty" id="notifDropdownEmpty" style="display: none;">
                        <i class="ph ph-check-circle"></i>
                        <p>Tidak ada notifikasi stok</p>
                    </div>
                </div>

                
                <div class="notif-dropdown-footer">
                    <a href="<?php echo e(route('notifikasi.index')); ?>">
                        Lihat semua notifikasi
                    </a>
                </div>
            </div>
        </div>

        
        <div class="dropdown">
            <div class="topbar-user" id="navUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="topbar-avatar">
                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                </div>
                <div class="topbar-user-info d-none d-sm-block">
                    <div class="topbar-user-name"><?php echo e(auth()->user()->name ?? 'User'); ?></div>
                    <div class="topbar-user-role"><?php echo e(ucfirst(auth()->user()->role ?? 'admin')); ?></div>
                </div>
                <i class="ph ph-caret-down" style="font-size: 12px; color: var(--text-muted);"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navUserDropdown">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-bold" style="font-size: 0.88rem;"><?php echo e(auth()->user()->name ?? 'User'); ?></div>
                    <div class="text-muted" style="font-size: 0.75rem;"><?php echo e(auth()->user()->email ?? ''); ?></div>
                    <span class="badge mt-1
                        <?php if(auth()->user()->role === 'admin'): ?> badge-role-admin
                        <?php elseif(auth()->user()->role === 'manager'): ?> badge-role-manager
                        <?php else: ?> badge-role-owner
                        <?php endif; ?>" style="font-size: 0.68rem;">
                        <?php echo e(ucfirst(auth()->user()->role ?? 'admin')); ?>

                    </span>
                </li>
                <li>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" id="formLogout">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item py-2" id="btnLogout">
                            <i class="ph ph-sign-out me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
<?php /**PATH E:\JOB\TITI-WEB STOCK\della-frozenmart\resources\views/layouts/navbar.blade.php ENDPATH**/ ?>