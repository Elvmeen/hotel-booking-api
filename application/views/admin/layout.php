<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking — Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <span class="logo-mark">HB</span>
            <span class="logo-text">Hotel Admin</span>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= site_url('admin/dashboard') ?>" class="nav-item <?= ($page === 'admin/dashboard') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="<?= site_url('admin/rooms') ?>" class="nav-item <?= ($page === 'admin/rooms') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Rooms
            </a>
            <a href="<?= site_url('admin/bookings') ?>" class="nav-item <?= ($page === 'admin/bookings') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                Bookings
            </a>
            <a href="<?= site_url('admin/users') ?>" class="nav-item <?= ($page === 'admin/users') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Users
            </a>
        </nav>
        <div class="sidebar-footer">
            <span class="admin-name"><?= htmlspecialchars($admin_name ?? '') ?></span>
            <a href="<?= site_url('admin/logout') ?>" class="logout-btn">Sign out</a>
        </div>
    </aside>

    <!-- Main content -->
    <main class="main">
        <?= $this->load->view($page, $data ?? [], TRUE) ?>
    </main>
</div>
<script src="<?= base_url('assets/js/admin.js') ?>"></script>
<?php if ($jwt = $this->session->userdata('admin_jwt')): ?>
<script>
    localStorage.setItem('admin_token', '<?= addslashes($jwt) ?>');
</script>
<?php endif; ?>
</body>
</html>
