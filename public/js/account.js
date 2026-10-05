/* =========================================================
   TheSlowMatcha - Account Pages Script
   - Dropdown "..." pada kartu pesanan & alamat
   - Buka/tutup panel "Tambah Alamat Baru"
   - Toggle lihat password
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {
  initMoreDropdowns();
  initPanels();
  initPasswordToggles();
  initDeleteConfirm();
  initCopyButtons();
});

/**
 * Dropdown tiga titik.
 * Markup:
 * <div class="ac-more">
 *   <button class="ac-more__btn" data-more-toggle>...</button>
 *   <div class="ac-more__menu"> ... </div>
 * </div>
 */
function initMoreDropdowns() {
  var toggles = document.querySelectorAll('[data-more-toggle]');
  if (!toggles.length) return;

  toggles.forEach(function (btn) {
    btn.setAttribute('aria-expanded', 'false');

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var wrapper = btn.closest('.ac-more');
      var willOpen = !wrapper.classList.contains('is-open');

      closeAllDropdowns();

      if (willOpen) {
        wrapper.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // Klik di luar menutup dropdown
  document.addEventListener('click', closeAllDropdowns);

  // Escape juga menutup
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAllDropdowns();
  });
}

function closeAllDropdowns() {
  document.querySelectorAll('.ac-more.is-open').forEach(function (el) {
    el.classList.remove('is-open');
    var btn = el.querySelector('[data-more-toggle]');
    if (btn) btn.setAttribute('aria-expanded', 'false');
  });
}

/**
 * Panel yang bisa dibuka/tutup (mis. form "Tambah Alamat Baru").
 * Pemicu : <button data-panel-open="address-form">
 * Penutup: <button data-panel-close="address-form">
 * Target : <div data-panel="address-form">
 */
function initPanels() {
  document.querySelectorAll('[data-panel-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var panel = document.querySelector('[data-panel="' + btn.dataset.panelOpen + '"]');
      if (!panel) return;
      panel.classList.add('is-open');
      panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      var firstInput = panel.querySelector('input, select, textarea');
      if (firstInput) firstInput.focus({ preventScroll: true });
    });
  });

  document.querySelectorAll('[data-panel-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var panel = document.querySelector('[data-panel="' + btn.dataset.panelClose + '"]');
      if (panel) panel.classList.remove('is-open');
    });
  });
}

/**
 * Tombol mata untuk memperlihatkan isi input password.
 * <button data-password-toggle> di dalam .ac-input-group
 */
function initPasswordToggles() {
  document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var group = btn.closest('.ac-input-group');
      var input = group ? group.querySelector('input') : null;
      if (!input) return;

      var showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      btn.setAttribute('aria-label', showing ? 'Tampilkan password' : 'Sembunyikan password');
      btn.classList.toggle('is-showing', !showing);
    });
  });
}

/**
 * Konfirmasi sebelum submit form hapus (alamat, dll).
 * <form data-confirm="Hapus alamat ini?"> ... </form>
 */
function initDeleteConfirm() {
  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.dataset.confirm)) e.preventDefault();
    });
  });
}

/**
 * Tombol salin teks (mis. "Salin Alamat" di dropdown).
 * <button data-copy="teks yang disalin">
 */
function initCopyButtons() {
  document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var text = btn.dataset.copy || '';
      var original = btn.textContent;

      function done() {
        btn.textContent = 'Tersalin';
        setTimeout(function () { btn.textContent = original; }, 1500);
      }

      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(done);
      } else {
        // Fallback untuk browser lama / koneksi non-HTTPS
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); done(); } catch (e) { /* abaikan */ }
        document.body.removeChild(ta);
      }
    });
  });
}
