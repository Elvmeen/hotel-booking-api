<?php $this->load->view('frontend/_header', ['page' => 'dashboard', 'page_title' => 'My Dashboard']); ?>

<div class="dashboard-page">

  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <div class="container">
      <p style="color:var(--gold);font-size:.8rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:6px">WELCOME BACK</p>
      <h1 id="dash-name">Loading…</h1>
      <p id="dash-email"></p>
    </div>
  </div>

  <!-- Dashboard Body -->
  <div class="dashboard-body">
    <div class="container">

      <!-- Stats Cards -->
      <div class="stats-cards">
        <div class="stat-card">
          <div class="stat-card-icon gold">📋</div>
          <div>
            <div class="stat-card-value" id="stat-total">—</div>
            <div class="stat-card-label">Total Bookings</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-card-icon blue">📅</div>
          <div>
            <div class="stat-card-value" id="stat-upcoming">—</div>
            <div class="stat-card-label">Upcoming Stays</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-card-icon green">💰</div>
          <div>
            <div class="stat-card-value" id="stat-spent">—</div>
            <div class="stat-card-label">Total Spent</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-card-icon red">❌</div>
          <div>
            <div class="stat-card-value" id="stat-cancelled">—</div>
            <div class="stat-card-label">Cancelled</div>
          </div>
        </div>
      </div>

      <!-- Bookings Table -->
      <div class="card">
        <div class="card-header">
          <h3>My Reservations</h3>
          <a href="/rooms" class="btn btn-gold btn-sm">+ New Booking</a>
        </div>
        <div id="bookings-alert"></div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Reference</th>
                <th>Room</th>
                <th>Dates</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="bookings-tbody">
              <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:var(--muted)">
                  <div style="display:inline-block;width:24px;height:24px;border:3px solid var(--border);border-top-color:var(--gold);border-radius:50%;animation:spin .8s linear infinite"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<?php $this->load->view('frontend/_footer'); ?>
