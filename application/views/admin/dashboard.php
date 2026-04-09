<div class="page-header">
    <h1>Dashboard</h1>
    <span class="page-subtitle">Welcome back, <?= htmlspecialchars($admin_name ?? 'Admin') ?></span>
</div>

<!-- Stats grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon--blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Total Rooms</span>
            <span class="stat-value"><?= $room_stats['total'] ?></span>
            <span class="stat-sub"><?= $room_stats['active'] ?> active</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Total Bookings</span>
            <span class="stat-value"><?= $booking_stats['total'] ?></span>
            <span class="stat-sub"><?= $booking_stats['today'] ?> today</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Total Guests</span>
            <span class="stat-value"><?= $user_count ?></span>
            <span class="stat-sub">registered users</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="stat-body">
            <span class="stat-label">Revenue This Month</span>
            <span class="stat-value">$<?= number_format($revenue['month'], 2) ?></span>
            <span class="stat-sub">$<?= number_format($revenue['today'], 2) ?> today</span>
        </div>
    </div>
</div>

<!-- Booking status breakdown -->
<div class="section-grid">
    <div class="card">
        <div class="card-header"><h2>Booking Status</h2></div>
        <div class="card-body">
            <div class="status-list">
                <div class="status-item">
                    <span class="badge badge-warning">Pending</span>
                    <strong><?= $booking_stats['pending'] ?></strong>
                </div>
                <div class="status-item">
                    <span class="badge badge-success">Confirmed</span>
                    <strong><?= $booking_stats['confirmed'] ?></strong>
                </div>
                <div class="status-item">
                    <span class="badge badge-danger">Cancelled</span>
                    <strong><?= $booking_stats['cancelled'] ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Revenue Overview</h2></div>
        <div class="card-body">
            <div class="status-list">
                <div class="status-item">
                    <span>Today</span>
                    <strong>$<?= number_format($revenue['today'], 2) ?></strong>
                </div>
                <div class="status-item">
                    <span>This Month</span>
                    <strong>$<?= number_format($revenue['month'], 2) ?></strong>
                </div>
                <div class="status-item">
                    <span>This Year</span>
                    <strong>$<?= number_format($revenue['year'], 2) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent bookings -->
<div class="card mt-4">
    <div class="card-header">
        <h2>Recent Bookings</h2>
        <a href="<?= site_url('admin/bookings') ?>" class="btn btn-sm btn-outline">View all</a>
    </div>
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
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($recent_bookings)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No bookings yet</td></tr>
                <?php else: ?>
                    <?php foreach ($recent_bookings as $b): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($b['booking_reference']) ?></code></td>
                        <td><?= htmlspecialchars($b['guest_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($b['room_number'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($b['check_in']) ?></td>
                        <td><?= htmlspecialchars($b['check_out']) ?></td>
                        <td>$<?= number_format($b['total_price'], 2) ?></td>
                        <td><span class="badge badge-<?= $b['status'] === 'confirmed' ? 'success' : ($b['status'] === 'pending' ? 'warning' : 'danger') ?>"><?= ucfirst($b['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
