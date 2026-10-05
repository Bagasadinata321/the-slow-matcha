/* =========================================================
   TheSlowMatcha - About Page Script
   Satu efek saja: section muncul halus saat masuk viewport.
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {
  initReveal();
});

function initReveal() {
  var targets = document.querySelectorAll('.a-reveal');
  if (!targets.length) return;

  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Tanpa IntersectionObserver atau kalau user minta minim animasi:
  // langsung tampilkan semuanya.
  if (prefersReduced || !('IntersectionObserver' in window)) {
    targets.forEach(function (el) { el.classList.add('is-visible'); });
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -60px 0px'
  });

  targets.forEach(function (el) { observer.observe(el); });
}
