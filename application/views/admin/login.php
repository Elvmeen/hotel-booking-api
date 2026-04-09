<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">HB</div>
        <h1>Hotel Booking</h1>
        <p>Sign in to the admin panel</p>
    </div>

    <?php if ($error = $this->session->flashdata('error')): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= site_url('admin/login/do') ?>" class="auth-form">
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" required autocomplete="email"
                   placeholder="admin@example.com" value="<?= set_value('email') ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required
                   placeholder="••••••••" autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Sign in</button>
    </form>
</div>
