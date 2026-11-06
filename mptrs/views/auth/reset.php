<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg" style="width:420px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Set New Password</h5>
            <?php if ($msg = \Session::flash('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=reset-password">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                <div class="mb-3">
                    <label class="form-label">New Password (min 8 chars)</label>
                    <input type="password" name="password" class="form-control" minlength="8" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-control" minlength="8" required>
                </div>
                <button class="btn btn-primary w-100">Update Password</button>
            </form>
        </div>
    </div>
</div>