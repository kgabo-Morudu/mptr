<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Maintenance Requests</h4>
    <a href="<?= BASE_URL ?>/index.php?route=requests-create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Request
    </a>
</div>
<div class="card p-3">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr><th>Request #</th><th>Title</th><th>Asset</th><th>Requester</th><th>Priority</th><th>Status</th><th>Photos</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($items as $r): ?>
        <tr>
            <td><small class="fw-bold"><?= htmlspecialchars($r['request_number']) ?></small></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><small><?= htmlspecialchars($r['asset_name']) ?><br><span class="text-muted"><?= htmlspecialchars($r['asset_code']) ?></span></small></td>
            <td><small><?= htmlspecialchars($r['requester_name']) ?></small></td>
            <td><span class="badge bg-<?= match($r['priority']) {
                'Low'=>'secondary','Medium'=>'info','High'=>'warning','Critical'=>'danger'
            } ?>"><?= $r['priority'] ?></span></td>
            <td><span class="badge bg-<?= match($r['status']) {
                'Open'=>'primary','Approved'=>'info','Assigned'=>'warning','In Progress'=>'secondary',
                'Completed'=>'success','Closed'=>'dark','Rejected'=>'danger'
            } ?>"><?= $r['status'] ?></span></td>
            <td><small class="text-muted"><i class="bi bi-paperclip"></i> <?= $r['attach_count'] ?></small></td>
            <td><small><?= date('d M', strtotime($r['created_at'])) ?></small></td>
            <td><a href="<?= BASE_URL ?>/index.php?route=requests-view&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="9" class="text-center text-muted py-4">No requests</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
</div>