<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'TheSlowMatcha — Pure Matcha. Real Ritual.')</title>

  <!-- Global Style -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Page Specific Style -->
  @stack('styles')
</head>
<body>

  <!-- GLOBAL NAV -->
  <header class="nav {{ !request()->is('/') ? 'scrolled' : '' }} @yield('nav_class')" id="mainNav">
    <a href="{{ url('/') }}" class="logo" style="text-decoration: none; color: inherit;">TheSlowMatcha</a>
    
    <!-- Desktop Nav Links -->
    <nav class="nav-links">
      <a href="{{ url('/') }}">Home</a>
      <a href="{{ route('public.products.index') }}">Shop</a>
      <a href="{{ route('public.about') }}">About</a>
    </nav>

    <div class="nav-right">
      <a href="{{ route('public.cart.index') }}" class="cart-link" aria-label="Cart">
        <svg viewBox="0 0 24 24"><path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="9.5" cy="20.5" r="1.3" fill="currentColor" stroke="none"/><circle cx="17.5" cy="20.5" r="1.3" fill="currentColor" stroke="none"/></svg>
        <span class="cart-badge">0</span>
      </a>

      {{-- Dynamic Login / Account Section --}}
      @auth
        <div class="user-menu-dropdown" style="position: relative; display: inline-block;">
          <a href="{{ route('public.account.profile.edit') }}" class="account-link" style="font-weight: 500;">
            Hi, {{ Str::words(Auth::user()->name, 1, '') }}
          </a>
        </div>
      @else
        <a href="{{ route('login') }}" class="nav-login-btn">Login</a>
      @endauth

      <!-- Mobile Hamburger Toggle -->
      <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <!-- Mobile Drawer Menu Overlay -->
    <div class="mobile-drawer" id="mobileDrawer">
      <div class="mobile-drawer-content">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('public.products.index') }}">Shop</a>
        <a href="{{ route('public.about') }}">About</a>
        <hr style="border: none; border-top: 1px solid var(--line); margin: 12px 0;">
        @auth
          <a href="{{ route('public.account.orders.index') }}">My Orders</a>
          <a href="{{ route('public.account.addresses.index') }}">Buku Alamat</a>
          <a href="{{ route('public.account.profile.edit') }}">Pengaturan Profil</a>
          <form action="{{ route('logout') }}" method="POST" style="margin-top: 8px;">
            @csrf
            <button type="submit" style="background: none; border: none; padding: 0; color: #a83a29; font: inherit; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 1.5px; font-weight: 600;">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}">Login</a>
          <a href="{{ route('register') }}">Register</a>
        @endauth
      </div>
    </div>
  </header>

  <!-- DYNAMIC CONTENT -->
  <main>
    @yield('content')
  </main>

  <!-- GLOBAL FOOTER -->
  <footer>
    <div class="footer-grid">
      <div class="footer-brand-col">
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
        <a href="{{ route('public.orders.track') }}">Lacak Pesanan</a>
        <a href="#">FAQ</a>
        <a href="#">Contact</a>
      </div>
      <div class="footer-col">
        <h5>Account</h5>
        @auth
          <a href="{{ route('public.account.orders.index') }}">My Orders</a>
          <a href="{{ route('public.account.addresses.index') }}">Buku Alamat</a>
          <a href="{{ route('public.account.profile.edit') }}">Pengaturan Profil</a>
          <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" style="background: none; border: none; padding: 0; color: inherit; font: inherit; cursor: pointer;">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}">Login</a>
          <a href="{{ route('register') }}">Register</a>
        @endauth
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
      <div class="footer-bottom-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </footer>

  <!-- Mobile Menu Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleBtn = document.getElementById('mobileNavToggle');
      const drawer = document.getElementById('mobileDrawer');
      const header = id = document.getElementById('mainNav');

      if (toggleBtn && drawer) {
        toggleBtn.addEventListener('click', function() {
          toggleBtn.classList.toggle('active');
          drawer.classList.toggle('active');
          document.body.classList.toggle('no-scroll');
        });
      }
    });
  </script>

  <!-- Global Script -->
  <script src="{{ asset('js/app.js') }}"></script>

  <!-- Page Specific Script -->
  @stack('scripts')
</body>
</html>