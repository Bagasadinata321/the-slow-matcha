@extends('layouts.auth')

@section('title', 'Verifikasi Email')

@section('hero')
    <span class="tsm-auth-subtitle">VERIFIKASI EMAIL</span>
    <h1 class="tsm-auth-title">Cek Email Anda</h1>
    <p class="tsm-auth-desc">Kami telah mengirimkan link verifikasi ke alamat email Anda. Silakan periksa inbox atau folder spam.</p>
    <img src="{{ asset('images/auth-matcha-plant.png') }}" alt="Matcha Leaves" class="tsm-auth-hero-img">
@endsection

@section('card')
    <!-- Status Notifikasi jika link baru berhasil dikirim -->
    @if (session('status') == 'verification-link-sent')
        <div style="font-size: 12.5px; color: var(--tsm-matcha-deep); background: rgba(44, 56, 43, 0.08); padding: 12px; border-radius: 6px; margin-bottom: 20px; line-height: 1.5; text-align: center;">
            Link verifikasi baru telah berhasil dikirim ke alamat email Anda.
        </div>
    @endif

    <div style="text-align: center; margin-bottom: 24px;">
        <div class="tsm-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
        </div>
        <h3 style="font-size: 15px; font-weight: 600; color: var(--tsm-ink); margin-bottom: 6px;">Tidak menerima email?</h3>
        <p style="font-size: 12.5px; color: var(--tsm-ink-muted); line-height: 1.5;">
            Jika Anda tidak menemukan email, Anda dapat mengirim ulang link verifikasi.
        </p>
    </div>

    <!-- Tombol Kirim Ulang Link Verifikasi -->
    <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom: 12px;">
        @csrf
        <button type="submit" class="tsm-btn tsm-btn-primary">
            Kirim Ulang Link &rarr;
        </button>
    </form>

    <!-- Tombol Logout -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="tsm-btn tsm-btn-outline">
            Log Out
        </button>
    </form>
@endsection