<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Assets</h4>
    <div class="d-flex gap-2">
        <form class="d-flex" method="GET" action="<?= BASE_URL ?>/index.php">
            <input type="hidden" name="route" value="assets">
            <input name="q" class="form-control me-2" placeholder="Search code/serial/name" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
        </form>
        <a href="<?= BASE_URL ?>/index.php?route=assets-form" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Asset</a>
    </div>
</div>
<div class="card p-3">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr><th>Code</th><th>Name</th><th>Category</th><th>Facility/Building</th><th>Condition</th><th>Criticality</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($items as $it): ?>
        <tr>
            <td><strong><?= htmlspecialchars($it['asset_code']) ?></strong></td>
            <td><?= htmlspecialchars($it['name']) ?></td>
            <td><span class="badge bg-secondary"><?= htmlspecialchars($it['category_name']) ?></span></td>
            <td><small><?= htmlspecialchars($it['facility_name']) ?><br><?= htmlspecialchars($it['building_name']) ?></small></td>
            <td><span class="badge bg-<?= match($it['condition_status']) {
                'Excellent'=>'success','Good'=>'primary','Fair'=>'warning','Poor'=>'danger','Out of Service'=>'dark'
            } ?>"><?= $it['condition_status'] ?></span></td>
            <td><span class="badge bg-<?= match($it['criticality']) {
                'Low'=>'secondary','Medium'=>'info','High'=>'warning','Critical'=>'danger'
            } ?>"><?= $it['criticality'] ?></span></td>
            <td><?= $it['status'] ?></td>
            <td>
                <a href="<?= BASE_URL ?>/index.php?route=assets-form&id=<?= $it['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=assets-delete" class="d-inline" onsubmit="return confirm('Delete?')">
                    <input type="hidden" name="id" value="<?= $it['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="8" class="text-center text-muted py-4">No assets</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
</div>