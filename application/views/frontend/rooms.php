<?php $this->load->view('frontend/_header', ['page' => 'rooms', 'page_title' => 'All Rooms']); ?>

<!-- ─── Page Hero ─── -->
<section class="page-hero">
  <div class="container page-hero-content">
    <div class="breadcrumb">
      <a href="/">Home</a>
      <span>/</span>
      <span style="color:var(--gold)">Rooms</span>
    </div>
    <h1>Our Rooms &amp; Suites</h1>
    <p>Choose your perfect accommodation from our curated collection</p>
  </div>
</section>

<!-- ─── Rooms Section ─── -->
<section class="section-sm">
  <div class="container">

    <!-- Filter Bar -->
    <div class="filter-bar">
      <form id="filter-form" style="display:contents">
        <div class="form-group">
          <label class="form-label">Room Type</label>
          <select name="type" class="form-select">
            <option value="">All Types</option>
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
        <div style="display:flex;gap:10px;align-items:flex-end">
          <button type="submit" class="btn btn-gold">Search</button>
          <button type="button" class="btn btn-outline reset-btn">Reset</button>
        </div>
      </form>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
      <p id="rooms-count" style="color:var(--muted);font-size:.9rem">Loading…</p>
    </div>

    <!-- Rooms Grid -->
    <div class="rooms-grid" id="rooms-grid">
      <!-- Loaded via JS -->
    </div>

  </div>
</section>

<?php $this->load->view('frontend/_footer'); ?>
