<h4 class="fw-bold mb-3">Reports & Analytics</h4>
<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">Reports & Analytics</h4>
    <div>
        <a href="?route=reports-export-csv&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </a>
        <a href="?route=reports-print-pdf&month=<?= $month ?>&year=<?= $year ?>" target="_blank" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>
</div>
<form method="GET" class="card p-3 mb-3">
    <input type="hidden" name="route" value="reports">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Month</label>
            <select name="month" class="form-select">
                <?php for ($m=1;$m<=12;$m++): ?>
                <option value="<?= $m ?>" <?= $m==$month?'selected':'' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Year</label>
            <input type="number" name="year" class="form-control" value="<?= $year ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Apply</button>
        </div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card p-3"><small>Requests Logged</small><div class="h3"><?= $monthly['requests_logged'] ?></div></div></div>
    <div class="col-md-3"><div class="card p-3"><small>Requests Closed</small><div class="h3 text-success"><?= $monthly['requests_closed'] ?></div></div></div>
    <div class="col-md-3"><div class="card p-3"><small>Work Orders Created</small><div class="h3"><?= $monthly['wo_created'] ?></div></div></div>
    <div class="col-md-3"><div class="card p-3"><small>Total Cost</small><div class="h3 text-danger"><?= number_format($monthly['total_cost'],2) ?></div></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card p-3"><h6 class="fw-bold">Monthly Cost Trend (12 months)</h6>
            <canvas id="chartTrend" height="160"></canvas></div>
    </div>
    <div class="col-md-3">
        <div class="card p-3"><h6 class="fw-bold">Assets by Condition</h6>
            <canvas id="chartCond" height="160"></canvas></div>
    </div>
    <div class="col-md-3">
        <div class="card p-3"><h6 class="fw-bold">Assets by Criticality</h6>
            <canvas id="chartCrit" height="160"></canvas></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-3"><h6 class="fw-bold">Top Faulty Assets</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Asset</th><th>Faults</th></tr></thead>
                <tbody>
                <?php foreach ($faulty as $f): ?>
                    <tr><td><?= htmlspecialchars($f['asset_code']) ?> — <?= htmlspecialchars($f['name']) ?></td><td><strong><?= $f['fault_count'] ?></strong></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3"><h6 class="fw-bold">Highest Cost Assets (Month)</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Asset</th><th>Cost</th></tr></thead>
                <tbody>
                <?php foreach ($costAssets as $c): ?>
                    <tr><td><?= htmlspecialchars($c['asset_code']) ?> — <?= htmlspecialchars($c['name']) ?></td><td><strong><?= number_format($c['total'],2) ?></strong></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3"><h6 class="fw-bold">Cost by Building (Month)</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Building</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($costByBuilding as $b): ?>
                    <tr><td><?= htmlspecialchars($b['name']) ?></td><td><strong><?= number_format($b['total'],2) ?></strong></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3"><h6 class="fw-bold">Cost by Facility (Month)</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Facility</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($costByFacility as $b): ?>
                    <tr><td><?= htmlspecialchars($b['name']) ?></td><td><strong><?= number_format($b['total'],2) ?></strong></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('chartTrend'), {
    type: 'line',
    data: { labels: <?= json_encode(array_column($trend,'m')) ?>,
            datasets: [{ label:'Monthly Cost', data: <?= json_encode(array_map('floatval', array_column($trend,'total'))) ?>,
                borderColor:'#4facfe', backgroundColor:'rgba(79,172,254,.2)', fill:true, tension:.3 }] },
    options:{ plugins:{legend:{display:false}} }
});
new Chart(document.getElementById('chartCond'), {
    type: 'doughnut',
    data: { labels: <?= json_encode(array_column($cond,'condition_status')) ?>,
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($cond,'c'))) ?>,
                backgroundColor:['#43e97b','#4facfe','#fee140','#fa709a','#f5576c'] }] },
    options:{ plugins:{legend:{position:'bottom'}} }
});
new Chart(document.getElementById('chartCrit'), {
    type: 'pie',
    data: { labels: <?= json_encode(array_column($crit,'criticality')) ?>,
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($crit,'c'))) ?>,
                backgroundColor:['#667eea','#4facfe','#fee140','#f5576c'] }] },
    options:{ plugins:{legend:{position:'bottom'}} }
});
</script>