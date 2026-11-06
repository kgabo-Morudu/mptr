<h4 class="mb-4 fw-bold">Dashboard</h4>
<div class="row g-3 mb-4">
<?php
$cards = [
    ['Open Requests', $kpi['open_requests'], 'bg-kpi-1', 'envelope-paper'],
    ['Active Work Orders', $kpi['active_wo'], 'bg-kpi-2', 'clipboard-check'],
    ['Overdue Work Orders', $kpi['overdue_wo'], 'bg-kpi-3', 'exclamation-triangle'],
    ['Completed Work Orders', $kpi['completed_wo'], 'bg-kpi-4', 'check-circle'],
    ['Total Assets', $kpi['total_assets'], 'bg-kpi-5', 'tools'],
    ['Critical Assets', $kpi['critical_assets'], 'bg-kpi-6', 'exclamation-octagon'],
    ['Total Cost', number_format($kpi['total_cost'], 2), 'bg-kpi-7', 'cash-stack'],
];
if (isset($kpi['my_assigned'])) $cards[] = ['My Assigned Jobs', $kpi['my_assigned'], 'bg-kpi-2', 'person-badge'];
if (isset($kpi['my_active']))   $cards[] = ['My Active Jobs',   $kpi['my_active'],   'bg-kpi-1', 'hourglass-split'];
if (isset($kpi['my_requests'])) $cards[] = ['My Requests',      $kpi['my_requests'], 'bg-kpi-4', 'file-earmark-text'];

foreach ($cards as [$label,$val,$bg,$icon]): ?>
    <div class="col-6 col-md-3">
        <div class="card kpi-card <?= $bg ?> p-3 shadow-sm h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="label"><?= $label ?></div>
                    <div class="value"><?= $val ?></div>
                </div>
                <i class="bi bi-<?= $icon ?> fs-2 opacity-50"></i>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 h-100"><h6 class="fw-bold">Requests by Status</h6>
            <canvas id="chartReqStatus" height="180"></canvas></div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100"><h6 class="fw-bold">Work Orders by Status</h6>
            <canvas id="chartWoStatus" height="180"></canvas></div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100"><h6 class="fw-bold">Costs by Category</h6>
            <canvas id="chartCostType" height="180"></canvas></div>
    </div>
</div>

<div class="card p-3">
    <h6 class="fw-bold">Recent Maintenance Requests</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Title</th><th>Asset</th><th>Requester</th><th>Priority</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php foreach ($recentRequests as $r): ?>
                <tr>
                    <td><small><?= htmlspecialchars($r['request_number']) ?></small></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['asset_name']) ?></td>
                    <td><?= htmlspecialchars($r['requester']) ?></td>
                    <td><span class="badge bg-<?= match($r['priority']) {
                        'Low'=>'secondary','Medium'=>'info','High'=>'warning','Critical'=>'danger'
                    } ?>"><?= $r['priority'] ?></span></td>
                    <td><span class="badge bg-primary"><?= $r['status'] ?></span></td>
                    <td><small><?= date('d M Y', strtotime($r['created_at'])) ?></small></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recentRequests): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">No requests yet</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const reqStatus = <?= json_encode($reqByStatus) ?>;
const woStatus  = <?= json_encode($woByStatus) ?>;
const costType  = <?= json_encode($costByType) ?>;

new Chart(document.getElementById('chartReqStatus'), {
    type: 'doughnut',
    data: { labels: reqStatus.map(x=>x.status),
            datasets:[{ data: reqStatus.map(x=>x.c), backgroundColor:['#667eea','#f5576c','#43e97b','#fa709a','#4facfe','#f093fb','#30cfd0'] }] },
    options:{ plugins:{legend:{position:'bottom'}} }
});
new Chart(document.getElementById('chartWoStatus'), {
    type: 'bar',
    data: { labels: woStatus.map(x=>x.status),
            datasets:[{ label:'Count', data: woStatus.map(x=>x.c), backgroundColor:'#667eea' }] },
    options:{ plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});
new Chart(document.getElementById('chartCostType'), {
    type: 'pie',
    data: { labels: costType.map(x=>x.cost_type),
            datasets:[{ data: costType.map(x=>x.t), backgroundColor:['#4facfe','#43e97b','#fa709a','#fee140','#30cfd0'] }] },
    options:{ plugins:{legend:{position:'bottom'}} }
});
</script>