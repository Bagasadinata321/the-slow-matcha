@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('hero')
    <span class="tsm-auth-subtitle">LUPA PASSWORD</span>
    <h1 class="tsm-auth-title">Reset Password</h1>
    <p class="tsm-auth-desc">Masukkan email yang terdaftar pada akun Anda. Kami akan mengirimkan link untuk mereset password melalui email.</p>
    <img src="{{ asset('images/auth-matcha-powder.png') }}" alt="Matcha Bowl & Whisk" class="tsm-auth-hero-img">
@endsection

@section('card')
    <!-- Session Status (Notifikasi jika link berhasil dikirim) -->
    @if (session('status'))
        <div style="font-size: 12.5px; color: var(--tsm-matcha-deep); background: rgba(44, 56, 43, 0.08); padding: 12px; border-radius: 6px; margin-bottom: 18px; line-height: 1.5;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <!-- Submit Button -->
        <button type="submit" class="tsm-btn tsm-btn-primary" style="margin-top: 8px;">
            Kirim Link Reset Password &rarr;
        </button>

        <!-- Divider -->
        <div class="tsm-divider">
            <span>atau</span>
        </div>

        <!-- Back to Login Link -->
        <div style="text-align: center;">
            <a href="{{ route('login') }}" class="tsm-link" style="font-size: 12.5px; display: inline-flex; align-items: center; gap: 6px;">
                &larr; Kembali ke halaman Login
            </a>
        </div>
    </form>
@endsection