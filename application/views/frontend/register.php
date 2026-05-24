<?php $this->load->view('frontend/_header', ['page' => 'register', 'page_title' => 'Create Account']); ?>

<div class="auth-page">
  <div class="auth-card" style="max-width:480px">
    <div class="auth-logo">
      <span>🏨 Grand Palace</span>
    </div>
    <h1 class="auth-title">Create Account</h1>
    <p class="auth-subtitle">Join us for exclusive rates, easy booking management, and more</p>

    <div id="register-alert"></div>

    <form id="register-form">
      <div class="form-group">
        <label class="form-label" for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-input" placeholder="Jane Smith" required autocomplete="name">
      </div>
      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required autocomplete="email">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Phone Number <span style="color:var(--muted)">(optional)</span></label>
        <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 000-0000" autocomplete="tel">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-input" placeholder="Min. 8 characters" required autocomplete="new-password">
      </div>
      <div class="form-group">
        <label class="form-label" for="password_confirm">Confirm Password</label>
        <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="Repeat password" required autocomplete="new-password">
      </div>
      <p style="font-size:.8rem;color:var(--muted);margin-bottom:16px">By creating an account you agree to our Terms of Service and Privacy Policy.</p>
      <button type="submit" class="btn btn-navy btn-full" style="padding:14px">Create Account</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="/login">Sign in</a></p>
  </div>
</div>

<?php $this->load->view('frontend/_footer'); ?>
