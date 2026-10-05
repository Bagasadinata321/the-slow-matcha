@extends('layouts.app')

@section('title', 'Pengaturan Profil — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
<div class="tsm-account">
  <div class="ac-shell">

    @include('public.account.sidebar', ['active' => 'profile'])

    <main class="ac-main">
      <nav class="ac-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('public.account.profile.edit') }}">Akun</a>
        <span>&rsaquo;</span>
        Pengaturan
      </nav>

      <div class="ac-head">
        <div>
          <h1>Pengaturan Profil</h1>
          <p>Perbarui informasi akun dan kata sandi Anda.</p>
        </div>
      </div>

      {{-- ===== Alert Error Validation Global ===== --}}
      @if ($errors->any())
        <div class="ac-card" style="padding: 12px 16px; background: #fbe4e2; color: #a8483a; margin-bottom: 16px; font-size: 13px; border-radius: var(--ac-radius);">
          <ul style="margin: 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- ===== Form 1: Informasi Profil ===== --}}
      @if(session('success_profile'))
        <div class="ac-card" style="padding: 12px 16px; background: var(--ac-sage); color: var(--ac-green-deep); margin-bottom: 16px; font-size: 13px; border-radius: var(--ac-radius);">
          {{ session('success_profile') }}
        </div>
      @endif

      <form action="{{ route('public.account.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="ac-card">
          <div class="ac-card__head">
            <p class="ac-card__title">Informasi Profil</p>
          </div>

          <div class="ac-profile-grid">
            <div class="ac-avatar-block">
              <div style="width:82px; height:82px; border-radius:50%; background:#ded9cc; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:bold; color:var(--ac-green-deep); margin:0 auto;">
                {{ strtoupper(substr($user->name ?? auth()->user()->name ?? 'U', 0, 1)) }}
              </div>
            </div>

            <div>
              <div class="ac-field">
                <label for="name">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? auth()->user()->name ?? '') }}" required>
              </div>

              <div class="ac-field">
                <label for="email">Email Address <span class="req">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? auth()->user()->email ?? '') }}" required>
              </div>

              <div class="ac-field">
                <label for="phone">Nomor WhatsApp / Telepon</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone ?? auth()->user()->phone ?? '') }}" placeholder="Contoh: 081234567890">
              </div>
            </div>
          </div>

          <div class="ac-card__actions">
            <button type="submit" class="ac-btn ac-btn--primary">Simpan Profil</button>
          </div>
        </div>
      </form>

      {{-- ===== Form 2: Ubah Password ===== --}}
      @if(session('success_password'))
        <div class="ac-card" style="padding: 12px 16px; background: var(--ac-sage); color: var(--ac-green-deep); margin-top: 16px; font-size: 13px; border-radius: var(--ac-radius);">
          {{ session('success_password') }}
        </div>
      @endif

      <form action="{{ route('public.account.profile.update-password') }}" method="POST" style="margin-top: 16px;">
        @csrf
        @method('PUT')

        <div class="ac-card">
          <div class="ac-card__head">
            <p class="ac-card__title">Ubah Password</p>
            <p class="ac-card__sub">Gunakan kombinasi yang kuat untuk keamanan akun Anda.</p>
          </div>

          <div style="padding: 18px 22px 0;">
            <div class="ac-form-grid">
              <div class="ac-field">
                <label for="current_password">Password Saat Ini <span class="req">*</span></label>
                <div class="ac-input-group">
                  <input type="password" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required>
                  <button type="button" class="ac-input-group__toggle" data-password-toggle aria-label="Tampilkan password">
                    👁️
                  </button>
                </div>
              </div>

              <div class="ac-field">
                <label for="password">Password Baru <span class="req">*</span></label>
                <div class="ac-input-group">
                  <input type="password" id="password" name="password" placeholder="Masukkan password baru" required>
                  <button type="button" class="ac-input-group__toggle" data-password-toggle aria-label="Tampilkan password">
                    👁️
                  </button>
                </div>
              </div>

              <div class="ac-field">
                <label for="password_confirmation">Konfirmasi Password Baru <span class="req">*</span></label>
                <div class="ac-input-group">
                  <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password baru" required>
                  <button type="button" class="ac-input-group__toggle" data-password-toggle aria-label="Tampilkan password">
                    👁️
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="ac-card__actions">
            <button type="submit" class="ac-btn ac-btn--primary">Ubah Password</button>
          </div>
        </div>
      </form>

    </main>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/account.js') }}"></script>
@endpush