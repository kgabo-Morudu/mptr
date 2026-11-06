<h4 class="fw-bold mb-3"><?= $item ? 'Edit' : 'New' ?> Facility</h4>
<form method="POST" action="<?= BASE_URL ?>/index.php?route=facilities-save" class="card p-4">
    <input type="hidden" name="id" value="<?= $item['id'] ?? '' ?>">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Code *</label>
            <input name="code" class="form-control" required value="<?= htmlspecialchars($item['code'] ?? '') ?>">
        </div>
        <div class="col-md-8">
            <label class="form-label">Name *</label>
            <input name="name" class="form-control" required value="<?= htmlspecialchars($item['name'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <?php foreach (['Hospital','Clinic','Office','Maintenance Facility'] as $t): ?>
                <option <?= ($item['type'] ?? '')===$t?'selected':'' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label">Location</label>
            <input name="location" class="form-control" value="<?= htmlspecialchars($item['location'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Contact Person</label>
            <input name="contact_person" class="form-control" value="<?= htmlspecialchars($item['contact_person'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" value="<?= htmlspecialchars($item['phone'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Active</label>
            <select name="is_active" class="form-select">
                <option value="1" <?= ($item['is_active']??1)?'selected':'' ?>>Yes</option>
                <option value="0" <?= isset($item['is_active'])&&!$item['is_active']?'selected':'' ?>>No</option>
            </select>
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-primary">Save</button>
        <a href="<?= BASE_URL ?>/index.php?route=facilities" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<input type="hidden" name="csrf_token" value="<?= \Auth::csrfToken() ?>">