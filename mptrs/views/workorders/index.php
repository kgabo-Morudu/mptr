<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Work Orders</h4>
</div>
<div class="card p-3">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr><th>WO #</th><th>Asset</th><th>Title</th><th>Technician</th><th>Priority</th><th>Status</th><th>Due</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($items as $w): ?>
        <tr>
            <td><small class="fw-bold"><?= htmlspecialchars($w['wo_number']) ?></small></td>
            <td><small><?= htmlspecialchars($w['asset_name']) ?></small></td>
            <td><?= htmlspecialchars($w['title']) ?></td>
            <td><?= htmlspecialchars($w['technician_name'] ?? '—') ?></td>
            <td><span class="badge bg-<?= match($w['priority']) {
                'Low'=>'secondary','Medium'=>'info','High'=>'warning','Critical'=>'danger'
            } ?>"><?= $w['priority'] ?></span></td>
            <td><span class="badge bg-<?= match($w['status']) {
                'Created'=>'primary','Assigned'=>'info','In Progress'=>'warning',
                'Completed'=>'success','Verified'=>'success','Closed'=>'dark','Cancelled'=>'danger'
            } ?>"><?= $w['status'] ?></span></td>
            <td><small><?= $w['due_date'] ? date('d M Y', strtotime($w['due_date'])) : '—' ?></small></td>
            <td><a href="<?= BASE_URL ?>/index.php?route=workorders-view&id=<?= $w['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="8" class="text-center text-muted py-4">No work orders</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
</div>