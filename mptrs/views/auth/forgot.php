<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg" style="width:420px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Reset Password</h5>
            <?php if ($msg = \Session::flash('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <?php if ($msg = \Session::flash('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=forgot-password">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100">Send Reset Link</button>
                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/index.php?route=login" class="small">Back to login</a>
                </div>
            </form>
        </div>
    </div>
</div>