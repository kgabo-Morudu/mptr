<nav class="navbar navbar-dark bg-primary px-3 shadow-sm">
    <span class="navbar-brand fw-bold">
        <i class="bi bi-hospital"></i> MPTRS
    </span>
        <div class="d-flex align-items-center text-white gap-3">
        <!-- NOTIFICATIONS BELL -->
        <a href="<?= BASE_URL ?>/index.php?route=notifications" class="text-white position-relative text-decoration-none me-2">
            <i class="bi bi-bell fs-5"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">
                3 <!-- In production, query this dynamically -->
            </span>
        </a>
        
        <span class="d-none d-md-inline">
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars(Auth::user()['full_name']) ?>
            <span class="badge bg-light text-primary ms-1"><?= ucfirst(Auth::role()) ?></span>
        </span>
        <!-- ... Logout button ... -->
</nav>