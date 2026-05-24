<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' : '' ?>The Grand Palace Hotel</title>
  <meta name="description" content="Luxury hotel with world-class amenities. Book your perfect stay online.">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏨</text></svg>">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body data-page="<?= isset($page) ? $page : '' ?>" <?= isset($room_id) ? 'data-room-id="' . (int)$room_id . '"' : '' ?>>

<!-- ─── Navbar ─── -->
<nav id="navbar" class="navbar" data-transparent="<?= isset($transparent_nav) && $transparent_nav ? '1' : '0' ?>">
  <a href="/" class="navbar-brand">
    <div class="brand-icon">🏨</div>
    Grand Palace
  </a>

  <ul class="navbar-nav">
    <li><a href="/"        class="nav-link <?= ($page??'')==='home'?'active':'' ?>">Home</a></li>
    <li><a href="/rooms"   class="nav-link <?= ($page??'')==='rooms'?'active':'' ?>">Rooms</a></li>
    <li><a href="/admin"   class="nav-link">Admin</a></li>
  </ul>

  <div class="navbar-actions">
    <a href="/login"     class="btn btn-outline btn-sm" id="nav-login">Sign In</a>
    <div id="nav-user" class="flex" style="align-items:center;gap:10px;display:none">
      <a href="/dashboard" class="nav-link" style="color:var(--gold)">👤 <span id="nav-username"></span></a>
      <button id="nav-logout" class="btn btn-sm" style="background:rgba(255,255,255,.12);color:#fff">Sign Out</button>
    </div>
  </div>

  <div class="hamburger" id="hamburger" aria-label="Open menu">
    <span></span><span></span><span></span>
  </div>
</nav>

<!-- ─── Mobile Menu ─── -->
<div class="mobile-menu" id="mobile-menu">
  <button class="mobile-close" id="mobile-close">✕</button>
  <a href="/"         class="nav-link">Home</a>
  <a href="/rooms"    class="nav-link">Rooms</a>
  <a href="/dashboard" class="nav-link">My Bookings</a>
  <a href="/admin"    class="nav-link">Admin</a>
  <a href="/login"    class="btn btn-gold" style="margin-top:16px">Sign In</a>
</div>
