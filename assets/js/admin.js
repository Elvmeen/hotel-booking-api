/**
 * Admin Panel — JavaScript
 */
'use strict';

/* -----------------------------------------------------------------------
   Modal helpers
   ----------------------------------------------------------------------- */
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            const hiddenId = form.querySelector('input[type="hidden"]');
            if (hiddenId) hiddenId.value = '';
        }
    }
}

/* Close modal on Escape */
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(m => {
            if (m.style.display === 'flex') closeModal(m.id);
        });
    }
});

/* -----------------------------------------------------------------------
   Token management
   Admin dashboard pages use a JWT stored in localStorage to call
   the API endpoints directly from the browser (CRUD operations).
   On first load, the token is absent — prompt admin to log in via API.
   ----------------------------------------------------------------------- */
(function initAdminToken() {
    setTimeout(function() {
    if (!localStorage.getItem('admin_token') || localStorage.getItem('admin_token') === 'null') {
        const banner = document.createElement('div');
        banner.style.cssText = [
            'position:fixed;bottom:20px;right:20px;z-index:9999;',
            'background:#1e293b;color:#fff;padding:14px 18px;',
            'border-radius:8px;font-size:13px;max-width:340px;',
            'box-shadow:0 4px 20px rgba(0,0,0,.4);line-height:1.5;',
        ].join('');
        banner.innerHTML = `
            <strong style="display:block;margin-bottom:6px;">API Token Required</strong>
            To use the admin panel action buttons (confirm, cancel, edit rooms, etc.)
            you need to authenticate via the REST API and paste your JWT token here.
            <br><br>
            <input id="token-input" type="text" placeholder="Paste JWT token…"
                style="width:100%;padding:7px 10px;border-radius:5px;border:none;
                       font-size:12px;color:#1a202c;margin-bottom:8px;">
            <button onclick="saveToken()" style="background:#2563eb;color:#fff;border:none;
                padding:6px 14px;border-radius:5px;cursor:pointer;font-size:12px;">Save Token</button>
            <button onclick="this.closest('div').remove()"
                style="background:transparent;color:#94a3b8;border:none;
                padding:6px 10px;border-radius:5px;cursor:pointer;font-size:12px;">Dismiss</button>
        `;
        document.body.appendChild(banner);
    }
})();

function saveToken() {
    const val = document.getElementById('token-input').value.trim();
    if (val) {
        localStorage.setItem('admin_token', val);
        document.querySelector('[id="token-input"]')?.closest('div')?.remove();
        alert('Token saved. You can now use the action buttons.');
    }
}

/* -----------------------------------------------------------------------
   Auto-dismiss flash messages
   ----------------------------------------------------------------------- */
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }, 5000);
});
