@extends('layouts.auth')

@section('title', 'Buat Password Baru')

@section('hero')
    <span class="tsm-auth-subtitle">RESET PASSWORD</span>
    <h1 class="tsm-auth-title">Buat Password Baru</h1>
    <p class="tsm-auth-desc">Masukkan password baru Anda untuk melanjutkan akses ke akun TheSlowMatcha.</p>
    <img src="{{ asset('images/auth-matcha-tea.png') }}" alt="Matcha Cup" class="tsm-auth-hero-img">
@endsection

@section('card')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="tsm-form-group">
            <label for="email" class="tsm-form-label">Email Address</label>
            <input id="email" type="email" name="email" class="tsm-form-input" 
                   value="{{ old('email', $request->email) }}" placeholder="nama@email.com" required readonly autocomplete="username">
            @error('email')
                <span class="tsm-error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password Baru -->
        <div class="tsm-form-group">
            <label for="password" class="tsm-form-label">Password Baru</label>
            <div class="tsm-password-wrapper">
                <input id="password" type="password" name="password" class="tsm-form-input" 
                       placeholder="Minimal 8 karakter" required autofocus autocomplete="new-password">
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

        <!-- Konfirmasi Password Baru -->
        <div class="tsm-form-group">
            <label for="password_confirmation" class="tsm-form-label">Konfirmasi Password Baru</label>
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
            Reset Password &rarr;
        </button>
    </form>
@endsection