document.addEventListener('DOMContentLoaded', () => {
  // Stagger reveal animations
  document.querySelectorAll('[data-reveal-group]').forEach(group => {
    const items = group.querySelectorAll(':scope > .reveal');
    items.forEach((el, i) => { 
      el.style.transitionDelay = (i * 90) + 'ms'; 
    });
  });

  // Intersection Observer for animations
  const revealIO = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
        revealIO.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

  document.querySelectorAll('.reveal').forEach(el => revealIO.observe(el));
});