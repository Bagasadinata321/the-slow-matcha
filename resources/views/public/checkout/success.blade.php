@extends('layouts.app')

@section('title', 'Pesanan Berhasil — TheSlowMatcha')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
<div class="tsm-page">
  <div class="tsm-container">

    <div class="tsm-success">
      <div class="tsm-success__icon">&#10003;</div>
      <h1>Pesanan Berhasil dibuat!</h1>
      <p>Terima kasih telah berbelanja di TheSlowMatcha.<br>Silakan lakukan pembayaran sesuai petunjuk di bawah ini.</p>

      {{-- Rekap Informasi Utama Order --}}
      <div class="tsm-card tsm-order-recap">
        <div>
          <span>Nomor Invoice</span>
          <strong>#{{ $order->invoice_number }}</strong>
        </div>
        <div>
          <span>Tanggal Pemesanan</span>
          <strong>{{ $order->created_at->translatedFormat('d F Y, H:i') }}</strong>
        </div>
        <div>
          <span>Total Pembayaran</span>
          <strong style="color: var(--tsm-green-800);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
        </div>
      </div>

      {{-- Informasi Instruksi Pembayaran (Manual Transfer / COD / PG) --}}
      <div class="tsm-card" style="margin-top: 16px; text-align: left;">
        <p class="tsm-card__title">Instruksi Pembayaran</p>

        @if($order->payment_method === 'manual')
          <p style="font-size: 13.5px; color: var(--tsm-text-muted); margin-bottom: 12px;">
            Silakan lakukan transfer ke rekening resmi kami di bawah ini:
          </p>
          <div style="background: var(--tsm-cream-dark); padding: 12px; border-radius: var(--tsm-radius-sm); font-size: 13.5px;">
            <strong>Bank BCA</strong><br>
            No. Rekening: <strong>123-456-7890</strong><br>
            Atas Nama: <strong>TheSlowMatcha Official</strong>
          </div>
          <p style="font-size: 12.5px; color: var(--tsm-text-soft); margin-top: 10px;">
            *Setelah transfer, harap simpan bukti pembayaran dan konfirmasi via WhatsApp kami.
          </p>
        @elseif($order->payment_method === 'cod')
          <p style="font-size: 13.5px; color: var(--tsm-text-muted);">
            Pesanan Anda diproses dengan metode <strong>COD (Bayar di Tempat)</strong>. Siapkan uang tunai sebesar <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> saat kurir mengantarkan paket.
          </p>
        @else
          <p style="font-size: 13.5px; color: var(--tsm-text-muted);">
            Instruksi pembayaran otomatis telah dikirimkan ke email <strong>{{ $order->customer_email }}</strong>.
          </p>
        @endif
      </div>

      {{-- Tombol Aksi --}}
      <div class="tsm-btn-row">
        <a href="{{ route('public.orders.track', ['invoice_number' => $order->invoice_number]) }}" class="tsm-btn tsm-btn--primary">
          Lacak Pesanan
        </a>
        <a href="{{ route('public.products.index') }}" class="tsm-btn tsm-btn--secondary">
          Kembali ke Beranda
        </a>
      </div>
    </div>

    {{-- Banner Promo --}}
    <div class="tsm-promo" style="background-image: url('{{ asset('images/promo-matcha.jpg') }}'); margin-top: 40px;">
      <div>
        <h3>Terus nikmati momen matcha</h3>
        <p>Ikuti kami untuk koleksi terbaru, tips racikan matcha, dan promo menarik.</p>
        <a href="{{ route('public.products.index') }}">Jelajahi Produk &rarr;</a>
      </div>
    </div>

  </div>
</div>
@endsection