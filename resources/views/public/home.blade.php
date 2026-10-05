@extends('layouts.app')

@section('title', 'TheSlowMatcha — Pure Matcha. Real Ritual.')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
  <!-- HERO -->
   <section class="hero" id="top">
    <!-- =========================================================
         OPSI VIDEO BACKGROUND (Di-comment untuk penggunaan nanti)
         Atribut muted & playsinline WAJIB ada agar autoplay berfungsi.
         ========================================================= -->
    <!--
    <video autoplay muted loop playsinline id="heroVideo">
      <source src="{{ asset('videos/hero-matcha-pour.mp4') }}" type="video/mp4">
    </video>
    -->

    <!-- =========================================================
         OPSI GAMBAR STATIS (Digunakan sementara)
         ========================================================= -->
    <img src="{{ asset('assets/homepage/home-hero.png') }}" id="heroVideo" alt="TheSlowMatcha Hero Background">

    <!-- Layer Fallback / Overlay Gelap -->
    <div class="video-fallback" aria-hidden="true"></div>

    <div class="hero-content">
      <div class="hero-brand">TheSlowMatcha</div>
      <h1>Pure Matcha.<br>Real Ritual.</h1>
      <p>Mindfully crafted Japanese matcha for everyday moments.</p>
      <a href="{{ route('public.products.index') }}" class="btn">Shop Matcha</a>
    </div>
  </section>

  <!-- INTRO -->
  <section class="intro">
    <div class="intro-media reveal">
      @if(file_exists(public_path('assets/homepage/home-intro.png')))
        <img src="{{ asset('assets/homepage/home-intro.png') }}" alt="Alat & Daun Matcha Japanese Traditional">
      @else
        <span style="color: rgba(244,241,234,.5); font-size: 12px;">[ placeholder foto — alat &amp; daun matcha ]</span>
      @endif
    </div>
    <div class="intro-text reveal">
      <div class="eyebrow">Our Matcha</div>
      <h2>Pure. Intentional.<br>Everyday.</h2>
      <p>We source the finest Japanese matcha and craft it with respect for nature, tradition, and the moments that matter most.</p>
      <a href="{{ route('public.about') }}" class="link-arrow">Learn Our Philosophy →</a>
    </div>
  </section>

  <!-- COLLECTION -->
  <section class="collection">
    <div class="collection-label">Matcha Collection</div>
    <div class="collection-grid" data-reveal-group>
      @forelse($featuredProducts as $product)
        @php
          $firstVariant = $product->variants->first();
          $isPromo = $firstVariant ? $firstVariant->is_promo : false;
          $originalPrice = $firstVariant->price ?? 0;
          $finalPrice = $isPromo && $firstVariant->promo_price ? $firstVariant->promo_price : $originalPrice;
          $coverPath = $product->coverMedia ? asset('storage/' . $product->coverMedia->path) : null;
          $avgRating = $product->averageRating();
          $totalReviews = $product->totalReviews();
        @endphp
        <div class="product-card reveal">
          <div class="product-thumb">
            @if($isPromo)
              <span class="badge promo">Promo -{{ $firstVariant->discount_percent }}%</span>
            @elseif($loop->first)
              <span class="badge">Best Seller</span>
            @endif

            @if($coverPath)
              <img src="{{ $coverPath }}" alt="{{ $product->title }}">
            @else
              <div class="thumb-placeholder">{{ $product->title }}</div>
            @endif
          </div>

          <div class="product-info">
            <!-- Micro Social Proof (Rating Dinamis) -->
            <div class="product-rating">
              <span class="stars" style="color: #f59e0b;">
                @if($totalReviews > 0)
                  @for($i = 1; $i <= 5; $i++)
                    {{ $i <= round($avgRating) ? '★' : '☆' }}
                  @endfor
                @else
                  ☆☆☆☆☆
                @endif
              </span>
              <span class="rating-count">
                @if($totalReviews > 0)
                  ({{ number_format($avgRating, 1) }})
                @else
                  (Belum ada ulasan)
                @endif
              </span>
            </div>

            <div class="product-name">{{ $product->title }}</div>
            
            <!-- Price Display with Strikethrough for Promo -->
            <div class="product-price-wrapper">
              <span class="product-price">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
              @if($isPromo && $firstVariant->promo_price)
                <span class="product-price-old">Rp {{ number_format($originalPrice, 0, ',', '.') }}</span>
              @endif
            </div>

            <!-- Action Links -->
            <div class="product-actions">
              <a href="{{ route('public.products.show', $product->slug) }}" class="view-link">View Details →</a>
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; text-align: center; color: #6d7063; padding: 40px 0;">
          Belum ada produk yang tersedia.
        </div>
      @endforelse
    </div>
  </section>

  <!-- FEATURES -->
  <section class="features" data-reveal-group>
    <div class="feature reveal">
      <div class="feature-icon">🍃</div>
      <h4>100% Ceremonial Grade</h4>
      <p>Single-origin Japanese matcha leaves carefully harvested by hand.</p>
    </div>
    <div class="feature reveal">
      <div class="feature-icon">⚙</div>
      <h4>Traditional Stone Ground</h4>
      <p>Slowly ground using granite mills to preserve natural vibrant color & flavor.</p>
    </div>
    <div class="feature reveal">
      <div class="feature-icon">✦</div>
      <h4>Umami & Zero Bitterness</h4>
      <p>Rich creamy mouthfeel with a naturally sweet, soothing finish.</p>
    </div>
    <div class="feature reveal">
      <div class="feature-icon">☕</div>
      <h4>Sustained Energy</h4>
      <p>Rich in L-Theanine for calm focus without caffeine crash.</p>
    </div>
  </section>

  <!-- TESTIMONIALS & SOCIAL PROOF WITH SLIDER -->
  <section class="testimonials">
    <div class="collection-label">Loved by Tea Enthusiasts</div>
    <h2 class="testimonials-title">The Slow Experience</h2>
    
    <div class="testimonial-slider-wrapper">
      <button class="slider-btn prev" id="reviewPrev" aria-label="Previous Review">‹</button>
      
      <div class="testimonials-grid" id="testimonialTrack" data-reveal-group>
  @foreach($latestReviews as $review)
    <div class="testimonial-card reveal">
      <div class="stars" style="color: #f59e0b;">
        @for($i = 1; $i <= 5; $i++)
          {{ $i <= $review->rating ? '★' : '☆' }}
        @endfor
      </div>
      <p class="quote">"{{ $review->comment }}"</p>
      <div class="author">
        — {{ $review->user->name ?? 'Pembeli Verified' }}, 
        <small>Membeli <em>{{ $review->product->title ?? 'Matcha' }}</em></small>
      </div>
    </div>
  @endforeach
</div>

      <button class="slider-btn next" id="reviewNext" aria-label="Next Review">›</button>
    </div>

    {{-- Slider Dots --}}
    <div class="slider-dots" id="testimonialDots"></div>
  </section>

  <!-- SEO & BRAND NARRATIVE SECTION -->
  <section class="seo-narrative">
    <div class="seo-container">
      <div class="seo-header">
        <div class="eyebrow">The Artisan Matcha</div>
        <h2>Crafted for Mindfulness &amp; Authentic Japanese Tea Rituals</h2>
      </div>
      <div class="seo-content">
        <p>
          <strong>TheSlowMatcha</strong> menghadirkan keutamaan matcha Jepang kualitas premium (<em>Ceremonial Grade Matcha</em>) yang dipetik dari perkebunan pilihan di Uji, Kyoto. Setiap lembar daun teh hijau digiling secara perlahan menggunakan batu granit tradisional untuk menjaga kesegaran warna, aroma otentik, serta profil rasa umami yang kaya tanpa rasa pahit yang tajam.
        </p>
        <p>
          Sebagai alternatif kopi bebas efek <em>caffeine crash</em>, produk matcha kami kaya akan antioksidan, L-Theanine, dan nutrisi alami yang memberikan energi fokus berkelanjutan sepanjang hari. Baik untuk seduhan murni ala tradisi Jepang maupun dikreasikan sebagai <em>Matcha Latte</em> yang creamy, temukan ketenangan ritual harian Anda bersama koleksi matcha otentik dari TheSlowMatcha.
        </p>
      </div>
    </div>
  </section>

  <!-- FAQ SECTION -->
  <section class="faq">
    <div class="collection-label">Got Questions?</div>
    <h2 class="faq-title">Frequently Asked Questions</h2>

    <div class="faq-container">
      <details class="faq-item" open>
        <summary class="faq-question">
          <span>Apakah TheSlowMatcha menggunakan 100% Ceremonial Grade?</span>
          <span class="faq-icon">+</span>
        </summary>
        <div class="faq-answer">
          <p>Ya, seluruh matcha kami diimpor langsung dari perkebunan pilihan di Uji, Kyoto, Jepang. Kami hanya menggunakan daun teh muda petikan pertama (first-harvest) yang digiling menggunakan batu granit tradisional untuk menghasilkan kualitas Ceremonial Grade murni.</p>
        </div>
      </details>

      <details class="faq-item">
        <summary class="faq-question">
          <span>Apakah rasa matchanya terasa pahit atau kelat?</span>
          <span class="faq-icon">+</span>
        </summary>
        <div class="faq-answer">
          <p>Tidak. Ceremonial Grade berkualitas tinggi memiliki profil rasa umami yang kaya, rasa manis alami yang lembut, dan *texture* yang sangat halus tanpa rasa pahit yang tajam (zero harsh bitterness).</p>
        </div>
      </details>

      <details class="faq-item">
        <summary class="faq-question">
          <span>Bagaimana cara menyimpan matcha agar tetap segar?</span>
          <span class="faq-icon">+</span>
        </summary>
        <div class="faq-answer">
          <p>Simpan kemasan kaleng matcha yang sudah dibuka di dalam lemari es (kulkas) dengan kedap udara. Hindari paparan sinar matahari langsung dan udara terbuka agar warna hijau cerah serta aromanya tetap terjaga hingga 2-3 bulan.</p>
        </div>
      </details>

      <details class="faq-item">
        <summary class="faq-question">
          <span>Apakah aman dikonsumsi setiap hari untuk penderita asam lambung?</span>
          <span class="faq-icon">+</span>
        </summary>
        <div class="faq-answer">
          <p>Matcha kaya akan L-Theanine dan memiliki sifat alkaline yang jauh lebih ramah di lambung dibandingkan kopi. Kandungan kafein pada matcha dilepaskan secara perlahan, sehingga tidak memicu debaran jantung atau lonjakan asam lambung.</p>
        </div>
      </details>
    </div>
  </section>
@endsection

@push('scripts')
  <script src="{{ asset('js/home.js') }}"></script>
@endpush