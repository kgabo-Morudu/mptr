<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Buildings</h4>
    <a href="<?= BASE_URL ?>/index.php?route=buildings-form" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Building</a>
</div>
<div class="card p-3">
<table class="table table-hover mb-0">
    <thead class="table-light"><tr><th>#</th><th>Facility</th><th>Code</th><th>Name</th><th>Type</th><th>Floors</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
        <tr>
            <td><?= $it['id'] ?></td>
            <td><?= htmlspecialchars($it['facility_name']) ?></td>
            <td><strong><?= htmlspecialchars($it['code']) ?></strong></td>
            <td><?= htmlspecialchars($it['name']) ?></td>
            <td><?= htmlspecialchars($it['type']) ?></td>
            <td><?= $it['floors'] ?></td>
            <td>
                <a href="<?= BASE_URL ?>/index.php?route=buildings-form&id=<?= $it['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=buildings-delete" class="d-inline" onsubmit="return confirm('Delete?')">
                    <input type="hidden" name="id" value="<?= $it['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="7" class="text-center text-muted py-4">No buildings</td></tr><?php endif; ?>
    </tbody>
</table>
</div>