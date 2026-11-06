<div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold">User Management</h4>
    <a href="<?= BASE_URL ?>/index.php?route=users-form" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New User</a>
</div>
<div class="card p-3">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Department</th><th>Active</th><th>Last Login</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($items as $u): ?>
        <tr>
            <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><small><?= htmlspecialchars($u['email']) ?></small></td>
            <td><span class="badge bg-primary"><?= ucfirst($u['role_name']) ?></span></td>
            <td><?= htmlspecialchars($u['department']) ?></td>
            <td><span class="badge bg-<?= $u['is_active']?'success':'secondary' ?>"><?= $u['is_active']?'Yes':'No' ?></span></td>
            <td><small><?= $u['last_login'] ? date('d M Y H:i', strtotime($u['last_login'])) : '—' ?></small></td>
            <td>
                <a href="<?= BASE_URL ?>/index.php?route=users-form&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="<?= BASE_URL ?>/index.php?route=users-delete" class="d-inline" onsubmit="return confirm('Delete user?')">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>