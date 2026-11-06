<h4 class="fw-bold mb-3">Technician Performance</h4>
<div class="card p-3">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr><th>Name</th><th>Contact</th><th>Assigned</th><th>Completed</th><th>Outstanding</th><th>Hours</th><th>Completion %</th></tr>
    </thead>
    <tbody>
    <?php foreach ($items as $t): ?>
        <tr>
            <td><strong><?= htmlspecialchars($t['full_name']) ?></strong></td>
            <td><small><?= htmlspecialchars($t['email']) ?><br><?= htmlspecialchars($t['phone']) ?></small></td>
            <td><?= $t['total_jobs'] ?></td>
            <td class="text-success"><?= $t['completed_jobs'] ?></td>
            <td class="text-warning"><?= $t['outstanding_jobs'] ?></td>
            <td><?= number_format($t['total_hours'],1) ?>h</td>
            <td>
                <div class="progress" style="height:18px;">
                    <div class="progress-bar bg-success" style="width:<?= $t['completion_rate'] ?>%">
                        <?= $t['completion_rate'] ?>%
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="7" class="text-center text-muted py-4">No technicians</td></tr><?php endif; ?>
    </tbody>
</table>
</div>