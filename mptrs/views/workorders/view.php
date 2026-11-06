<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Work Order <?= htmlspecialchars($wo['wo_number']) ?></h4>
    <a href="<?= BASE_URL ?>/index.php?route=workorders" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="row g-3">
    <div class="col-md-8">
        <div class="card p-4">
            <h5><?= htmlspecialchars($wo['title']) ?></h5>
            <p class="text-muted"><?= nl2br(htmlspecialchars($wo['description'] ?? '')) ?></p>
            <dl class="row mb-0">
                <dt class="col-sm-3">Asset</dt><dd class="col-sm-9"><?= htmlspecialchars($wo['asset_name']) ?> (<?= htmlspecialchars($wo['asset_code']) ?>)</dd>
                <dt class="col-sm-3">Technician</dt><dd class="col-sm-9"><?= htmlspecialchars($wo['technician_name'] ?? '—') ?></dd>
                <dt class="col-sm-3">Supervisor</dt><dd class="col-sm-9"><?= htmlspecialchars($wo['supervisor_name'] ?? '—') ?></dd>
                <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge bg-primary fs-6"><?= $wo['status'] ?></span></dd>
                <dt class="col-sm-3">Priority</dt><dd class="col-sm-9"><?= $wo['priority'] ?></dd>
                <dt class="col-sm-3">Due</dt><dd class="col-sm-9"><?= $wo['due_date'] ? date('d M Y H:i', strtotime($wo['due_date'])) : '—' ?></dd>
                <dt class="col-sm-3">Hours Logged</dt><dd class="col-sm-9"><?= number_format($wo['labour_hours'],2) ?> h</dd>
                <dt class="col-sm-3">Total Cost</dt><dd class="col-sm-9"><strong><?= number_format($totals['total'] ?? 0, 2) ?></strong></dd>
            </dl>
        </div>

        <div class="card p-4 mt-3">
            <h6 class="fw-bold">Update Progress</h6>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=workorders-status" class="row g-2">
                <input type="hidden" name="id" value="<?= $wo['id'] ?>">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <?php foreach (['Assigned','In Progress','Completed','Verified','Closed','Cancelled'] as $s): ?>
                        <option <?= $wo['status']===$s?'selected':'' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.25" min="0" name="hours" class="form-control" placeholder="Hours" value="0">
                </div>
                <div class="col-md-4">
                    <input name="notes" class="form-control" placeholder="Notes">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Update</button>
                </div>
            </form>
        </div>

        <div class="card p-4 mt-3">
            <h6 class="fw-bold">Progress Log</h6>
            <?php if (!$progress): ?>
                <p class="text-muted mb-0">No updates yet.</p>
            <?php else: ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($progress as $p): ?>
                <li class="list-group-item px-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong><?= htmlspecialchars($p['full_name']) ?></strong>
                            <?php if ($p['status_from'] && $p['status_to']): ?>
                                changed <span class="badge bg-secondary"><?= $p['status_from'] ?></span> → <span class="badge bg-primary"><?= $p['status_to'] ?></span>
                            <?php endif; ?>
                            <?php if ($p['hours_logged'] > 0): ?>
                                <span class="badge bg-info">+<?= $p['hours_logged'] ?>h</span>
                            <?php endif; ?>
                            <?php if ($p['notes']): ?>
                                <div class="small text-muted mt-1"><?= nl2br(htmlspecialchars($p['notes'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?= date('d M H:i', strtotime($p['created_at'])) ?></small>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <div class="card p-4 mt-3">
            <h6 class="fw-bold">Photos & Documents</h6>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=workorders-upload" enctype="multipart/form-data" class="mb-3">
                <input type="hidden" name="id" value="<?= $wo['id'] ?>">
                <div class="input-group">
                    <input type="file" name="photo" class="form-control" required accept="image/*,application/pdf">
                    <button class="btn btn-primary"><i class="bi bi-upload"></i> Upload</button>
                </div>
            </form>
            <div class="row g-2">
            <?php foreach ($attachments as $a): $isImg = preg_match('/\.(jpg|jpeg|png|gif)$/i', $a['file_path']); ?>
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>/../<?= htmlspecialchars($a['file_path']) ?>" target="_blank" class="d-block border rounded p-2 text-decoration-none">
                        <?php if ($isImg): ?>
                            <img src="<?= BASE_URL ?>/../<?= htmlspecialchars($a['file_path']) ?>" class="img-fluid rounded" style="height:80px;object-fit:cover;width:100%;">
                        <?php else: ?><i class="bi bi-file-earmark-text fs-2"></i><?php endif; ?>
                        <small class="d-block text-truncate"><?= htmlspecialchars($a['file_name']) ?></small>
                    </a>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php if (in_array(Auth::role(), ['admin','manager','supervisor']) && $wo['status']==='Created'): ?>
        <div class="card p-4 mb-3">
            <h6 class="fw-bold">Assign Technician</h6>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=workorders-assign">
                <input type="hidden" name="id" value="<?= $wo['id'] ?>">
                <select name="technician_id" class="form-select mb-2" required>
                    <option value="">-- select --</option>
                    <?php foreach ($technicians as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="due_date" class="form-control mb-2">
                <button class="btn btn-primary w-100">Assign</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="card p-4">
            <h6 class="fw-bold">Costs</h6>
            <?php if (in_array(Auth::role(), ['admin','manager','supervisor'])): ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?route=costs-store" class="mb-3">
                <input type="hidden" name="work_order_id" value="<?= $wo['id'] ?>">
                <select name="cost_type" class="form-select mb-1" required>
                    <option>Labour</option><option>Material</option><option>Contractor</option>
                    <option>Transport</option><option>Other</option>
                </select>
                <input name="description" class="form-control mb-1" placeholder="Description">
                <input name="amount" type="number" step="0.01" class="form-control mb-1" placeholder="Amount" required>
                <input name="incurred_date" type="date" class="form-control mb-1" value="<?= date('Y-m-d') ?>" required>
                <button class="btn btn-primary w-100 btn-sm">Add Cost</button>
            </form>
            <?php endif; ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($costs as $c): ?>
                <li class="list-group-item px-0 d-flex justify-content-between">
                    <div>
                        <span class="badge bg-secondary"><?= $c['cost_type'] ?></span>
                        <?= htmlspecialchars($c['description']) ?>
                        <div><small class="text-muted"><?= $c['incurred_date'] ?></small></div>
                    </div>
                    <strong><?= number_format($c['amount'],2) ?></strong>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>