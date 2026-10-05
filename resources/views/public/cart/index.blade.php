@extends('layouts.app')

@section('title', 'Keranjang Belanja — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
<div class="tsm-page">
  <div class="tsm-container">

    <div class="tsm-heading">
      <h1>Keranjang Belanja</h1>
      <p>Pastikan kembali produk yang kamu pesan sebelum ke proses checkout.</p>
    </div>

    <div class="tsm-layout">

      {{-- Kolom kiri: daftar item --}}
      <div>
        <div class="tsm-card">
          @forelse ($cartItems as $item)
            @php
              $price = ($item->variant->promo_price && $item->variant->promo_price < $item->variant->price)
                  ? $item->variant->promo_price
                  : $item->variant->price;
              $cover = $item->product->coverMedia 
                  ? asset('storage/' . $item->product->coverMedia->path) 
                  : asset('images/placeholder.jpg');
            @endphp

            <div class="tsm-item" data-cart-id="{{ $item->id }}">
              <img src="{{ $cover }}" alt="{{ $item->product->title }}" class="tsm-item__img">
              
              <div class="tsm-item__body">
                <div class="tsm-item__top">
                  <div>
                    <p class="tsm-item__name">{{ $item->product->title }}</p>
                    <p class="tsm-item__meta">{{ $item->variant->gram_size ?? $item->variant->variant_name }}</p>
                  </div>
                  <button type="button" class="tsm-item__remove" onclick="destroyCartItem({{ $item->id }})" title="Hapus item">
                    &times;
                  </button>
                </div>

                <div class="tsm-item__bottom">
                  <div class="tsm-qty" data-price="{{ $price }}">
                    <button type="button" data-action="decrease">&minus;</button>
                    <input type="number" min="1" value="{{ $item->quantity }}" inputmode="numeric" onchange="syncQty({{ $item->id }}, this.value)">
                    <button type="button" data-action="increase">+</button>
                  </div>
                  <span class="tsm-item__price">Rp {{ number_format($price * $item->quantity, 0, ',', '.') }}</span>
                </div>
              </div>
            </div>
          @empty
            <p class="tsm-text-muted">Keranjang belanja kamu masih kosong.</p>
          @endforelse
        </div>

        <a href="{{ route('public.products.index') }}" class="tsm-link-back">&larr; Lanjut Belanja</a>

        <div class="tsm-promo" style="background-image: url('{{ asset('images/promo-matcha.jpg') }}')">
          <div>
            <h3>Nikmati pengalaman matcha terbaik</h3>
            <p>Yuk koleksi pilihan Jepang untuk momen terbaikmu.</p>
            <a href="{{ route('public.products.index') }}">Jelajahi Produk &rarr;</a>
          </div>
        </div>
      </div>

      {{-- Kolom kanan: ringkasan belanja --}}
      <div class="tsm-card" data-shipping-cost="0">
        <p class="tsm-card__title">Ringkasan Belanja</p>

        <div class="tsm-summary-row">
          <span>Subtotal ({{ $cartItems->count() }} item)</span>
          <span data-summary="subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="tsm-summary-row">
          <span>Ongkos Kirim</span>
          <span>-</span>
        </div>
        <div class="tsm-summary-row is-total">
          <span>Total</span>
          <span data-summary="total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>

        @if($cartItems->isNotEmpty())
          <a href="{{ route('public.checkout.index') }}" class="tsm-btn tsm-btn--primary" style="margin-top: 14px;">
            Lanjut ke Checkout &rarr;
          </a>
        @else
          <button type="button" class="tsm-btn tsm-btn--primary" style="margin-top: 14px;" disabled>
            Lanjut ke Checkout &rarr;
          </button>
        @endif
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/checkout.js') }}"></script>
<script>
    // Integrasi AJAX Sync dengan backend saat kuantitas berubah
    function syncQty(itemId, newQty) {
        if (newQty < 1) return;

        fetch(`/cart/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Gagal memperbarui kuantitas.');
                window.location.reload();
            }
        })
        .catch(() => alert('Terjadi kesalahan koneksi.'));
    }

    // Integrasi AJAX Hapus Item
    function destroyCartItem(itemId) {
        if (!confirm('Hapus produk ini dari keranjang?')) return;

        fetch(`/cart/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Gagal menghapus item.');
            }
        })
        .catch(() => alert('Terjadi kesalahan koneksi.'));
    }

    // Hook ke checkout.js tombol +/- agar memicu sinkronisasi database
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tsm-qty button').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const itemRow = this.closest('.tsm-item');
                if (input && itemRow) {
                    const itemId = itemRow.dataset.cartId;
                    syncQty(itemId, input.value);
                }
            });
        });
    });
</script>
@endpush