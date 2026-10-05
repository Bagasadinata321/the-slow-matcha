@extends('layouts.app')

@section('title', 'Checkout — Informasi Pengiriman & Pembayaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    {{-- CSS Bootstrap modal jika belum include di layout utama --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="tsm-page">
  <div class="tsm-container">

    <div class="tsm-heading">
      <h1>Checkout</h1>
      <p>Lengkapi data berikut untuk menyelesaikan pesanan Anda.</p>
    </div>

    {{-- Stepper Progress --}}
    <div class="tsm-stepper">
      <div class="tsm-stepper__step is-active">
        <span class="tsm-stepper__num">1</span> Pengiriman & Pembayaran
      </div>
      <span class="tsm-stepper__line"></span>
      <div class="tsm-stepper__step">
        <span class="tsm-stepper__num">2</span> Selesai
      </div>
    </div>

    <form action="{{ route('public.checkout.process') }}" method="POST" id="checkout-form">
      @csrf
      <div class="tsm-layout">

        {{-- Kolom Kiri: Form Data Kontak, Alamat, Kurir & Pembayaran --}}
        <div>
          {{-- 1. Data Kontak --}}
          <div class="tsm-card mb-4">
            <p class="tsm-card__title">Data Kontak</p>

            <div class="tsm-field">
              <label for="customer_email">Email Address <span class="req">*</span></label>
              <input type="email" id="customer_email" name="customer_email" placeholder="nama@email.com" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required>
            </div>

            <div class="tsm-form-row">
              <div class="tsm-field">
                <label for="customer_name">Nama Pemesan <span class="req">*</span></label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Nama lengkap" value="{{ old('customer_name', $primaryAddress->recipient_name ?? auth()->user()->name ?? '') }}" required>
              </div>
              <div class="tsm-field">
                <label for="customer_phone">Nomor WhatsApp / Telepon <span class="req">*</span></label>
                <input type="tel" id="customer_phone" name="customer_phone" placeholder="08xx-xxxx-xxxx" value="{{ old('customer_phone', $primaryAddress->phone_number ?? auth()->user()->phone ?? '') }}" required>
              </div>
            </div>
          </div>

          {{-- 2. Alamat Pengiriman --}}
          <div class="tsm-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <p class="tsm-card__title mb-0">Alamat Pengiriman</p>
              
              {{-- Tombol Pilih Alamat (Muncul jika user login & punya alamat tersimpan) --}}
              @if(auth()->check() && $savedAddresses->count() > 0)
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                  <i class="bi bi-journal-bookmark me-1"></i> Pilih Alamat Tersimpan
                </button>
              @endif
            </div>

            <input type="hidden" name="address_id" id="address_id_input" value="{{ old('address_id', $primaryAddress->id ?? '') }}">

            <div class="tsm-field">
              <label for="shipping_address">Alamat Lengkap (Jalan, No. Rumah, RT/RW) <span class="req">*</span></label>
              <input type="text" id="shipping_address" name="shipping_address" placeholder="Contoh: Jl. Sudirman No. 12, RT 01/RW 02" value="{{ old('shipping_address', $primaryAddress->full_address ?? '') }}" required>
            </div>

            <div class="tsm-form-row">
              <div class="tsm-field">
                <label for="province">Provinsi</label>
                <input type="text" id="province" name="province" placeholder="Contoh: DKI Jakarta" value="{{ old('province', $primaryAddress->province ?? '') }}">
              </div>
              <div class="tsm-field">
                <label for="city">Kota / Kabupaten</label>
                <input type="text" id="city" name="city" placeholder="Contoh: Jakarta Selatan" value="{{ old('city', $primaryAddress->city ?? '') }}">
              </div>
            </div>

            <div class="tsm-form-row">
              <div class="tsm-field">
                <label for="district">Kecamatan</label>
                <input type="text" id="district" name="district" placeholder="Contoh: Kebayoran Baru" value="{{ old('district', $primaryAddress->district ?? '') }}">
              </div>
              <div class="tsm-field">
                <label for="postal_code">Kode Pos</label>
                <input type="text" id="postal_code" name="postal_code" placeholder="5 digit kode pos" value="{{ old('postal_code', $primaryAddress->postal_code ?? '') }}">
              </div>
            </div>

            @auth
              <label class="tsm-checkbox mt-3">
                <input type="checkbox" name="save_address" id="save_address_checkbox" value="1" {{ $primaryAddress ? '' : 'checked' }}>
                Simpan sebagai alamat baru di Buku Alamat
              </label>
            @endauth
          </div>

          {{-- 3. Opsi Pengiriman (Kurir) --}}
          <div class="tsm-card tsm-shipping-list mb-4">
            <p class="tsm-card__title">Pilih Jasa Pengiriman</p>

            <label class="tsm-option is-selected">
              <input type="radio" name="courier" value="JNE - REG" data-cost="18000" checked required>
              <div class="tsm-option__body">
                <div class="tsm-option__title">
                  <span class="tsm-badge-logo">JNE</span> REG (Regular Service)
                </div>
                <p class="tsm-option__meta">Estimasi 2-3 hari kerja</p>
              </div>
              <span class="tsm-option__price">Rp 18.000</span>
            </label>

            <label class="tsm-option">
              <input type="radio" name="courier" value="SICEPAT - BEST" data-cost="24000" required>
              <div class="tsm-option__body">
                <div class="tsm-option__title">
                  <span class="tsm-badge-logo">SiCepat</span> BEST (Besok Sampai)
                </div>
                <p class="tsm-option__meta">Estimasi 1 hari kerja</p>
              </div>
              <span class="tsm-option__price">Rp 24.000</span>
            </label>

            <label class="tsm-option">
              <input type="radio" name="courier" value="POS - KILAT" data-cost="15000" required>
              <div class="tsm-option__body">
                <div class="tsm-option__title">
                  <span class="tsm-badge-logo">POS</span> Pos Kilat Khusus
                </div>
                <p class="tsm-option__meta">Estimasi 2-4 hari kerja</p>
              </div>
              <span class="tsm-option__price">Rp 15.000</span>
            </label>

            <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="18000">
          </div>

          {{-- 4. Metode Pembayaran --}}
          <div class="tsm-card mb-4">
            <p class="tsm-card__title">Metode Pembayaran</p>

            <label class="tsm-option is-selected">
              <input type="radio" name="payment_method" value="manual" checked required>
              <div class="tsm-option__body">
                <div class="tsm-option__title">Transfer Bank (Manual)</div>
                <p class="tsm-option__meta">Transfer via BCA / Mandiri / BRI</p>
              </div>
            </label>

            <label class="tsm-option">
              <input type="radio" name="payment_method" value="midtrans" required>
              <div class="tsm-option__body">
                <div class="tsm-option__title">E-Wallet / QRIS / VA (Midtrans)</div>
                <p class="tsm-option__meta">Pembayaran otomatis via GoPay, QRIS, OVO, dll.</p>
              </div>
            </label>

            <label class="tsm-option">
              <input type="radio" name="payment_method" value="cod" required>
              <div class="tsm-option__body">
                <div class="tsm-option__title">COD (Bayar di Tempat)</div>
                <p class="tsm-option__meta">Bayar tunai saat pesanan tiba</p>
              </div>
            </label>
          </div>
        </div>

        {{-- Kolom Kanan: Ringkasan Pesanan --}}
        <div>
          <div class="tsm-card" id="summary-card" data-shipping-cost="18000" style="position: sticky; top: 20px;">
            <p class="tsm-card__title">Ringkasan Pesanan</p>

            @foreach ($cartItems as $item)
              @php
                $price = ($item->variant->promo_price && $item->variant->promo_price < $item->variant->price)
                    ? $item->variant->promo_price
                    : $item->variant->price;
                $cover = $item->product->coverMedia 
                    ? asset('storage/' . $item->product->coverMedia->path) 
                    : asset('images/placeholder.jpg');
              @endphp
              <div class="tsm-mini-item">
                <img src="{{ $cover }}" alt="{{ $item->product->title }}">
                <div>
                  <p class="tsm-mini-item__name">{{ $item->product->title }}</p>
                  <p class="tsm-mini-item__meta">
                    {{ $item->variant->gram_size ?? $item->variant->variant_name }}<br>
                    Rp {{ number_format($price, 0, ',', '.') }}
                  </p>
                </div>
                <span class="tsm-mini-item__qty">x{{ $item->quantity }}</span>
              </div>
            @endforeach

            <div class="tsm-summary-row mt-3">
              <span>Subtotal</span>
              <span data-summary="subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="tsm-summary-row">
              <span>Ongkos Kirim</span>
              <span id="summary-shipping">Rp 18.000</span>
            </div>
            <div class="tsm-summary-row is-total">
              <span>Total Pembayaran</span>
              <span id="summary-total" data-summary="total">Rp {{ number_format($subtotal + 18000, 0, ',', '.') }}</span>
            </div>

            <button type="submit" class="tsm-btn tsm-btn--primary w-100 mt-3">
              Buat Pesanan Sekarang &rarr;
            </button>
          </div>
        </div>

      </div>
    </form>
  </div>
</div>

{{-- MODAL PILIH ALAMAT --}}
@if(auth()->check() && $savedAddresses->count() > 0)
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addressModalLabel">Pilih Alamat Pengiriman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="list-group list-group-flush">
          @foreach($savedAddresses as $addr)
            <button type="button" class="list-group-item list-group-item-action p-3" onclick='applyAddress(@json($addr))'>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <strong class="text-dark">{{ $addr->label }} {{ $addr->is_primary ? '(Alamat Utama)' : '' }}</strong>
                <span class="badge bg-primary-subtle text-primary">{{ $addr->recipient_name }}</span>
              </div>
              <p class="mb-1 small text-secondary">{{ $addr->full_address }}</p>
              <small class="text-muted d-block">{{ $addr->district }}, {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</small>
            </button>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/checkout.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subtotal = {{ $subtotal }};
        const shippingInputs = document.querySelectorAll('input[name="courier"]');
        const shippingCostInput = document.getElementById('shipping_cost_input');
        const shippingSummaryEl = document.getElementById('summary-shipping');
        const totalSummaryEl = document.getElementById('summary-total');
        const summaryCard = document.getElementById('summary-card');

        // Kalkulasi Ongkir & Total secara Dinamis
        shippingInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Efek visual pilihan kurir
                document.querySelectorAll('.tsm-shipping-list .tsm-option').forEach(opt => opt.classList.remove('is-selected'));
                this.closest('.tsm-option').classList.add('is-selected');

                const cost = parseInt(this.dataset.cost, 10) || 0;
                shippingCostInput.value = cost;
                summaryCard.dataset.shippingCost = cost;
                
                shippingSummaryEl.textContent = 'Rp ' + cost.toLocaleString('id-ID');
                totalSummaryEl.textContent = 'Rp ' + (subtotal + cost).toLocaleString('id-ID');
            });
        });

        // Efek visual pilihan metode pembayaran
        const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
        paymentInputs.forEach(input => {
            input.addEventListener('change', function() {
                input.closest('.tsm-card').querySelectorAll('.tsm-option').forEach(opt => opt.classList.remove('is-selected'));
                this.closest('.tsm-option').classList.add('is-selected');
            });
        });
    });

    // Fungsi Auto-fill Alamat saat memilih dari Modal
    function applyAddress(addressData) {
        document.getElementById('shipping_address').value = addressData.full_address || '';
        document.getElementById('province').value = addressData.province || '';
        document.getElementById('city').value = addressData.city || '';
        document.getElementById('district').value = addressData.district || '';
        document.getElementById('postal_code').value = addressData.postal_code || '';
        document.getElementById('address_id_input').value = addressData.id || '';

        if(addressData.recipient_name) {
            document.getElementById('customer_name').value = addressData.recipient_name;
        }
        if(addressData.phone_number) {
            document.getElementById('customer_phone').value = addressData.phone_number;
        }

        // Hapus centang "Simpan Alamat" karena memilih alamat yang sudah ada
        const saveAddressCheckbox = document.getElementById('save_address_checkbox');
        if(saveAddressCheckbox) {
            saveAddressCheckbox.checked = false;
        }

        // Tutup Modal
        const modalEl = document.getElementById('addressModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if(modal) {
            modal.hide();
        }
    }
</script>
@endpush