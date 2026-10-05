@php $active = $active ?? ''; @endphp

<aside class="ac-sidebar">
  <div class="ac-user">
    <div class="ac-user__avatar" style="display:flex; align-items:center; justify-content:center; font-weight:bold; color:var(--ac-green-deep);">
      {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
    </div>
    <div>
      <p class="ac-user__name">{{ auth()->user()->name }}</p>
      <p class="ac-user__email">{{ auth()->user()->email }}</p>
    </div>
  </div>

  <nav class="ac-nav" aria-label="Menu akun">
    <a href="{{ route('public.account.profile.edit') }}" class="ac-nav__item {{ $active === 'profile' ? 'is-active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      Profil
    </a>

    <a href="{{ route('public.account.orders.index') }}" class="ac-nav__item {{ $active === 'orders' ? 'is-active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="17" rx="2"></rect>
        <path d="M8 2v4M16 2v4M7 11h10M7 15h6"></path>
      </svg>
      Riwayat Pesanan
    </a>

    <a href="{{ route('public.account.addresses.index') }}" class="ac-nav__item {{ $active === 'address' ? 'is-active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 21s7-5.4 7-11a7 7 0 10-14 0c0 5.6 7 11 7 11z"></path>
        <circle cx="12" cy="10" r="2.5"></circle>
      </svg>
      Buku Alamat
    </a>

    

    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="ac-nav__item">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path>
          <path d="M16 17l5-5-5-5M21 12H9"></path>
        </svg>
        Logout
      </button>
    </form>
  </nav>

  <div class="ac-sidebar__art">
    <p class="ac-sidebar__script">Good Matcha,<br>Better Days</p>
  </div>
</aside>