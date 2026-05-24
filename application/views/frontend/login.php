<?php $this->load->view('frontend/_header', ['page' => 'login', 'page_title' => 'Sign In']); ?>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <span>🏨 Grand Palace</span>
    </div>
    <h1 class="auth-title">Welcome Back</h1>
    <p class="auth-subtitle">Sign in to manage your bookings and enjoy member benefits</p>

    <div id="login-alert"></div>

    <form id="login-form">
      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required autocomplete="email">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-navy btn-full" style="margin-top:8px;padding:14px">Sign In</button>
    </form>

    <div class="auth-divider"><span>or try demo credentials</span></div>
    <div style="background:var(--light);border-radius:8px;padding:14px 16px;font-size:.85rem;color:var(--muted)">
      <strong style="color:var(--navy)">Admin:</strong> admin@hotelbooking.com / Admin@1234<br>
      <strong style="color:var(--navy)">Guest:</strong> guest1@example.com / Guest@1234
    </div>

    <p class="auth-switch">Don't have an account? <a href="/register">Create one free</a></p>
  </div>
</div>

<?php $this->load->view('frontend/_footer'); ?>
