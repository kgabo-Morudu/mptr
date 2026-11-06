<h4 class="fw-bold mb-3"><?= $item?'Edit':'New' ?> User</h4>
<form method="POST" action="<?= BASE_URL ?>/index.php?route=users-save" class="card p-4">
    <input type="hidden" name="id" value="<?= $item['id'] ?? '' ?>">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Full Name *</label>
            <input name="full_name" class="form-control" required value="<?= htmlspecialchars($item['full_name'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($item['email'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Username *</label>
            <input name="username" class="form-control" required value="<?= htmlspecialchars($item['username'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Password <?= $item?'(leave blank to keep)':'*' ?></label>
            <input type="password" name="password" class="form-control" <?= $item?'':'required minlength="8"' ?>></div>
        <div class="col-md-4"><label class="form-label">Phone</label>
            <input name="phone" class="form-control" value="<?= htmlspecialchars($item['phone'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Role *</label>
            <select name="role_id" class="form-select" required>
                <option value="">-- select --</option>
                <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>" <?= ($item['role_id']??0)==$r['id']?'selected':'' ?>><?= ucfirst($r['name']) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div class="col-md-4"><label class="form-label">Department</label>
            <input name="department" class="form-control" value="<?= htmlspecialchars($item['department'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Active</label>
            <select name="is_active" class="form-select">
                <option value="1" <?= ($item['is_active']??1)?'selected':'' ?>>Yes</option>
                <option value="0" <?= isset($item['is_active'])&&!$item['is_active']?'selected':'' ?>>No</option>
            </select></div>
    </div>
    <div class="mt-4"><button class="btn btn-primary">Save</button>
        <a href="<?= BASE_URL ?>/index.php?route=users" class="btn btn-secondary">Cancel</a></div>
</form>