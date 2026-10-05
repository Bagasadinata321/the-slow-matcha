/* =========================================================
   TheSlowMatcha - Auth Interactive Script
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {
  initPasswordToggles();
  initFormSubmitState();
});

/**
 * Toggle Password Visibility (Mata / Eye Icon)
 */
function initPasswordToggles() {
  const toggles = document.querySelectorAll('[data-password-toggle]');

  toggles.forEach(function (button) {
    button.addEventListener('click', function () {
      const wrapper = button.closest('.tsm-password-wrapper');
      const input = wrapper ? wrapper.querySelector('input') : null;

      if (!input) return;

      const isPassword = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPassword ? 'text' : 'password');

      // Update SVG Icon State
      button.innerHTML = isPassword
        ? `<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`
        : `<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
      
      button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
    });
  });
}

/**
 * Prevent Double Form Submission
 */
function initFormSubmitState() {
  const forms = document.querySelectorAll('form');

  forms.forEach(function (form) {
    form.addEventListener('submit', function () {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn && !submitBtn.disabled) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
        submitBtn.style.cursor = 'wait';
      }
    });
  });
}