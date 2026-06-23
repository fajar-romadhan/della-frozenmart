<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Della Frozen Mart</title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="app-shell sidebar-expanded" id="appShell">
        
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        
        <div class="main-wrapper">
            
            <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <main class="main-content">
            
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="alertSuccess">
                    <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alertError">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert" id="alertWarning">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('warning')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    </div>

    
    <div id="toastContainer" class="toast-container"></div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    
    <script>
        // Mobile Sidebar toggle (hamburger)
        function toggleSidebar() {
            document.getElementById('appSidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }

        // Tooltip init for general elements
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) {
                return new bootstrap.Tooltip(el);
            });
        });

        // CSRF token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
            setTimeout(function() {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });

        // Real-time Notification Short-Polling
        <?php if(auth()->guard()->check()): ?>
            let lastNotifCount = <?php echo e(auth()->user()->notifications()->where('status_baca', false)->count()); ?>;

            function checkNotifications() {
                fetch('<?php echo e(route("notifikasi.count")); ?>')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count;
                        if (count !== lastNotifCount) {
                            // Update badges in sidebar and topbar
                            const sidebarBadge = document.querySelector('.sidebar-notif-badge');
                            const topbarBadge = document.querySelector('.topbar-notif-badge');
                            
                            // Helper to update individual badge
                            const updateBadge = (badge) => {
                                if (badge) {
                                    if (count > 0) {
                                        badge.textContent = count > 9 ? '9+' : count;
                                        badge.style.display = 'inline-flex';
                                    } else {
                                        badge.style.display = 'none';
                                    }
                                }
                            };

                            updateBadge(sidebarBadge);
                            updateBadge(topbarBadge);

                            // If there are new notifications, show a dynamic Toast
                            if (count > lastNotifCount) {
                                showNotificationToast('Ada notifikasi sistem baru.');
                            }

                            lastNotifCount = count;
                        }
                    })
                    .catch(err => console.error('Gagal memuat data notifikasi:', err));
            }

            function showNotificationToast(message) {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = 'custom-toast';
                toast.innerHTML = `
                    <div class="toast-icon">
                        <i class="ph ph-bell"></i>
                    </div>
                    <div class="toast-body">
                        <div class="fw-bold text-primary" style="font-size: 0.85rem;">Notifikasi Baru</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">${message}</div>
                    </div>
                `;
                container.appendChild(toast);

                // Animate in
                setTimeout(() => toast.classList.add('show'), 100);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 400);
                }, 5000);
            }

            // Check notifications every 30 seconds
            setInterval(checkNotifications, 30000);

            // ===== NOTIFICATION DROPDOWN =====
            let notifDropdownLoaded = false;
            let notifDropdownCache = null;
            let notifDropdownCacheTime = 0;
            const NOTIF_CACHE_TTL = 30000; // 30 seconds

            function toggleNotifDropdown(e) {
                e.stopPropagation();
                const dropdown = document.getElementById('notifDropdown');
                const isOpen = dropdown.classList.contains('show');

                if (isOpen) {
                    dropdown.classList.remove('show');
                } else {
                    dropdown.classList.add('show');
                    loadNotifDropdown();
                }
            }

            function loadNotifDropdown() {
                const now = Date.now();
                // Use cache if fresh enough
                if (notifDropdownCache && (now - notifDropdownCacheTime) < NOTIF_CACHE_TTL) {
                    renderNotifDropdown(notifDropdownCache);
                    return;
                }

                // Show loading
                document.getElementById('notifDropdownLoading').style.display = 'block';
                document.getElementById('notifDropdownList').style.display = 'none';
                document.getElementById('notifDropdownEmpty').style.display = 'none';

                fetch('<?php echo e(route("notifikasi.latest-dropdown")); ?>')
                    .then(r => r.json())
                    .then(data => {
                        notifDropdownCache = data;
                        notifDropdownCacheTime = Date.now();
                        renderNotifDropdown(data);
                    })
                    .catch(err => {
                        console.error('Error loading notifications:', err);
                        document.getElementById('notifDropdownLoading').style.display = 'none';
                        document.getElementById('notifDropdownEmpty').style.display = 'flex';
                    });
            }

            function renderNotifDropdown(data) {
                document.getElementById('notifDropdownLoading').style.display = 'none';

                if (!data.items || data.items.length === 0) {
                    document.getElementById('notifDropdownEmpty').style.display = 'flex';
                    document.getElementById('notifDropdownList').style.display = 'none';
                    return;
                }

                const colorMap = {
                    'red': { bg: '#fef2f2', border: '#fecaca', icon: '#dc2626' },
                    'yellow': { bg: '#fffbeb', border: '#fde68a', icon: '#d97706' },
                    'orange': { bg: '#fff7ed', border: '#fed7aa', icon: '#ea580c' },
                    'purple': { bg: '#faf5ff', border: '#e9d5ff', icon: '#7c3aed' },
                };

                let html = '';
                data.items.forEach(item => {
                    const colors = colorMap[item.color] || colorMap['red'];
                    html += `
                        <a href="<?php echo e(route('notifikasi.index')); ?>" class="notif-dropdown-item">
                            <div class="notif-dropdown-icon" style="background: ${colors.bg}; border-color: ${colors.border};">
                                <i class="ph ${item.icon}" style="color: ${colors.icon};"></i>
                            </div>
                            <div class="notif-dropdown-content">
                                <div class="notif-dropdown-item-title">${item.judul}</div>
                                <div class="notif-dropdown-item-msg">${item.pesan}</div>
                            </div>
                            <div class="notif-dropdown-time">${item.waktu}</div>
                        </a>
                    `;
                });

                const listEl = document.getElementById('notifDropdownList');
                listEl.innerHTML = html;
                listEl.style.display = 'block';
                document.getElementById('notifDropdownEmpty').style.display = 'none';
            }

            // Close dropdown on outside click
            document.addEventListener('click', function(e) {
                const wrap = document.getElementById('notifDropdownWrap');
                const dropdown = document.getElementById('notifDropdown');
                if (wrap && dropdown && !wrap.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        <?php endif; ?>
    </script>

    
    <?php echo $__env->yieldPushContent('modals'); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH D:\ANTIGRAVITY\della-frozenmart\resources\views/layouts/app.blade.php ENDPATH**/ ?>