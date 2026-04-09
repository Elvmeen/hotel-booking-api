<div class="page-header">
    <h1>Bookings</h1>
</div>

<?php if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- Filters -->
<form method="GET" action="<?= site_url('admin/bookings') ?>" class="search-bar">
    <input type="text" name="search" placeholder="Search reference, guest…" value="<?= htmlspecialchars($search) ?>">
    <select name="status">
        <option value="">All statuses</option>
        <?php foreach (['pending','confirmed','cancelled','completed'] as $s): ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline">Filter</button>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Nights</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="9" class="text-center text-muted">No bookings found</td></tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($b['booking_reference']) ?></code></td>
                        <td>
                            <strong><?= htmlspecialchars($b['guest_name'] ?? '—') ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($b['guest_email'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($b['room_number'] ?? '—') ?> <small>(<?= htmlspecialchars($b['room_type'] ?? '') ?>)</small></td>
                        <td><?= htmlspecialchars($b['check_in']) ?></td>
                        <td><?= htmlspecialchars($b['check_out']) ?></td>
                        <td><?= $b['nights'] ?></td>
                        <td>$<?= number_format($b['total_price'], 2) ?></td>
                        <td>
                            <span class="badge badge-<?= match($b['status']) {
                                'confirmed' => 'success',
                                'pending'   => 'warning',
                                'cancelled' => 'danger',
                                default     => 'secondary',
                            } ?>"><?= ucfirst($b['status']) ?></span>
                        </td>
                        <td class="actions">
                            <?php if ($b['status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-primary" onclick="updateStatus(<?= $b['id'] ?>, 'confirmed')">Confirm</button>
                                <button class="btn btn-sm btn-danger" onclick="updateStatus(<?= $b['id'] ?>, 'cancelled')">Cancel</button>
                            <?php elseif ($b['status'] === 'confirmed'): ?>
                                <button class="btn btn-sm btn-outline" onclick="updateStatus(<?= $b['id'] ?>, 'completed')">Complete</button>
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
        <a href="<?= site_url('admin/bookings?page=' . $p . '&status=' . urlencode($status) . '&search=' . urlencode($search)) ?>"
           class="page-btn <?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<script>
async function updateStatus(id, newStatus) {
    if (!confirm('Set booking status to "' + newStatus + '"?')) return;
    const token = localStorage.getItem('admin_token');
    const res = await fetch('/api/bookings/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify({ status: newStatus }),
    });
    if (res.ok) location.reload();
    else {
        const data = await res.json();
        alert(data.message || 'Failed to update status');
    }
}
</script>
