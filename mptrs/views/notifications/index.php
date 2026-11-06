<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Notifications</h4>
    <form method="POST" action="<?= BASE_URL ?>/index.php?route=notifications-read-all">
        <button class="btn btn-outline-primary btn-sm">Mark All as Read</button>
    </form>
</div>
<div class="card p-0">
    <div class="list-group list-group-flush">
        <?php foreach ($items as $n): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center <?= $n['is_read'] ? 'bg-light' : 'bg-white' ?>">
            <div>
                <h6 class="mb-1 fw-bold">
                    <?php if (!$n['is_read']): ?><span class="badge bg-danger me-2">New</span><?php endif; ?>
                    <?= htmlspecialchars($n['title']) ?>
                </h6>
                <p class="mb-1 text-muted small"><?= htmlspecialchars($n['message']) ?></p>
                <small class="text-secondary"><?= date('d M Y H:i', strtotime($n['created_at'])) ?></small>
            </div>
            <?php if ($n['link']): ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=notifications-read">
                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                <button class="btn btn-sm btn-primary">View</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if (!$items): ?><div class="list-group-item text-center text-muted py-4">No notifications</div><?php endif; ?>
    </div>
</div>