@extends('layouts.auth')

@section('title', 'Masuk')

@section('hero')
    <span class="tsm-auth-subtitle">MASUK KE AKUN ANDA</span>
    <h1 class="tsm-auth-title">Selamat Datang Kembali</h1>
    <p class="tsm-auth-desc">Lanjutkan perjalanan matcha Anda bersama TheSlowMatcha.</p>
    <img src="{{ asset('images/auth-matcha-hero.png') }}" alt="Matcha Set" class="tsm-auth-hero-img">
@endsection

@section('card')
    @if (session('status'))
        <div style="font-size: 12px; color: var(--tsm-matcha-deep); background: rgba(44, 56, 43, 0.08); padding: 10px; border-radius: 6px; margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="tsm-form-group">
            <label for="email" class="tsm-form-label">Email Address</label>
            <input id="email" type="email" name="email" class="tsm-form-input" 
                   value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="username">
            @error('email')
                <span class="tsm-error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="tsm-form-group">
            <label for="password" class="tsm-form-label">Password</label>
            <div class="tsm-password-wrapper">
                <input id="password" type="password" name="password" class="tsm-form-input" 
                       placeholder="Masukkan password" required autocomplete="current-password">
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

        <!-- Remember Me & Forgot Password -->
        <div class="tsm-form-row">
            <label for="remember_me" class="tsm-checkbox-label">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember Me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="tsm-link">Lupa Password?</a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="tsm-btn tsm-btn-primary">
            Log In &rarr;
        </button>

        <!-- Divider -->
        <div class="tsm-divider">
            <span>atau</span>
        </div>

        <!-- Google Login -->
        <a href="#" class="tsm-btn tsm-btn-outline" onclick="alert('Fitur Login Google segera hadir!'); return false;">
            <svg width="16" height="16" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            Login dengan Google
        </a>

        <!-- Footer Link -->
        <div class="tsm-card-footer">
            Belum punya akun? <a href="{{ route('register') }}" class="tsm-link">Daftar di sini</a>
        </div>
    </form>
@endsection