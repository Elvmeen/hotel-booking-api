<?php $this->load->view('frontend/_header', ['page' => 'room', 'page_title' => 'Room Detail', 'room_id' => $room_id]); ?>

<div class="room-detail-page">

  <!-- ─── Room Hero Image ─── -->
  <div class="room-hero">
    <img id="room-hero-img" src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1200&q=80" alt="Room">
    <div class="room-hero-overlay"></div>
    <div class="room-hero-meta">
      <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
          <a href="/">Home</a><span>/</span>
          <a href="/rooms">Rooms</a><span>/</span>
          <span style="color:var(--gold)" id="room-hero-type"></span>
        </div>
        <h2 style="color:var(--white);font-size:2rem" id="room-hero-name">Loading…</h2>
      </div>
    </div>
  </div>

  <!-- ─── Detail Body ─── -->
  <section class="room-detail-body">
    <div class="container">
      <div class="room-detail-grid">

        <!-- Left: Info (loaded by JS) -->
        <div id="room-detail">
          <div class="skeleton-card">
            <div class="skeleton-body">
              <div class="skeleton skeleton-line short" style="height:16px;margin-bottom:12px"></div>
              <div class="skeleton skeleton-line" style="height:32px;margin-bottom:10px"></div>
              <div class="skeleton skeleton-line medium" style="height:14px;margin-bottom:8px"></div>
              <div class="skeleton skeleton-line" style="height:14px"></div>
            </div>
          </div>
        </div>

        <!-- Right: Booking Card -->
        <div>
          <div class="booking-card">
            <h3 class="booking-card-title">Book This Room</h3>
            <div class="booking-card-price" id="booking-price">Loading…</div>

            <div id="booking-alert"></div>

            <form id="booking-form">
              <div class="form-group">
                <label class="form-label">Check-In Date</label>
                <input type="date" id="b-check-in" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label">Check-Out Date</label>
                <input type="date" id="b-check-out" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label">Number of Guests</label>
                <select id="b-guests" class="form-select">
                  <option value="1">1 Guest</option>
                  <option value="2">2 Guests</option>
                  <option value="3">3 Guests</option>
                  <option value="4">4 Guests</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Special Requests <span style="color:var(--muted)">(optional)</span></label>
                <textarea id="b-requests" class="form-input" rows="3" placeholder="Dietary requirements, accessibility needs, celebrations…" style="resize:vertical"></textarea>
              </div>

              <div class="price-summary">
                <div class="price-row"><span>Rate</span><span id="booking-nights">Select dates</span></div>
                <div class="price-row total"><span>Estimated Total</span><span id="booking-total">—</span></div>
              </div>

              <button type="submit" id="booking-submit" class="btn btn-gold btn-full btn-lg" style="margin-top:8px">Book Now</button>
              <p style="text-align:center;font-size:.78rem;color:var(--muted);margin-top:10px">Free cancellation up to 24h before check-in</p>
            </form>
          </div>
        </div>

      </div>
    </div>
  </section>

</div>

<!-- ─── Success Overlay ─── -->
<div class="success-overlay" id="success-overlay">
  <div class="success-box">
    <div class="success-icon">🎉</div>
    <h2 class="success-title">Booking Confirmed!</h2>
    <p class="success-msg">Your reservation has been placed successfully.<br>Reference: <strong id="success-ref" style="color:var(--gold)"></strong></p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <a href="/dashboard" class="btn btn-navy">View My Bookings</a>
      <a href="/rooms" class="btn btn-outline">Browse More Rooms</a>
    </div>
  </div>
</div>

<?php $this->load->view('frontend/_footer'); ?>
