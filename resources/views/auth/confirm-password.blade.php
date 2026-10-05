@extends('layouts.auth')

@section('title', 'Konfirmasi Password')

@section('hero')
    <span class="tsm-auth-subtitle">KEAMANAN AKUN</span>
    <h1 class="tsm-auth-title">Konfirmasi Password</h1>
    <p class="tsm-auth-desc">Untuk melanjutkan ke halaman penting, silakan masukkan password Anda.</p>
    <img src="{{ asset('images/auth-matcha-tea.png') }}" alt="Matcha Cup" class="tsm-auth-hero-img">
@endsection

@section('card')
    <div style="text-align: center; margin-bottom: 20px;">
        <div class="tsm-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="tsm-form-group">
            <label for="password" class="tsm-form-label">Masukkan Password</label>
            <div class="tsm-password-wrapper">
                <input id="password" type="password" name="password" class="tsm-form-input" 
                       placeholder="Password Anda" required autocomplete="current-password" autofocus>
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

        <!-- Submit Button -->
        <button type="submit" class="tsm-btn tsm-btn-primary" style="margin-top: 8px; margin-bottom: 12px;">
            Confirm &rarr;
        </button>

        <!-- Kembali -->
        <div style="text-align: center;">
            <a href="javascript:history.back()" class="tsm-link" style="font-size: 12.5px;">Kembali</a>
        </div>
    </form>
@endsection