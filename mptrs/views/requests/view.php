<?php $user = Auth::user(); ?>
<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Request <?= htmlspecialchars($req['request_number']) ?></h4>
    <a href="<?= BASE_URL ?>/index.php?route=requests" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="row g-3">
    <div class="col-md-8">
        <div class="card p-4">
            <h5><?= htmlspecialchars($req['title']) ?></h5>
            <p class="text-muted"><?= nl2br(htmlspecialchars($req['description'])) ?></p>
            <hr>
            <dl class="row mb-0">
                <dt class="col-sm-3">Asset</dt><dd class="col-sm-9"><?= htmlspecialchars($req['asset_name']) ?> (<?= htmlspecialchars($req['asset_code']) ?>)</dd>
                <dt class="col-sm-3">Criticality</dt><dd class="col-sm-9"><?= $req['criticality'] ?></dd>
                <dt class="col-sm-3">Requester</dt><dd class="col-sm-9"><?= htmlspecialchars($req['requester_name']) ?></dd>
                <dt class="col-sm-3">Priority</dt><dd class="col-sm-9"><span class="badge bg-warning"><?= $req['priority'] ?></span></dd>
                <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge bg-primary"><?= $req['status'] ?></span></dd>
                <dt class="col-sm-3">Submitted</dt><dd class="col-sm-9"><?= date('d M Y H:i', strtotime($req['created_at'])) ?></dd>
                <?php if ($req['approved_at']): ?>
                <dt class="col-sm-3">Approved</dt><dd class="col-sm-9"><?= date('d M Y H:i', strtotime($req['approved_at'])) ?> by <?= htmlspecialchars($req['approver_name'] ?? '') ?></dd>
                <?php endif; ?>
                <?php if ($req['rejection_reason']): ?>
                <dt class="col-sm-3">Rejection Reason</dt><dd class="col-sm-9 text-danger"><?= htmlspecialchars($req['rejection_reason']) ?></dd>
                <?php endif; ?>
            </dl>
        </div>

        <?php if ($attachments): ?>
        <div class="card p-4 mt-3">
            <h6 class="fw-bold">Attachments</h6>
            <div class="row g-2">
            <?php foreach ($attachments as $a): 
                $isImg = preg_match('/\.(jpg|jpeg|png|gif)$/i', $a['file_path']); ?>
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>/../<?= htmlspecialchars($a['file_path']) ?>" target="_blank" class="d-block border rounded p-2 text-decoration-none">
                        <?php if ($isImg): ?>
                            <img src="<?= BASE_URL ?>/../<?= htmlspecialchars($a['file_path']) ?>" class="img-fluid rounded" style="height:80px;object-fit:cover;width:100%;">
                        <?php else: ?>
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        <?php endif; ?>
                        <small class="d-block text-truncate"><?= htmlspecialchars($a['file_name']) ?></small>
                    </a>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($workOrders): ?>
        <div class="card p-4 mt-3">
            <h6 class="fw-bold">Linked Work Orders</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>WO #</th><th>Technician</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($workOrders as $wo): ?>
                    <tr>
                        <td><?= htmlspecialchars($wo['wo_number']) ?></td>
                        <td><?= htmlspecialchars($wo['technician_name'] ?? '—') ?></td>
                        <td><span class="badge bg-secondary"><?= $wo['status'] ?></span></td>
                        <td><a href="<?= BASE_URL ?>/index.php?route=workorders-view&id=<?= $wo['id'] ?>" class="btn btn-sm btn-outline-primary">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="card p-4">
            <h6 class="fw-bold">Actions</h6>
            <?php if ($req['status']==='Open' && in_array(Auth::role(), ['admin','manager'])): ?>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=requests-approve" class="mb-2">
                    <input type="hidden" name="id" value="<?= $req['id'] ?>">
                    <button class="btn btn-success w-100"><i class="bi bi-check-lg"></i> Approve Request</button>
                </form>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=requests-reject">
                    <input type="hidden" name="id" value="<?= $req['id'] ?>">
                    <textarea name="reason" class="form-control mb-2" placeholder="Reason (optional)" rows="2"></textarea>
                    <button class="btn btn-outline-danger w-100"><i class="bi bi-x-lg"></i> Reject</button>
                </form>
            <?php endif; ?>

            <?php if ($req['status']==='Approved' && in_array(Auth::role(), ['admin','manager','supervisor'])): ?>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=requests-create-wo" class="mt-2">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <button class="btn btn-primary w-100"><i class="bi bi-clipboard-plus"></i> Create Work Order</button>
                </form>
            <?php endif; ?>

            <?php if (in_array($req['status'], ['Assigned','Completed']) && !$workOrders && in_array(Auth::role(),['admin','manager','supervisor'])): ?>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=requests-create-wo" class="mt-2">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <button class="btn btn-primary w-100">Create Work Order</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>