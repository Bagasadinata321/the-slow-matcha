@extends('layouts.app')

@section('title', 'About Us — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endpush

@section('content')
<div class="tsm-about">

  {{-- ============ Breadcrumb ============ --}}
  <div class="tsm-about__inner">
    <nav class="a-breadcrumb" aria-label="Breadcrumb">
      <a href="{{ url('/') }}">Home</a>
      <span>/</span>
      <span class="is-current">About</span>
    </nav>
  </div>

  {{-- ============ Hero ============ --}}
  <section class="tsm-about__inner a-hero">
    <div class="a-hero__copy a-reveal">
      <p class="a-eyebrow">Our Philosophy</p>
      <h1 class="a-title a-title--xl">More Than<br>Just Matcha</h1>
      <p class="a-body" style="margin-top: 26px;">
        TheSlowMatcha hadir dari keyakinan bahwa matcha bukan sekadar minuman,
        tetapi sebuah ritual untuk hidup yang lebih mindful, seimbang, dan bermakna.
      </p>
      <hr class="a-rule">
      <p class="a-script">Pure Matcha,<br>More Meaningful Moments.</p>
    </div>

    <!-- GAMBAR 1: Hero Image -->
    <figure class="a-hero__media a-reveal {{ !file_exists(public_path('assets/about/hero-about.png')) ? 'a-img-placeholder' : '' }}" 
            data-placeholder="Hero Image (800x1000px)" style="margin: 0; min-height: 420px;">
      @if(file_exists(public_path('assets/about/hero-about.png')))
        <img src="{{ asset('assets/about/hero-about.png') }}" alt="Chasen & Matcha">
      @endif
      <span class="a-vertical-kanji" aria-hidden="true">良い一日を</span>
    </figure>
  </section>

  {{-- ============ Our Story ============ --}}
  <section class="a-story">
    <div class="tsm-about__inner a-story__grid">
      <div class="a-story__text a-reveal">
        <p class="a-eyebrow">Our Story</p>
        <h2 class="a-title a-title--lg">Dari Uji,<br>Hingga ke Dunia</h2>

        <p class="a-body" style="margin-top: 26px;">
          Perjalanan TheSlowMatcha dimulai dari Uji, Kyoto — sebuah daerah yang telah
          berabad-abad menjadi pusat budaya teh hijau terbaik di Jepang. Di sinilah kami
          belajar bahwa kualitas sejati lahir dari kesabaran, ketelitian, dan rasa hormat
          terhadap alam.
        </p>
        <p class="a-body">
          Kami membawa filosofi tersebut ke dalam setiap cangkir, menghadirkan matcha
          autentik Jepang yang dipilih dengan cermat, diolah secara tradisional, dan
          disajikan untuk menemani momen-momen berharga dalam hidup Anda.
        </p>

        <p class="a-story__signature">Uji, Kyoto</p>
        <p class="a-story__signature-label">The Birthplace of Our Journey</p>
      </div>

      <div class="a-collage a-reveal">
        <!-- GAMBAR 2: Uji Temple -->
        <div class="a-collage__main {{ !file_exists(public_path('assets/about/uji-temple.png')) ? 'a-img-placeholder' : '' }}" 
             data-placeholder="Uji Temple (800x600px)" style="aspect-ratio: 4/3;">
          @if(file_exists(public_path('assets/about/uji-temple.png')))
            <img src="{{ asset('assets/about/uji-temple.png') }}" alt="Uji Temple">
          @endif
        </div>

        <div class="a-collage__tag">
          <span class="a-collage__tag-kanji" aria-hidden="true">宇治</span>
          <span class="a-collage__tag-label">Uji, Kyoto<br>Japan</span>
        </div>

        <!-- GAMBAR 3: Whisking Matcha -->
        <div class="a-collage__overlap {{ !file_exists(public_path('assets/about/whisking.png')) ? 'a-img-placeholder' : '' }}" 
             data-placeholder="Whisking (600x480px)" style="aspect-ratio: 5/4;">
          @if(file_exists(public_path('assets/about/whisking.png')))
            <img src="{{ asset('assets/about/whisking.png') }}" alt="Whisking Matcha">
          @endif
        </div>

        <blockquote class="a-quote">
          “Setiap daun teh menyimpan cerita tentang tanah, cuaca, dan waktu.”
        </blockquote>
      </div>
    </div>
  </section>

  {{-- ============ Our Values ============ --}}
  <section class="a-values">
    <div class="tsm-about__inner a-values__grid">
      <div class="a-values__heading a-reveal">
        <p class="a-eyebrow">Our Values</p>
        <h2 class="a-title a-title--lg">Lebih dari Sekadar<br>Rasa, Ini Tentang Nilai</h2>
      </div>

      <article class="a-value a-reveal">
        <svg class="a-value__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M4 20c0-8 6-14 16-15 0 10-5 15-11 15H4z"></path>
          <path d="M4 20c4-4 7-6 11-7.5"></path>
        </svg>
        <h3>Kualitas Premium</h3>
        <p>Hanya matcha pilihan terbaik dari Uji, Kyoto yang kami gunakan.</p>
      </article>

      <article class="a-value a-reveal">
        <svg class="a-value__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M2 19l7-11 4.5 7 2.5-3.5L22 19H2z"></path>
        </svg>
        <h3>Proses Tradisional</h3>
        <p>Diproses dengan metode tradisional untuk menjaga rasa dan nutrisi alami.</p>
      </article>

      <article class="a-value a-reveal">
        <svg class="a-value__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 20s-7-4.6-7-9.2A4 4 0 0112 8.6 4 4 0 0119 10.8c0 4.6-7 9.2-7 9.2z"></path>
        </svg>
        <h3>Ramah Lingkungan</h3>
        <p>Kami berkomitmen pada praktik berkelanjutan dan mendukung petani lokal.</p>
      </article>

      <article class="a-value a-reveal">
        <svg class="a-value__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 3c.8 4.4 3.8 7.4 8.2 8.2-4.4.8-7.4 3.8-8.2 8.2-.8-4.4-3.8-7.4-8.2-8.2C8.2 10.4 11.2 7.4 12 3z"></path>
        </svg>
        <h3>Kehidupan Lebih Baik</h3>
        <p>Karena kami percaya, matcha bisa menjadi bagian dari hidup yang lebih sehat dan seimbang.</p>
      </article>
    </div>
  </section>

  {{-- ============ Mini Guide ============ --}}
  <section class="a-guide">
    <!-- GAMBAR 4: Matcha Set -->
    <figure class="a-guide__photo {{ !file_exists(public_path('assets/about/matcha-set.png')) ? 'a-img-placeholder' : '' }}" 
            data-placeholder="Matcha Set (700x900px)" style="margin: 0; min-height: 420px;">
      @if(file_exists(public_path('assets/about/matcha-set.png')))
        <img src="{{ asset('assets/about/matcha-set.png') }}" alt="Matcha Set">
      @endif
      <span class="a-vertical-kanji" aria-hidden="true">抹茶の時間</span>
    </figure>

    <div class="a-guide__body">
      <div class="a-reveal">
        <p class="a-eyebrow">Mini Guide</p>
        <h2 class="a-title a-title--lg a-guide__title">Panduan Menyeduh<br>Matcha yang Sempurna</h2>
        <p class="a-body">
          Menikmati matcha bukan hanya soal rasa, tetapi juga ritual. Berikut langkah
          sederhana untuk mendapatkan pengalaman terbaik dari setiap cangkir matcha.
        </p>

        <ol class="a-steps">
          <li class="a-step">
            <span class="a-step__num">1</span>
            <div>
              <h4>Siapkan alat dan bahan</h4>
              <p>Matcha, air panas (70–80&deg;C), chasen, dan mangkuk.</p>
            </div>
          </li>
          <li class="a-step">
            <span class="a-step__num">2</span>
            <div>
              <h4>Ayak matcha</h4>
              <p>Untuk hasil yang lebih halus dan lembut.</p>
            </div>
          </li>
          <li class="a-step">
            <span class="a-step__num">3</span>
            <div>
              <h4>Seduh dengan air hangat</h4>
              <p>Aduk dengan gerakan “M” atau “W” hingga berbusa.</p>
            </div>
          </li>
          <li class="a-step">
            <span class="a-step__num">4</span>
            <div>
              <h4>Nikmati</h4>
              <p>Rasakan aroma, tekstur, dan ketenangan di setiap tegukan.</p>
            </div>
          </li>
        </ol>
      </div>

      <aside class="a-guide__side a-reveal">
        <!-- GAMBAR 5: Holding Bowl -->
        <div class="{{ !file_exists(public_path('assets/about/holding-bowl.png')) ? 'a-img-placeholder' : '' }}" 
             data-placeholder="Holding Bowl (400x300px)" style="aspect-ratio: 4/3;">
          @if(file_exists(public_path('assets/about/holding-bowl.png')))
            <img src="{{ asset('assets/about/holding-bowl.png') }}" alt="Holding Bowl">
          @endif
        </div>

        <!-- GAMBAR 6: Tea Field -->
        <div class="{{ !file_exists(public_path('assets/about/tea-field.png')) ? 'a-img-placeholder' : '' }}" 
             data-placeholder="Tea Field (400x300px)" style="aspect-ratio: 4/3;">
          @if(file_exists(public_path('assets/about/tea-field.png')))
            <img src="{{ asset('assets/about/tea-field.png') }}" alt="Tea Field">
          @endif
        </div>
        <p class="a-guide__note">A moment of calm in every cup.</p>
      </aside>
    </div>
  </section>

  {{-- ============ CTA ============ --}}
  <section class="a-cta {{ !file_exists(public_path('images/about/cta-product.jpg')) ? 'a-img-placeholder' : '' }}" 
           data-placeholder="CTA Banner (1400x500px)" style="min-height: 320px;">
    @if(file_exists(public_path('images/about/cta-product.jpg')))
      <img class="a-cta__bg" src="{{ asset('images/about/cta-product.jpg') }}" alt="" aria-hidden="true">
    @endif
    <div class="tsm-about__inner a-cta__inner">
      <p class="a-eyebrow">Jelajahi Koleksi Kami</p>
      <h2 class="a-title a-title--lg">Rasakan Kualitas Matcha Terbaik dari Jepang</h2>
      <p>
        Dari single origin hingga ceremonial grade, temukan matcha yang sesuai
        dengan perjalanan rasa Anda.
      </p>
      <a href="{{ route('public.products.index') }}" class="a-btn">Lihat Katalog Produk &rarr;</a>
    </div>
  </section>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/about.js') }}"></script>
@endpush