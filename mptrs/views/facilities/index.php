<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Facilities</h4>
    <a href="<?= BASE_URL ?>/index.php?route=facilities-create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Facility
    </a>
</div>
<div class="card p-3">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr><th>#</th><th>Code</th><th>Name</th><th>Type</th><th>Location</th><th>Contact</th><th>Active</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($items as $it): ?>
        <tr>
            <td><?= $it['id'] ?></td>
            <td><strong><?= htmlspecialchars($it['code']) ?></strong></td>
            <td><?= htmlspecialchars($it['name']) ?></td>
            <td><?= htmlspecialchars($it['type']) ?></td>
            <td><?= htmlspecialchars($it['location']) ?></td>
            <td><?= htmlspecialchars($it['contact_person']) ?> <small class="text-muted"><?= htmlspecialchars($it['phone']) ?></small></td>
            <td><span class="badge bg-<?= $it['is_active']?'success':'secondary' ?>"><?= $it['is_active']?'Yes':'No' ?></span></td>
            <td>
                <a href="<?= BASE_URL ?>/index.php?route=facilities-edit&id=<?= $it['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=facilities-delete" class="d-inline" onsubmit="return confirm('Delete?')">
                    <input type="hidden" name="id" value="<?= $it['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="8" class="text-center text-muted py-4">No facilities</td></tr><?php endif; ?>
    </tbody>
</table>
</div>