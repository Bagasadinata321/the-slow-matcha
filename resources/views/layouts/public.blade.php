<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'TheSlowMatcha — Pure Matcha. Real Ritual.')</title>

<!-- Extra styles specific to page -->
@stack('styles')

<style>
  :root{
    --ink:#1c1f19;
    --cream:#f4f1ea;
    --cream-2:#ece7dc;
    --olive-dark:#22261d;
    --olive:#3f4a34;
    --olive-soft:#6b7a55;
    --gold:#c9a24b;
    --promo:#a83a29;
    --line: rgba(28,31,25,0.12);
    --serif: 'Georgia', 'Iowan Old Style', 'Times New Roman', serif;
    --sans: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
    --mono: 'SF Mono', 'Roboto Mono', 'Courier New', monospace;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html{scroll-behavior:smooth;}
  body{
    font-family:var(--sans);
    color:var(--ink);
    background:var(--cream);
    -webkit-font-smoothing:antialiased;
  }
  a{color:inherit; text-decoration:none;}
  button{font-family:inherit;}
  .wrap{max-width:1400px; margin:0 auto; padding:0 48px;}

  /* REVEAL ANIMATION */
  .reveal{
    opacity:0; transform:translateY(30px);
    transition:opacity .8s cubic-bezier(.2,.7,.2,1), transform .8s cubic-bezier(.2,.7,.2,1);
  }
  .reveal.in-view{opacity:1; transform:translateY(0);}

  /* NAV BAR */
  header.nav{
    position:fixed; top:0; left:0; right:0; z-index:100;
    padding:24px 48px;
    display:flex; align-items:center; justify-content:space-between;
    color:#f4f1ea;
    background:transparent;
    transition:background .35s ease, color .35s ease, box-shadow .35s ease;
  }
  header.nav.scrolled, header.nav.nav-solid{
    background:rgba(255,255,255,.97);
    color:var(--ink);
    box-shadow:0 1px 0 var(--line);
    padding:16px 48px;
  }
  header.nav.nav-solid .logo, header.nav.scrolled .logo{opacity:1;}
  .logo{font-family:var(--serif); font-size:19px; letter-spacing:.5px;}
  .nav-links{display:flex; gap:36px; font-size:12px; letter-spacing:1.5px; text-transform:uppercase;}
  .nav-links a{position:relative; opacity:.9; padding-bottom:4px; transition:opacity .2s;}
  .nav-links a:hover{opacity:1;}
  .nav-right{display:flex; align-items:center; gap:26px; font-size:12px; letter-spacing:1.5px; text-transform:uppercase;}
  .cart-link{position:relative; display:flex; align-items:center;}
  .cart-link svg{width:19px; height:19px; stroke:currentColor; fill:none; stroke-width:1.6;}
  .cart-badge{
    position:absolute; top:-6px; right:-7px;
    min-width:15px; height:15px; padding:0 3px; border-radius:50%;
    background:var(--promo); color:#fff;
    font-family:var(--sans); font-size:9px; font-weight:700;
    display:flex; align-items:center; justify-content:center;
  }

  /* FOOTER */
  footer{
    background:var(--olive-dark);
    color:rgba(244,241,234,.7);
    padding:56px 48px 28px;
  }
  .footer-grid{
    display:grid; grid-template-columns:2fr 1fr 1fr 1fr 1fr;
    gap:32px; max-width:1300px; margin:0 auto 40px;
  }
  .footer-logo{font-family:var(--serif); color:#f4f1ea; font-size:18px; margin-bottom:10px;}
  .footer-grid p{font-size:12px; line-height:1.7; max-width:200px;}
  .footer-col h5{font-size:11px; letter-spacing:1.5px; text-transform:uppercase; color:#f4f1ea; margin-bottom:16px;}
  .footer-col a{display:block; font-size:12px; margin-bottom:10px; opacity:.75;}
  .footer-col a:hover{opacity:1;}
  .footer-bottom{
    max-width:1300px; margin:0 auto; padding-top:24px;
    border-top:1px solid rgba(244,241,234,.12);
    display:flex; justify-content:space-between; font-size:11px; opacity:.6;
  }
</style>
</head>
<body>

  <!-- GLOBAL NAV -->
  <header class="nav @yield('nav_class')" id="mainNav">
    <div class="logo">TheSlowMatcha</div>
    <nav class="nav-links">
      <a href="{{ route('public.products.index') }}">Shop</a>
      <a href="{{ route('public.about') }}">About</a>
    </nav>
    <div class="nav-right">
      <a href="#cart" class="cart-link" aria-label="Cart">
        <svg viewBox="0 0 24 24"><path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="9.5" cy="20.5" r="1.3" fill="currentColor" stroke="none"/><circle cx="17.5" cy="20.5" r="1.3" fill="currentColor" stroke="none"/></svg>
        <span class="cart-badge">0</span>
      </a>
      <a href="/login">Login</a>
    </div>
  </header>

  <!-- DYNAMIC CONTENT HERE -->
  <main>
    @yield('content')
  </main>

  <!-- GLOBAL FOOTER -->
  <footer>
    <div class="footer-grid">
      <div>
        <div class="footer-logo">TheSlowMatcha</div>
        <p>Slow down. With matcha, live fully.</p>
      </div>
      <div class="footer-col">
        <h5>Shop</h5>
        <a href="{{ route('public.products.index') }}">All Products</a>
        <a href="{{ route('public.products.index', ['type' => 'matcha']) }}">Matcha</a>
        <a href="{{ route('public.products.index', ['type' => 'tool']) }}">Matcha Set</a>
      </div>
      <div class="footer-col">
        <h5>Help</h5>
        <a href="#">FAQ</a>
        <a href="#">Shipping</a>
        <a href="#">Contact</a>
      </div>
      <div class="footer-col">
        <h5>Account</h5>
        <a href="/login">Login</a>
        <a href="#">My Orders</a>
      </div>
      <div class="footer-col">
        <h5>Connect</h5>
        <a href="#">Instagram</a>
        <a href="#">TikTok</a>
        <a href="#">WhatsApp</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 TheSlowMatcha</span>
      <span style="display:flex; gap:20px;">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </span>
    </div>
  </footer>

  <script>
    document.querySelectorAll('[data-reveal-group]').forEach(group => {
      const items = group.querySelectorAll(':scope > .reveal');
      items.forEach((el, i) => { el.style.transitionDelay = (i * 90) + 'ms'; });
    });

    const revealIO = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          revealIO.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

    document.querySelectorAll('.reveal').forEach(el => revealIO.observe(el));
  </script>

  @stack('scripts')
</body>
</html>