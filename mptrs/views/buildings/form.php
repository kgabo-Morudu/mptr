<h4 class="fw-bold mb-3"><?= $item?'Edit':'New' ?> Building</h4>
<form method="POST" action="<?= BASE_URL ?>/index.php?route=buildings-save" class="card p-4">
    <input type="hidden" name="id" value="<?= $item['id'] ?? '' ?>">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Facility *</label>
            <select name="facility_id" class="form-select" required>
                <option value="">-- select --</option>
                <?php foreach ($facilities as $f): ?>
                <option value="<?= $f['id'] ?>" <?= ($item['facility_id']??0)==$f['id']?'selected':'' ?>><?= htmlspecialchars($f['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Code *</label>
            <input name="code" class="form-control" required value="<?= htmlspecialchars($item['code'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <?php foreach (['Ward','Plant Room','Boiler House','Administration Block','Theatre','Laboratory','Other'] as $t): ?>
                <option <?= ($item['type']??'')===$t?'selected':'' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Name *</label>
            <input name="name" class="form-control" required value="<?= htmlspecialchars($item['name'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Floors</label>
            <input type="number" name="floors" class="form-control" value="<?= $item['floors'] ?? 1 ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Active</label>
            <select name="is_active" class="form-select">
                <option value="1" <?= ($item['is_active']??1)?'selected':'' ?>>Yes</option>
                <option value="0" <?= isset($item['is_active'])&&!$item['is_active']?'selected':'' ?>>No</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="mt-4"><button class="btn btn-primary">Save</button>
        <a href="<?= BASE_URL ?>/index.php?route=buildings" class="btn btn-secondary">Cancel</a></div>
</form>