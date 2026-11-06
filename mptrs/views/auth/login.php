<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg" style="width:420px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-hospital text-primary" style="font-size:2.5rem;"></i>
                <h4 class="mt-2 fw-bold">MPTRS</h4>
                <p class="text-muted small">Maintenance Planning & Tracking System</p>
            </div>
            <?php if ($error = \Session::flash('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success = \Session::flash('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=login">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100">Sign In</button>
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/index.php?route=forgot-password" class="small">
                        Forgot password?
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>