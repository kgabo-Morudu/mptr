<h4 class="fw-bold mb-3">New Maintenance Request</h4>
<form method="POST" action="<?= BASE_URL ?>/index.php?route=requests-store" 
      class="card p-4" enctype="multipart/form-data">
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Asset *</label>
            <select name="asset_id" class="form-select" required>
                <option value="">-- select asset --</option>
                <?php foreach ($assets as $a): ?>
                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['asset_code']) ?> — <?= htmlspecialchars($a['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
                <option>Low</option><option selected>Medium</option><option>High</option><option>Critical</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Title *</label>
            <input name="title" class="form-control" required maxlength="200">
        </div>
        <div class="col-12">
            <label class="form-label">Fault Description *</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="col-12">
            <label class="form-label">Photos</label>
            <input type="file" name="photos[]" class="form-control" multiple accept="image/*,application/pdf">
            <small class="text-muted">You can select multiple photos (max 10MB each)</small>
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-primary"><i class="bi bi-send"></i> Submit Request</button>
        <a href="<?= BASE_URL ?>/index.php?route=requests" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<input type="hidden" name="csrf_token" value="<?= \Auth::csrfToken() ?>">