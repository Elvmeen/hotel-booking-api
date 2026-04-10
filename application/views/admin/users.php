<div class="page-header">
    <h1>Users</h1>
</div>

<?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="GET" action="<?= site_url('admin/users') ?>" class="search-bar">
    <input type="text" name="search" placeholder="Search users…" value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-outline">Search</button>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="8" class="text-center text-muted">No users found</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td><?= ((int)$page - 1) * 20 + $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                        <td><span class="badge badge-<?= $u['role'] === 'admin' ? 'purple' : 'secondary' ?>"><?= ucfirst($u['role']) ?></span></td>
                        <td><span class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($u['status']) ?></span></td>
                        <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                        <td class="actions">
                            <?php if ($u['status'] === 'active'): ?>
                                <button class="btn btn-sm btn-danger" onclick="toggleUser(<?= $u['id'] ?>, 'suspended')">Suspend</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-primary" onclick="toggleUser(<?= $u['id'] ?>, 'active')">Activate</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pages > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
        <a href="<?= site_url('admin/users?page=' . $p . '&search=' . urlencode($search)) ?>"
           class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<script>
async function toggleUser(id, newStatus) {
    if (!confirm('Set user status to "' + newStatus + '"?')) return;
    const token = localStorage.getItem('admin_token');
    const res = await fetch('https://hotel-booking-api-1-zmcs.onrender.com/api/users/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify({ status: newStatus }),
    });
    if (res.ok) location.reload();
    else {
        const data = await res.json();
        alert(data.message || 'Failed to update user');
    }
}
</script>
