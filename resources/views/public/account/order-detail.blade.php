@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->invoice_number . ' — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/account.css') }}">
<style>
  /* Styling Modal Ulasan & Star Rating */
  .tsm-modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
    z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.2s ease;
  }
  .tsm-modal-overlay.is-active { opacity: 1; visibility: visible; }
  .tsm-modal-card {
    background: #fff; width: 100%; max-width: 480px; padding: 24px; border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15); position: relative; transform: translateY(-20px); transition: transform 0.2s ease;
  }
  .tsm-modal-overlay.is-active .tsm-modal-card { transform: translateY(0); }
  .star-rating-input { display: flex; flex-direction: row-reverse; justify-content: center; gap: 8px; margin: 16px 0; }
  .star-rating-input input { display: none; }
  .star-rating-input label { font-size: 32px; color: #ccc; cursor: pointer; transition: color 0.15s; }
  .star-rating-input input:checked ~ label,
  .star-rating-input label:hover,
  .star-rating-input label:hover ~ label { color: #f59e0b; }
</style>
@endpush

@section('content')
<div class="tsm-account">
  <div class="ac-shell">

    {{-- Sidebar Navigasi Akun --}}
    @include('public.account.sidebar', ['active' => 'orders'])

    <main class="ac-main">
      {{-- Navigasi Breadcrumb --}}
      <nav class="ac-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('public.account.profile.edit') }}">Akun</a>
        <span>&rsaquo;</span>
        <a href="{{ route('public.account.orders.index') }}">Riwayat Pesanan</a>
        <span>&rsaquo;</span>
        #{{ $order->invoice_number }}
      </nav>

      {{-- Alert Session --}}
      @if(session('success'))
        <div style="background: #d1e7dd; color: #0f5132; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div style="background: #f8d7da; color: #842029; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
          {{ session('error') }}
        </div>
      @endif

      {{-- Header & Status Pesanan --}}
      <div class="ac-head">
        <div>
          <h1>Detail Pesanan</h1>
          <p>Dipesan pada {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }} WITA</p>
        </div>
        <div>
          @php
            $statusRaw = strtolower($order->order_status ?? $order->payment_status ?? 'pending');
          @endphp
          <span class="ac-status ac-status--{{ $statusRaw }}">
            {{ ucfirst($order->order_status ?? $order->payment_status ?? 'Pending') }}
          </span>
        </div>
      </div>

      {{-- Layout Dua Kolom --}}
      <div class="ac-detail-layout">
        
        {{-- Kolom Kiri: Item Produk & Pengiriman --}}
        <div>
          {{-- Card Daftar Produk --}}
          <div class="ac-card ac-detail-section" style="margin-bottom: 16px;">
            <h2 class="ac-detail-section__title">Produk yang Dibeli</h2>

            <div class="ac-detail-item-list">
              @foreach($order->items as $item)
                @php
                  $product = $item->product ?? ($item->variant->product ?? null);
                  $coverMedia = $product->coverMedia ?? null;
                  $imagePath = $coverMedia ? asset('storage/' . $coverMedia->path) : asset('images/placeholder-matcha.jpg');
                  $itemPrice = $item->price ?? $item->unit_price ?? 0;
                  $itemSubtotal = $item->subtotal ?? ($itemPrice * $item->quantity);

                  // Cek apakah produk ini sudah pernah diulas pada pesanan ini
                  $alreadyReviewed = false;
                  if ($product && $order->relationLoaded('reviews')) {
                      $alreadyReviewed = $order->reviews->where('product_id', $product->id)->first();
                  } elseif ($product) {
                      $alreadyReviewed = \App\Models\Review::where('order_id', $order->id)
                          ->where('product_id', $product->id)
                          ->where('user_id', Auth::id())
                          ->first();
                  }
                @endphp
                <div class="ac-order-item" style="border-bottom: 1px solid var(--ac-line-soft); padding: 16px 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                  <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 220px;">
                    <img src="{{ $imagePath }}" alt="{{ $item->product_name ?? 'Matcha' }}" style="width: 56px; height: 56px; object-fit: cover; border-radius: 6px;">
                    <div>
                      <p class="ac-order-item__name" style="font-weight: 600;">{{ $item->product_name ?? ($product->title ?? 'Produk Matcha') }}</p>
                      <p class="ac-order-item__variant" style="font-size: 13px; color: var(--ac-ink-muted);">Varian: {{ $item->variant_name ?? ($item->variant->variant_name ?? '-') }}</p>
                    </div>
                  </div>

                  <div class="ac-order-item__qty" style="font-size: 14px;">
                    {{ $item->quantity }}x
                  </div>

                  <div class="ac-order-item__price" style="font-weight: 600;">
                    Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                  </div>

                  {{-- TOMBOL ULASAN --}}
                  <div style="width: 100%; text-align: right; margin-top: 8px;">
                    @if(strtolower($order->order_status) === 'completed' && $product)
                      @if($alreadyReviewed)
                        <span style="font-size: 13px; color: #f59e0b; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                          ★ {{ $alreadyReviewed->rating }}/5 (Sudah Diulas)
                        </span>
                      @else
                        <button type="button" class="ac-btn ac-btn--ghost ac-btn--sm" 
                                onclick="openReviewModal('{{ $product->id }}', '{{ e($item->product_name ?? $product->title) }}')">
                          ★ Beri Ulasan
                        </button>
                      @endif
                    @endif
                  </div>
                </div>
              @endforeach
            </div>

            {{-- Tombol Beli Lagi --}}
            <div style="margin-top: 20px; text-align: right; padding-top: 14px; border-top: 1px solid var(--ac-line-soft);">
              <form action="{{ route('public.account.orders.reorder', $order->invoice_number) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="ac-btn ac-btn--ghost ac-btn--sm">
                  Beli Lagi Produk Ini
                </button>
              </form>
            </div>
          </div>

          {{-- Card Informasi Pengiriman --}}
          <div class="ac-card ac-detail-section">
            <h2 class="ac-detail-section__title">Informasi Pengiriman</h2>
            <div class="ac-detail-info-grid">
              <div class="ac-info-item">
                <label>Penerima</label>
                <strong>{{ $order->shipping_name ?? $order->user->name ?? 'Pelanggan' }}</strong>
                <p style="color: var(--ac-ink-muted);">{{ $order->shipping_phone ?? '-' }}</p>
              </div>
              <div class="ac-info-item">
                <label>Kurir & Resi</label>
                <strong>{{ strtoupper($order->shipping_courier ?? 'Reguler') }} - {{ $order->shipping_service ?? 'Standard' }}</strong>
                @if(!empty($order->tracking_number))
                  <p style="color: var(--ac-green); font-weight: 600;">No. Resi: {{ $order->tracking_number }}</p>
                @endif
              </div>
              <div class="ac-info-item" style="grid-column: 1 / -1; border-top: 1px solid var(--ac-line-soft); padding-top: 12px;">
                <label>Alamat Lengkap</label>
                <p>{{ $order->shipping_address ?? 'Alamat pengiriman tidak dicantumkan.' }}</p>
              </div>
            </div>
          </div>
        </div>

        {{-- Kolom Kanan: Ringkasan Pembayaran --}}
        <div>
          <div class="ac-card ac-detail-section">
            <h2 class="ac-detail-section__title">Ringkasan Pembayaran</h2>

            <div class="ac-summary-row">
              <span>Subtotal Produk</span>
              <strong>Rp {{ number_format($order->subtotal ?? $order->total_amount, 0, ',', '.') }}</strong>
            </div>

            <div class="ac-summary-row">
              <span>Biaya Pengiriman</span>
              <strong>Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</strong>
            </div>

            @if(!empty($order->discount_amount) && $order->discount_amount > 0)
              <div class="ac-summary-row" style="color: var(--ac-danger);">
                <span>Diskon Promo</span>
                <strong>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</strong>
              </div>
            @endif

            <div class="ac-summary-row is-total">
              <span>Total Pembayaran</span>
              <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>

            <div class="ac-payment-box">
              <span style="font-size: 11px; text-transform: uppercase; color: var(--ac-ink-muted); display: block;">Metode Pembayaran</span>
              <strong style="font-size: 13px;">{{ str_replace('_', ' ', strtoupper($order->payment_method ?? 'Transfer Bank')) }}</strong>
            </div>

            @if(strtolower($order->payment_status) == 'unpaid')
              <a href="{{ route('public.checkout.success', $order->invoice_number) }}" class="ac-btn ac-btn--primary ac-btn--block">
                Bayar Sekarang
              </a>
            @endif
          </div>
        </div>

      </div>

    </main>
  </div>
</div>

{{-- MODAL INPUT RATING & ULASAN --}}
<div class="tsm-modal-overlay" id="reviewModal">
  <div class="tsm-modal-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
      <h3 style="margin:0; font-size: 18px;">Ulas Produk</h3>
      <button type="button" onclick="closeReviewModal()" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
    </div>
    
    <p id="modalProductName" style="font-weight: 600; color: var(--ac-ink-muted); margin-bottom: 12px; font-size: 14px;"></p>

    <form action="{{ route('public.account.orders.reviews.store', $order->invoice_number) }}" method="POST">
      @csrf
      <input type="hidden" name="product_id" id="modalProductId">

      <div style="text-align: center; margin-bottom: 12px;">
        <label style="font-size: 13px; color: var(--ac-ink-muted);">Berapa nilai kepuasan Anda?</label>
        <div class="star-rating-input">
          <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 bintang">★</label>
          <input type="radio" id="star4" name="rating" value="4"/><label for="star4" title="4 bintang">★</label>
          <input type="radio" id="star3" name="rating" value="3"/><label for="star3" title="3 bintang">★</label>
          <input type="radio" id="star2" name="rating" value="2"/><label for="star2" title="2 bintang">★</label>
          <input type="radio" id="star1" name="rating" value="1"/><label for="star1" title="1 bintang">★</label>
        </div>
      </div>

      <div style="margin-bottom: 16px;">
        <label style="font-size: 13px; color: var(--ac-ink-muted); display: block; margin-bottom: 6px;">Tuliskan Komentar / Ulasan (Opsional)</label>
        <textarea name="comment" rows="4" placeholder="Bagaimana rasa, kualitas, dan pengalaman Anda dengan produk ini?" 
                  style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 14px; box-sizing: border-box;"></textarea>
      </div>

      <div style="display: flex; gap: 8px; justify-content: flex-end;">
        <button type="button" class="ac-btn ac-btn--ghost ac-btn--sm" onclick="closeReviewModal()">Batal</button>
        <button type="submit" class="ac-btn ac-btn--primary ac-btn--sm">Kirim Ulasan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function openReviewModal(productId, productName) {
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalProductName').innerText = productName;
    document.getElementById('reviewModal').classList.add('is-active');
  }

  function closeReviewModal() {
    document.getElementById('reviewModal').classList.remove('is-active');
  }
</script>
@endpush