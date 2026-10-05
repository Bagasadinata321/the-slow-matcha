document.addEventListener('DOMContentLoaded', () => {
  // 1. Navigation Scroll Effect
  const nav = document.getElementById('mainNav');
  const hero = document.getElementById('top');

  if (nav) {
    function handleNavScroll() {
      if (!hero) {
        nav.classList.add('scrolled');
      } else {
        const threshold = hero.offsetHeight - 80;
        if (window.scrollY > threshold) {
          nav.classList.add('scrolled');
        } else {
          nav.classList.remove('scrolled');
        }
      }
    }

    window.addEventListener('scroll', handleNavScroll);
    handleNavScroll();
  }

  // 2. Testimonial Review Slider Logic
  const track = document.getElementById('testimonialTrack');
  const prevBtn = document.getElementById('reviewPrev');
  const nextBtn = document.getElementById('reviewNext');
  const dotsContainer = document.getElementById('testimonialDots');

  if (track) {
    const cards = track.querySelectorAll('.testimonial-card');
    
    if (cards.length > 0) {
      // Hitung apakah isi grid melampaui kontainer (butuh slider)
      function updateSliderControls() {
        const isOverflowing = track.scrollWidth > track.clientWidth + 10;
        
        if (isOverflowing) {
          if (prevBtn) prevBtn.style.display = 'flex';
          if (nextBtn) nextBtn.style.display = 'flex';
          renderDots();
        } else {
          if (prevBtn) prevBtn.style.display = 'none';
          if (nextBtn) nextBtn.style.display = 'none';
          if (dotsContainer) dotsContainer.innerHTML = '';
        }
      }

      function renderDots() {
        if (!dotsContainer) return;
        dotsContainer.innerHTML = '';
        
        const cardWidth = cards[0].offsetWidth + 28; // lebar kartu + gap
        const totalPages = Math.ceil((track.scrollWidth - track.clientWidth) / cardWidth) + 1;

        for (let i = 0; i < totalPages; i++) {
          const dot = document.createElement('div');
          dot.classList.add('dot');
          if (i === 0) dot.classList.add('active');
          
          dot.addEventListener('click', () => {
            track.scrollTo({
              left: i * cardWidth,
              behavior: 'smooth'
            });
          });
          
          dotsContainer.appendChild(dot);
        }
      }

      function syncActiveDot() {
        if (!dotsContainer) return;
        const cardWidth = cards[0].offsetWidth + 28;
        const pageIndex = Math.round(track.scrollLeft / cardWidth);
        
        const dots = dotsContainer.querySelectorAll('.dot');
        dots.forEach((dot, idx) => {
          dot.classList.toggle('active', idx === pageIndex);
        });
      }

      // Action Navigasi Tombol
      if (prevBtn) {
        prevBtn.addEventListener('click', () => {
          const cardWidth = cards[0].offsetWidth + 28;
          track.scrollBy({ left: -cardWidth, behavior: 'smooth' });
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          const cardWidth = cards[0].offsetWidth + 28;
          track.scrollBy({ left: cardWidth, behavior: 'smooth' });
        });
      }

      // Synchronize Dots saat scroll
      track.addEventListener('scroll', syncActiveDot);
      window.addEventListener('resize', updateSliderControls);

      // Inisialisasi awal
      updateSliderControls();
    }
  }
});