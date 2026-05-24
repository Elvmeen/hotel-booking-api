/* ============================================================
   Hotel Booking — Frontend JavaScript
   Handles API calls, routing helpers, auth, and UI
   ============================================================ */

const API = '/api';
const TOKEN_KEY = 'hb_token';
const USER_KEY  = 'hb_user';

/* ─── Auth Helpers ─── */
const Auth = {
  getToken: () => localStorage.getItem(TOKEN_KEY),
  getUser:  () => { try { return JSON.parse(localStorage.getItem(USER_KEY)); } catch { return null; } },
  isLoggedIn: () => !!localStorage.getItem(TOKEN_KEY),
  save: (token, user) => { localStorage.setItem(TOKEN_KEY, token); localStorage.setItem(USER_KEY, JSON.stringify(user)); },
  logout: () => { localStorage.removeItem(TOKEN_KEY); localStorage.removeItem(USER_KEY); window.location.href = '/'; }
};

/* ─── HTTP Client ─── */
async function http(method, url, data = null) {
  const headers = { 'Content-Type': 'application/json' };
  if (Auth.getToken()) headers['Authorization'] = 'Bearer ' + Auth.getToken();
  const opts = { method, headers };
  if (data) opts.body = JSON.stringify(data);
  const res = await fetch(url, opts);
  const json = await res.json();
  return { ok: res.ok, status: res.status, data: json };
}
const get  = (url) => http('GET', url);
const post = (url, data) => http('POST', url, data);
const put  = (url, data) => http('PUT',  url, data);
const del  = (url) => http('DELETE', url);

/* ─── Navbar ─── */
function initNavbar() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const transparent = navbar.dataset.transparent === '1';
  if (transparent) {
    navbar.classList.add('transparent');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('solid', window.scrollY > 60);
      navbar.classList.toggle('transparent', window.scrollY <= 60);
    });
  } else {
    navbar.classList.add('solid');
  }

  // Auth-aware nav
  const user = Auth.getUser();
  const loginBtn  = document.getElementById('nav-login');
  const userMenu  = document.getElementById('nav-user');
  const userLabel = document.getElementById('nav-username');
  const logoutBtn = document.getElementById('nav-logout');

  if (user && Auth.isLoggedIn()) {
    if (loginBtn)  loginBtn.classList.add('hidden');
    if (userMenu)  userMenu.classList.remove('hidden');
    if (userLabel) userLabel.textContent = user.name.split(' ')[0];
  } else {
    if (userMenu) userMenu.classList.add('hidden');
  }

  if (logoutBtn) logoutBtn.addEventListener('click', () => {
    post(API + '/auth/logout').finally(() => Auth.logout());
  });

  // Mobile menu
  const hamburger   = document.getElementById('hamburger');
  const mobileMenu  = document.getElementById('mobile-menu');
  const mobileClose = document.getElementById('mobile-close');
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => mobileMenu.classList.add('open'));
    mobileClose?.addEventListener('click', () => mobileMenu.classList.remove('open'));
  }
}

/* ─── Room Images (Unsplash by type) ─── */
const ROOM_IMAGES = {
  single:        'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=75',
  double:        'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600&q=75',
  suite:         'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&q=75',
  deluxe:        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=600&q=75',
  presidential:  'https://images.unsplash.com/photo-1629140727571-9b5c6f6267b4?w=600&q=75',
  default:       'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=600&q=75',
};
function roomImg(type) { return ROOM_IMAGES[type] || ROOM_IMAGES.default; }

/* ─── Skeleton Loader ─── */
function skeletonCard() {
  return `<div class="skeleton-card">
    <div class="skeleton skeleton-img"></div>
    <div class="skeleton-body">
      <div class="skeleton skeleton-line short" style="height:12px;margin-bottom:8px"></div>
      <div class="skeleton skeleton-line medium" style="height:18px;margin-bottom:12px"></div>
      <div class="skeleton skeleton-line" style="height:12px;margin-bottom:8px"></div>
      <div style="display:flex;justify-content:space-between;margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
        <div class="skeleton skeleton-line" style="width:80px;height:22px;margin:0"></div>
        <div class="skeleton skeleton-line" style="width:90px;height:36px;margin:0;border-radius:8px"></div>
      </div>
    </div>
  </div>`;
}

function amenityTags(str, max = 4) {
  if (!str) return '';
  return str.split(',').slice(0, max).map(a => `<span class="amenity-tag">${a.trim()}</span>`).join('');
}

function roomCard(room) {
  const img     = roomImg(room.type);
  const price   = parseFloat(room.price_per_night).toLocaleString('en-US', { minimumFractionDigits: 0 });
  const typeStr = room.type.charAt(0).toUpperCase() + room.type.slice(1);
  return `
  <div class="room-card fade-in">
    <div class="room-card-img">
      <img src="${img}" alt="Room ${room.room_number}" loading="lazy" onerror="this.src='${ROOM_IMAGES.default}'">
      <span class="room-card-badge badge-active">${typeStr}</span>
    </div>
    <div class="room-card-body">
      <div class="room-card-type">${typeStr} Room</div>
      <h3 class="room-card-title">Room ${room.room_number}</h3>
      <p style="font-size:.88rem;color:var(--muted);line-height:1.55">${(room.description||'').slice(0,80)}${(room.description||'').length>80?'…':''}</p>
      <div class="room-card-amenities">${amenityTags(room.amenities)}</div>
      <div class="room-card-info">
        <span class="room-info-item">🛏 Floor ${room.floor}</span>
        <span class="room-info-item">👥 Up to ${room.capacity} guest${room.capacity>1?'s':''}</span>
      </div>
      <div class="room-card-footer">
        <div class="room-price">
          <div class="room-price-amount">$${price}</div>
          <div class="room-price-night">per night</div>
        </div>
        <a href="/room/${room.id}" class="btn btn-gold btn-sm">Book Now</a>
      </div>
    </div>
  </div>`;
}

/* ─── Alert helper ─── */
function showAlert(container, msg, type = 'error') {
  if (!container) return;
  container.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
  container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function clearAlert(container) { if (container) container.innerHTML = ''; }

/* ─── Home Page ─── */
async function initHome() {
  const grid = document.getElementById('featured-rooms');
  if (!grid) return;

  // Skeleton
  grid.innerHTML = Array(3).fill(skeletonCard()).join('');

  const res = await get(API + '/rooms?per_page=6');
  if (!res.ok) { grid.innerHTML = '<p style="color:var(--muted)">Could not load rooms.</p>'; return; }
  const rooms = res.data.data?.rooms || [];
  if (!rooms.length) { grid.innerHTML = '<p style="color:var(--muted)">No rooms available.</p>'; return; }
  grid.innerHTML = rooms.slice(0,3).map(roomCard).join('');

  // Hero search form
  const searchForm = document.getElementById('hero-search');
  if (searchForm) {
    searchForm.addEventListener('submit', e => {
      e.preventDefault();
      const fd = new FormData(searchForm);
      const params = new URLSearchParams();
      if (fd.get('type')) params.set('type', fd.get('type'));
      if (fd.get('check_in'))  params.set('check_in',  fd.get('check_in'));
      if (fd.get('check_out')) params.set('check_out', fd.get('check_out'));
      window.location.href = '/rooms?' + params.toString();
    });
  }

  // Set min date for date pickers
  const today = new Date().toISOString().split('T')[0];
  document.querySelectorAll('input[type="date"]').forEach(el => el.min = today);
}

/* ─── Rooms Listing Page ─── */
async function initRooms() {
  const grid = document.getElementById('rooms-grid');
  if (!grid) return;

  const params = new URLSearchParams(window.location.search);
  const filterForm = document.getElementById('filter-form');

  // Populate filter form from URL
  if (filterForm) {
    ['type','check_in','check_out','min_price','max_price'].forEach(k => {
      const el = filterForm.elements[k];
      if (el && params.get(k)) el.value = params.get(k);
    });
    filterForm.addEventListener('submit', e => {
      e.preventDefault();
      const fd = new FormData(filterForm);
      const q = new URLSearchParams();
      fd.forEach((v, k) => { if (v) q.set(k, v); });
      window.location.href = '/rooms?' + q.toString();
    });
    filterForm.querySelector('.reset-btn')?.addEventListener('click', () => {
      window.location.href = '/rooms';
    });
  }

  // Set min date
  const today = new Date().toISOString().split('T')[0];
  document.querySelectorAll('input[type="date"]').forEach(el => el.min = today);

  // Load rooms
  grid.innerHTML = Array(6).fill(skeletonCard()).join('');
  const q = new URLSearchParams();
  if (params.get('type'))      q.set('type', params.get('type'));
  if (params.get('check_in'))  q.set('check_in', params.get('check_in'));
  if (params.get('check_out')) q.set('check_out', params.get('check_out'));
  q.set('per_page', '12');

  const res = await get(API + '/rooms?' + q.toString());
  if (!res.ok) { grid.innerHTML = '<p class="text-center" style="color:var(--muted)">Failed to load rooms.</p>'; return; }
  const rooms = res.data.data?.rooms || [];

  if (!rooms.length) {
    grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1">
      <div class="empty-state-icon">🏨</div>
      <h3>No rooms found</h3>
      <p>Try adjusting your filters or check back later.</p>
    </div>`;
    return;
  }
  grid.innerHTML = rooms.map(roomCard).join('');
  document.getElementById('rooms-count').textContent = `${rooms.length} room${rooms.length !== 1 ? 's' : ''} found`;
}

/* ─── Room Detail Page ─── */
async function initRoomDetail() {
  const roomId = document.body.dataset.roomId;
  if (!roomId) return;

  const detailEl = document.getElementById('room-detail');
  const bookingEl = document.getElementById('booking-card');
  const today = new Date().toISOString().split('T')[0];

  // Set date min
  document.querySelectorAll('input[type="date"]').forEach(el => el.min = today);

  const res = await get(API + '/rooms/' + roomId);
  if (!res.ok) {
    detailEl.innerHTML = '<div class="alert alert-error">Room not found.</div>';
    return;
  }
  const room = res.data.data?.room || res.data.data || {};
  const img = roomImg(room.type);
  const price = parseFloat(room.price_per_night);
  const typeStr = room.type ? room.type.charAt(0).toUpperCase() + room.type.slice(1) : '';

  // Hero image
  document.getElementById('room-hero-img').src = img;
  document.getElementById('room-hero-name').textContent = `Room ${room.room_number}`;
  document.getElementById('room-hero-type').textContent = typeStr;

  // Detail body
  detailEl.innerHTML = `
    <div class="room-detail-info">
      <span class="room-type-tag">${typeStr}</span>
      <h1 class="room-detail-title">Room ${room.room_number}</h1>
      <div class="room-detail-price">$${price.toLocaleString()} <small>/ night</small></div>
      <p style="color:var(--muted);line-height:1.75;margin:16px 0">${room.description || ''}</p>
      <div class="room-meta-grid">
        <div class="room-meta-item"><div class="room-meta-icon">🏢</div><div class="room-meta-value">Floor ${room.floor}</div><div class="room-meta-label">Floor</div></div>
        <div class="room-meta-item"><div class="room-meta-icon">👥</div><div class="room-meta-value">${room.capacity}</div><div class="room-meta-label">Max Guests</div></div>
        <div class="room-meta-item"><div class="room-meta-icon">🛏</div><div class="room-meta-value">${typeStr}</div><div class="room-meta-label">Room Type</div></div>
      </div>
      <h3 style="font-size:1rem;margin-bottom:12px;margin-top:8px">Amenities</h3>
      <div class="amenities-list">${(room.amenities || '').split(',').map(a => `<span class="amenity-pill">✓ ${a.trim()}</span>`).join('')}</div>
    </div>`;

  // Booking card
  const priceEl = document.getElementById('booking-price');
  const nightsEl = document.getElementById('booking-nights');
  const totalEl  = document.getElementById('booking-total');
  if (priceEl) priceEl.textContent = `$${price.toLocaleString()} / night`;

  function calcNights() {
    const ci = document.getElementById('b-check-in')?.value;
    const co = document.getElementById('b-check-out')?.value;
    if (!ci || !co) return 0;
    const diff = (new Date(co) - new Date(ci)) / 86400000;
    return diff > 0 ? Math.round(diff) : 0;
  }

  function updatePrice() {
    const nights = calcNights();
    if (nights > 0) {
      if (nightsEl) nightsEl.textContent = `${nights} night${nights>1?'s':''} × $${price.toLocaleString()}`;
      if (totalEl)  totalEl.textContent  = `$${(nights * price).toLocaleString()}`;
    } else {
      if (nightsEl) nightsEl.textContent = '—';
      if (totalEl)  totalEl.textContent  = '—';
    }
  }

  document.getElementById('b-check-in')?.addEventListener('change', function() {
    const co = document.getElementById('b-check-out');
    if (co) co.min = this.value;
    updatePrice();
  });
  document.getElementById('b-check-out')?.addEventListener('change', updatePrice);

  // Submit booking
  const bookingForm = document.getElementById('booking-form');
  const alertEl     = document.getElementById('booking-alert');
  const submitBtn   = document.getElementById('booking-submit');
  if (!bookingForm) return;

  bookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAlert(alertEl);

    if (!Auth.isLoggedIn()) {
      window.location.href = '/login?redirect=/room/' + roomId;
      return;
    }

    const nights = calcNights();
    if (nights <= 0) { showAlert(alertEl, 'Please select valid check-in and check-out dates.'); return; }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing…';

    const payload = {
      room_id:           parseInt(roomId),
      check_in:          document.getElementById('b-check-in').value,
      check_out:         document.getElementById('b-check-out').value,
      guests:            parseInt(document.getElementById('b-guests').value) || 1,
      special_requests:  document.getElementById('b-requests')?.value || '',
    };

    const res2 = await post(API + '/bookings', payload);
    submitBtn.disabled = false;
    submitBtn.textContent = 'Book Now';

    if (res2.ok) {
      const ref = res2.data.data?.booking?.booking_reference || '';
      document.getElementById('success-ref').textContent = ref;
      document.getElementById('success-overlay').classList.add('open');
    } else {
      showAlert(alertEl, res2.data.message || 'Booking failed. Please try again.');
    }
  });
}

/* ─── Login Page ─── */
async function initLogin() {
  if (Auth.isLoggedIn()) { window.location.href = '/dashboard'; return; }
  const form    = document.getElementById('login-form');
  const alertEl = document.getElementById('login-alert');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAlert(alertEl);
    const btn = form.querySelector('[type=submit]');
    btn.disabled = true; btn.textContent = 'Signing in…';

    const res = await post(API + '/auth/login', {
      email:    form.email.value.trim(),
      password: form.password.value,
    });

    btn.disabled = false; btn.textContent = 'Sign In';

    if (res.ok) {
      Auth.save(res.data.data.token, res.data.data.user);
      const redir = new URLSearchParams(window.location.search).get('redirect') || '/dashboard';
      window.location.href = redir;
    } else {
      showAlert(alertEl, res.data.message || 'Login failed.');
    }
  });
}

/* ─── Register Page ─── */
async function initRegister() {
  if (Auth.isLoggedIn()) { window.location.href = '/dashboard'; return; }
  const form    = document.getElementById('register-form');
  const alertEl = document.getElementById('register-alert');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAlert(alertEl);

    if (form.password.value !== form.password_confirm.value) {
      showAlert(alertEl, 'Passwords do not match.'); return;
    }
    if (form.password.value.length < 8) {
      showAlert(alertEl, 'Password must be at least 8 characters.'); return;
    }

    const btn = form.querySelector('[type=submit]');
    btn.disabled = true; btn.textContent = 'Creating account…';

    const res = await post(API + '/auth/register', {
      name:             form.elements.name.value.trim(),
      email:            form.email.value.trim(),
      phone:            form.phone?.value.trim() || '',
      password:         form.password.value,
      password_confirm: form.password_confirm.value,
    });

    btn.disabled = false; btn.textContent = 'Create Account';

    if (res.ok) {
      Auth.save(res.data.data.token, res.data.data.user);
      window.location.href = '/dashboard';
    } else {
      const errs = res.data.errors ? Object.values(res.data.errors).join('<br>') : (res.data.message || 'Registration failed.');
      showAlert(alertEl, errs);
    }
  });
}

/* ─── Dashboard Page ─── */
async function initDashboard() {
  if (!Auth.isLoggedIn()) { window.location.href = '/login?redirect=/dashboard'; return; }

  const user = Auth.getUser();
  document.getElementById('dash-name').textContent = user?.name || 'Guest';
  document.getElementById('dash-email').textContent = user?.email || '';

  // Load bookings
  const res = await get(API + '/bookings');
  const bookings = res.ok ? (res.data.data?.bookings || []) : [];

  // Stats
  const total     = bookings.length;
  const upcoming  = bookings.filter(b => b.status === 'confirmed' && new Date(b.check_in) > new Date()).length;
  const spent     = bookings.filter(b => b.status !== 'cancelled').reduce((s,b) => s + parseFloat(b.total_price||0), 0);
  const cancelled = bookings.filter(b => b.status === 'cancelled').length;

  document.getElementById('stat-total').textContent    = total;
  document.getElementById('stat-upcoming').textContent  = upcoming;
  document.getElementById('stat-spent').textContent    = '$' + spent.toLocaleString('en-US', {minimumFractionDigits:0});
  document.getElementById('stat-cancelled').textContent = cancelled;

  // Table
  const tbody = document.getElementById('bookings-tbody');
  if (!bookings.length) {
    tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state">
      <div class="empty-state-icon">📅</div>
      <h3>No bookings yet</h3>
      <p><a href="/rooms" style="color:var(--gold);font-weight:600">Browse rooms</a> to make your first reservation.</p>
    </div></td></tr>`;
    return;
  }

  tbody.innerHTML = bookings.map(b => {
    const ci = new Date(b.check_in).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'});
    const co = new Date(b.check_out).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'});
    const price = parseFloat(b.total_price||0).toLocaleString('en-US', {style:'currency',currency:'USD',minimumFractionDigits:0});
    return `<tr>
      <td><strong>${b.booking_reference || '—'}</strong></td>
      <td>Room ${b.room_id || '—'}</td>
      <td>${ci} → ${co}<br><small style="color:var(--muted)">${b.nights} night${b.nights>1?'s':''}</small></td>
      <td><strong>${price}</strong></td>
      <td><span class="badge badge-${b.status}">${b.status}</span></td>
      <td>
        ${b.status === 'pending' || b.status === 'confirmed' ? `<button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;font-weight:600" onclick="cancelBooking(${b.id}, this)">Cancel</button>` : '—'}
      </td>
    </tr>`;
  }).join('');
}

window.cancelBooking = async (id, btn) => {
  if (!confirm('Cancel this booking?')) return;
  btn.disabled = true; btn.textContent = '…';
  const res = await del(API + '/bookings/' + id);
  if (res.ok) {
    showAlert(document.getElementById('bookings-alert'), 'Booking cancelled.', 'success');
    initDashboard();
  } else {
    btn.disabled = false; btn.textContent = 'Cancel';
    alert(res.data.message || 'Could not cancel.');
  }
};

/* ─── Bootstrap ─── */
document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  const page = document.body.dataset.page;
  if (page === 'home')    initHome();
  if (page === 'rooms')   initRooms();
  if (page === 'room')    initRoomDetail();
  if (page === 'login')   initLogin();
  if (page === 'register') initRegister();
  if (page === 'dashboard') initDashboard();
});
