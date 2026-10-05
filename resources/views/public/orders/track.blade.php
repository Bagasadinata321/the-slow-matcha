@extends('layouts.app')

@section('title', 'Lacak Pesanan — TheSlowMatcha')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
<div class="tsm-page">
  <div class="tsm-container">

    <div class="tsm-heading">
      <h1>Lacak Pesanan</h1>
      <p>Masukkan nomor invoice pesanan Anda untuk melihat status pengiriman.</p>
    </div>

    <form action="{{ route('public.orders.track') }}" method="GET" data-track-form>
      <div class="tsm-track-search">
        <input type="text" name="order_number" placeholder="Masukkan nomor invoice (contoh: TSM202609170002)"
               value="{{ request('order_number') }}" required>
        <button type="submit" class="tsm-btn tsm-btn--primary">Lacak</button>
      </div>
      <p class="tsm-track-hint">Contoh: TSM202609170002</p>
    </form>

    @if (request('order_number') && !$order)
      <div class="tsm-card" style="margin-top: 24px;">
        <p class="tsm-text-muted" style="text-align: center; margin: 0;">
          Pesanan dengan nomor <strong>"{{ request('order_number') }}"</strong> tidak ditemukan.
        </p>
      </div>
    @endif

    @if ($order)
      <div class="tsm-card" style="margin-top: 24px;">
        <div class="tsm-timeline">
          @foreach ($order->trackingSteps as $step)
            <div class="tsm-timeline__step {{ $step->is_done ? 'is-done' : ($step->is_current ? 'is-current' : '') }}">
              <div class="tsm-timeline__dot"></div>
              <div class="tsm-timeline__label">{{ $step->label }}</div>
              <div class="tsm-timeline__date">
                {{ $step->date ? $step->date->translatedFormat('d M Y, H:i') : $step->estimated_date }}
              </div>
            </div>
          @endforeach
        </div>

        <div class="tsm-resi-row">
          <div>
            <span class="tsm-text-muted">No. Resi Pengiriman</span><br>
            <strong>{{ $order->tracking_number ?? 'Belum terbit' }}</strong>
          </div>
          @if($order->tracking_number)
            <a href="{{ $order->courier_tracking_url }}" target="_blank" rel="noopener">Lacak di {{ $order->courier }} &#8599;</a>
          @endif
        </div>
      </div>

      <div class="tsm-card" style="margin-top: 16px;">
        <p class="tsm-card__title">Detail Barang Pesanan</p>
        @foreach ($order->items as $item)
          <div class="tsm-mini-item" style="align-items:center;">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
            <div>
              <p class="tsm-mini-item__name">{{ $item->name }}</p>
              <p class="tsm-mini-item__meta">{{ $item->variant_label }} &middot; Qty {{ $item->qty }}</p>
            </div>
            <span class="tsm-mini-item__qty">
              <span class="tsm-status-badge">{{ $order->status_label }}</span>
            </span>
          </div>
        @endforeach
      </div>
    @endif

  </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/checkout.js') }}"></script>
@endpush