<?php $this->load->view('frontend/_header', ['page' => 'home', 'transparent_nav' => true, 'page_title' => 'Luxury Hotel Experience']); ?>

<!-- ─── Hero ─── -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-badge">⭐ 5-Star Luxury Experience</div>
    <h1 class="hero-title">
      Your Perfect Stay<br>
      <span>Awaits You</span>
    </h1>
    <p class="hero-subtitle">Unwind in elegance — from intimate single rooms to sprawling presidential suites</p>

    <!-- Search Form -->
    <form id="hero-search" class="hero-search">
      <div class="form-group">
        <label class="form-label">Room Type</label>
        <select name="type" class="form-select">
          <option value="">Any Type</option>
          <option value="single">Single</option>
          <option value="double">Double</option>
          <option value="suite">Suite</option>
          <option value="deluxe">Deluxe</option>
          <option value="presidential">Presidential</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Check-In</label>
        <input type="date" name="check_in" class="form-input">
      </div>
      <div class="form-group">
        <label class="form-label">Check-Out</label>
        <input type="date" name="check_out" class="form-input">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-gold btn-lg" style="width:100%">Search Rooms</button>
      </div>
    </form>
  </div>
  <div class="hero-scroll">
    <span style="letter-spacing:1.5px;font-size:.72rem">SCROLL</span>
    <div class="hero-scroll-dot"></div>
  </div>
</section>

<!-- ─── Stats Bar ─── -->
<section class="stats-bar">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-num">250+</div>
        <div class="stat-label">Luxury Rooms</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">15</div>
        <div class="stat-label">Years of Excellence</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">50K+</div>
        <div class="stat-label">Happy Guests</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">4.9★</div>
        <div class="stat-label">Average Rating</div>
      </div>
    </div>
  </div>
</section>

<!-- ─── Featured Rooms ─── -->
<section class="section" style="background:var(--off-white)">
  <div class="container">
    <div class="text-center">
      <p style="color:var(--gold);font-weight:700;letter-spacing:1.5px;font-size:.8rem;text-transform:uppercase">OUR ACCOMMODATIONS</p>
      <h2 class="section-title">Featured Rooms &amp; Suites</h2>
      <div class="gold-line"></div>
      <p class="section-subtitle">Each room is thoughtfully designed for comfort, luxury, and a restful experience.</p>
    </div>
    <div class="rooms-grid" id="featured-rooms">
      <!-- Loaded via JS -->
    </div>
    <div class="text-center" style="margin-top:44px">
      <a href="/rooms" class="btn btn-navy btn-lg">View All Rooms &nbsp;→</a>
    </div>
  </div>
</section>

<!-- ─── Features ─── -->
<section class="section" style="background:var(--white)">
  <div class="container">
    <div class="text-center">
      <p style="color:var(--gold);font-weight:700;letter-spacing:1.5px;font-size:.8rem;text-transform:uppercase">WHY CHOOSE US</p>
      <h2 class="section-title">The Grand Palace Promise</h2>
      <div class="gold-line"></div>
      <p class="section-subtitle">We go above and beyond to make every stay unforgettable.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">💰</div>
        <h3 class="feature-title">Best Rate Guarantee</h3>
        <p class="feature-desc">Book directly with us and enjoy the lowest available rate, guaranteed — no third-party markups.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🔄</div>
        <h3 class="feature-title">Free Cancellation</h3>
        <p class="feature-desc">Plans change. Cancel up to 24 hours before check-in at no charge — complete peace of mind.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🛎</div>
        <h3 class="feature-title">24/7 Concierge</h3>
        <p class="feature-desc">Our dedicated team is available around the clock to fulfill every request and exceed expectations.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🍽</div>
        <h3 class="feature-title">Fine Dining</h3>
        <p class="feature-desc">Three award-winning restaurants, room service, and a rooftop bar with panoramic city views.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🧖</div>
        <h3 class="feature-title">World-Class Spa</h3>
        <p class="feature-desc">Rejuvenate in our full-service spa and wellness center featuring treatments from around the world.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🔒</div>
        <h3 class="feature-title">Safe &amp; Secure</h3>
        <p class="feature-desc">Your security is paramount — 24-hour CCTV, secure key access, and a trained security team.</p>
      </div>
    </div>
  </div>
</section>

<!-- ─── Testimonials ─── -->
<section class="section testimonials-section">
  <div class="container">
    <div class="text-center">
      <p style="color:var(--gold);font-weight:700;letter-spacing:1.5px;font-size:.8rem;text-transform:uppercase">GUEST REVIEWS</p>
      <h2 class="section-title" style="color:var(--white)">What Our Guests Say</h2>
      <div class="gold-line"></div>
      <p class="section-subtitle" style="color:rgba(255,255,255,.6)">Real stories from guests who experienced the Grand Palace difference.</p>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-text">"Absolutely breathtaking. From the moment we arrived, every detail was perfect. The suite was immaculate, the staff unbelievably attentive. We'll be back every anniversary."</p>
        <div class="testimonial-author">
          <div class="author-avatar">SB</div>
          <div>
            <div class="author-name">Sarah B.</div>
            <div class="author-location">New York, USA</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-text">"The presidential suite was an experience unlike any other — jaw-dropping city view, a private plunge pool, and a butler who remembered every preference. Exceptional."</p>
        <div class="testimonial-author">
          <div class="author-avatar">JM</div>
          <div>
            <div class="author-name">James M.</div>
            <div class="author-location">London, UK</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-text">"I travel 30 weeks a year for business. Grand Palace is the only hotel that makes me feel at home. The beds, the food, the service — consistently flawless, every time."</p>
        <div class="testimonial-author">
          <div class="author-avatar">AL</div>
          <div>
            <div class="author-name">Aiko L.</div>
            <div class="author-location">Tokyo, Japan</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ─── CTA ─── -->
<section class="cta-section">
  <div class="container">
    <h2 class="cta-title">Ready for an Unforgettable Stay?</h2>
    <p class="cta-subtitle">Book now and enjoy exclusive member benefits and our best available rates.</p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;position:relative;z-index:1">
      <a href="/rooms" class="btn btn-gold btn-lg">Browse All Rooms</a>
      <a href="/register" class="btn btn-outline btn-lg">Create Account</a>
    </div>
  </div>
</section>

<?php $this->load->view('frontend/_footer'); ?>
