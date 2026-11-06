<h4 class="fw-bold mb-3"><?= $item?'Edit':'New' ?> Asset</h4>
<form method="POST" action="<?= BASE_URL ?>/index.php?route=assets-save" class="card p-4">
    <input type="hidden" name="id" value="<?= $item['id'] ?? '' ?>">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Asset Code *</label>
            <input name="asset_code" class="form-control" required value="<?= htmlspecialchars($item['asset_code'] ?? '') ?>">
        </div>
        <div class="col-md-9">
            <label class="form-label">Name *</label>
            <input name="name" class="form-control" required value="<?= htmlspecialchars($item['name'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Category *</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- select --</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($item['category_id']??0)==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Facility *</label>
            <select name="facility_id" class="form-select" required>
                <option value="">-- select --</option>
                <?php foreach ($facilities as $f): ?>
                <option value="<?= $f['id'] ?>" <?= ($item['facility_id']??0)==$f['id']?'selected':'' ?>><?= htmlspecialchars($f['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Building *</label>
            <select name="building_id" class="form-select" required>
                <option value="">-- select --</option>
                <?php foreach ($buildings as $b): ?>
                <option value="<?= $b['id'] ?>" <?= ($item['building_id']??0)==$b['id']?'selected':'' ?>><?= htmlspecialchars($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4"><label class="form-label">Manufacturer</label><input name="manufacturer" class="form-control" value="<?= htmlspecialchars($item['manufacturer'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Model</label><input name="model" class="form-control" value="<?= htmlspecialchars($item['model'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Serial No.</label><input name="serial_number" class="form-control" value="<?= htmlspecialchars($item['serial_number'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Installation Date</label><input type="date" name="installation_date" class="form-control" value="<?= $item['installation_date'] ?? '' ?>"></div>
        <div class="col-md-4"><label class="form-label">Warranty Expiry</label><input type="date" name="warranty_expiry" class="form-control" value="<?= $item['warranty_expiry'] ?? '' ?>"></div>
        <div class="col-md-4">
            <label class="form-label">Condition</label>
            <select name="condition_status" class="form-select">
                <?php foreach (['Excellent','Good','Fair','Poor','Out of Service'] as $c): ?>
                <option <?= ($item['condition_status']??'')===$c?'selected':'' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Criticality</label>
            <select name="criticality" class="form-select">
                <?php foreach (['Low','Medium','High','Critical'] as $c): ?>
                <option <?= ($item['criticality']??'')===$c?'selected':'' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['Active','Inactive','Under Maintenance','Retired'] as $c): ?>
                <option <?= ($item['status']??'')===$c?'selected':'' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($item['notes'] ?? '') ?></textarea></div>
    </div>
    <div class="mt-4"><button class="btn btn-primary">Save</button>
        <a href="<?= BASE_URL ?>/index.php?route=assets" class="btn btn-secondary">Cancel</a></div>
</form>
<input type="hidden" name="csrf_token" value="<?= \Auth::csrfToken() ?>">