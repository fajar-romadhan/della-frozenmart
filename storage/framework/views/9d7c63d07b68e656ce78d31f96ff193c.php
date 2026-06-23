<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Della Frozen Mart</title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-icon">
                <i class="ph ph-snowflake"></i>
            </div>
            <h4 class="fw-bold mb-1">Della Frozen Mart</h4>
            <p class="text-secondary" style="font-size: 0.82rem; font-family: var(--font-body);">Sistem Informasi Persediaan & Penjualan</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger py-2 px-3 mb-4" style="font-size: 0.78rem;">
                <i class="ph ph-warning-circle fs-5" style="flex-shrink: 0;"></i>
                <div><?php echo e($errors->first()); ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.8rem; font-family: var(--font-display);">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-secondary" style="border-radius: var(--radius-sm) 0 0 var(--radius-sm); border-color: var(--border-color);">
                        <i class="ph ph-envelope-simple" style="font-size: 1.1rem;"></i>
                    </span>
                    <input type="email" name="email" class="form-control border-start-0" required style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0;" placeholder="nama@email.com">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary" style="font-size: 0.8rem; font-family: var(--font-display);">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-secondary" style="border-radius: var(--radius-sm) 0 0 var(--radius-sm); border-color: var(--border-color);">
                        <i class="ph ph-lock" style="font-size: 1.1rem;"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-start-0" value="password" required style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0;" placeholder="••••••••">
                </div>
            </div>

            <button class="btn btn-primary w-100 py-2 mt-2" style="font-size: 0.88rem;">
                <i class="ph ph-sign-in"></i> Masuk ke Sistem
            </button>
        </form>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH D:\ANTIGRAVITY\della-frozenmart\resources\views/auth/login.blade.php ENDPATH**/ ?>