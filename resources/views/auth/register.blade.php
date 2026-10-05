@extends('layouts.auth')

@section('title', 'Daftar Akun Baru')

@section('hero')
    <span class="tsm-auth-subtitle">DAFTAR AKUN BARU</span>
    <h1 class="tsm-auth-title">Bergabung dengan TheSlowMatcha</h1>
    <p class="tsm-auth-desc">Nikmati pengalaman berbelanja matcha premium dan dapatkan berbagai keuntungan eksklusif.</p>
    <img src="{{ asset('images/auth-matcha-landscape.png') }}" alt="Matcha Plantation" class="tsm-auth-hero-img">
@endsection

@section('card')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        @if(request()->has('redirect_to'))
            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
        @endif

        <!-- Nama Lengkap -->
        <div class="tsm-form-group">
            <label for="name" class="tsm-form-label">Nama Lengkap</label>
            <input id="name" type="text" name="name" class="tsm-form-input" 
                   value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required autofocus autocomplete="name">
            @error('name')
                <span class="tsm-error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="tsm-form-group">
            <label for="email" class="tsm-form-label">Email Address</label>
            <input id="email" type="email" name="email" class="tsm-form-input" 
                   value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="username">
            @error('email')
                <span class="tsm-error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Nomor WhatsApp / HP -->
        <div class="tsm-form-group">
            <label for="phone" class="tsm-form-label">Nomor WhatsApp / HP</label>
            <input id="phone" type="text" name="phone" class="tsm-form-input" 
                   value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required autocomplete="tel">
            @error('phone')
                <span class="tsm-error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="tsm-form-group">
            <label for="password" class="tsm-form-label">Password</label>
            <div class="tsm-password-wrapper">
                <input id="password" type="password" name="password" class="tsm-form-input" 
                       placeholder="Minimal 8 karakter" required autocomplete="new-password">
                <button type="button" class="tsm-password-toggle" data-password-toggle aria-label="Tampilkan password">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            @error('password')
                <span class="tsm-error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Konfirmasi Password -->
        <div class="tsm-form-group">
            <label for="password_confirmation" class="tsm-form-label">Konfirmasi Password</label>
            <div class="tsm-password-wrapper">
                <input id="password_confirmation" type="password" name="password_confirmation" class="tsm-form-input" 
                       placeholder="Ulangi password" required autocomplete="new-password">
                <button type="button" class="tsm-password-toggle" data-password-toggle aria-label="Tampilkan password">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <span class="tsm-error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="tsm-btn tsm-btn-primary" style="margin-top: 8px;">
            Register &rarr;
        </button>

        <!-- Footer Link -->
        <div class="tsm-card-footer">
            Sudah punya akun? <a href="{{ route('login') }}" class="tsm-link">Login</a>
        </div>
    </form>
@endsection