@extends('layouts.app')

@section('title', 'Buku Alamat — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
<div class="tsm-account">
  <div class="ac-shell">

    @include('public.account.sidebar', ['active' => 'address'])

    <main class="ac-main">
      <nav class="ac-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('public.account.profile.edit') }}">Akun</a>
        <span>&rsaquo;</span>
        Alamat
      </nav>

      <div class="ac-head">
        <div>
          <h1>Buku Alamat</h1>
          <p>Kelola alamat pengiriman Anda agar proses checkout lebih cepat.</p>
        </div>
        <button type="button" class="ac-btn ac-btn--primary" data-panel-open="address-form">
          + Tambah Alamat Baru
        </button>
      </div>

      {{-- Notifikasi Sukses --}}
      @if(session('success'))
        <div class="ac-card" style="padding: 12px 16px; background: var(--ac-sage); color: var(--ac-green-deep); margin-bottom: 16px; font-size: 13px; border-radius: var(--ac-radius);">
          {{ session('success') }}
        </div>
      @endif

      {{-- Daftar Alamat --}}
      @forelse ($addresses as $address)
        <article class="ac-card ac-address">
          <div class="ac-address__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M3 10l9-7 9 7v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9z"></path>
              <path d="M9 21v-7h6v7"></path>
            </svg>
          </div>

          <div class="ac-address__body">
            <div class="ac-address__label-row">
              <span class="ac-address__label">{{ $address->label }}</span>
              @if ($address->is_primary)
                <span class="ac-badge-primary">Alamat Utama</span>
              @endif
            </div>
            <p class="ac-address__name">{{ $address->recipient_name }}</p>
            <p class="ac-address__phone">{{ $address->phone_number }}</p>
            <p class="ac-address__text">
              {{ $address->full_address }}<br>
              {{ implode(', ', array_filter([$address->district, $address->city, $address->province, $address->postal_code])) }}
            </p>
          </div>

          <div class="ac-address__actions">
            @unless ($address->is_primary)
              <form action="{{ route('public.account.addresses.primary', $address->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="ac-btn ac-btn--ghost ac-btn--sm">
                  Atur sebagai Alamat Utama
                </button>
              </form>
            @endunless

            <form action="{{ route('public.account.addresses.destroy', $address->id) }}" method="POST" data-confirm="Hapus alamat ini?">
              @csrf
              @method('DELETE')
              <button type="submit" class="ac-btn ac-btn--danger ac-btn--sm">Hapus</button>
            </form>
          </div>
        </article>
      @empty
        <div class="ac-card ac-empty">Belum ada alamat tersimpan.</div>
      @endforelse

      {{-- Panel Form Tambah Alamat Baru --}}
      <div class="ac-card" data-panel="address-form" style="margin-top: 16px;">
        <form action="{{ route('public.account.addresses.store') }}" method="POST">
          @csrf
          <div class="ac-panel">
            <div class="ac-panel__head">
              <p class="ac-panel__title">Tambah Alamat Baru</p>
              <button type="button" class="ac-panel__close" data-panel-close="address-form" aria-label="Tutup form">&times;</button>
            </div>

            <div class="ac-form-grid">
              <div class="ac-field">
                <label for="addr_label">Label Alamat <span class="req">*</span></label>
                <input type="text" id="addr_label" name="label" placeholder="Rumah / Kantor" required>
              </div>

              <div class="ac-field">
                <label for="addr_recipient">Nama Penerima <span class="req">*</span></label>
                <input type="text" id="addr_recipient" name="recipient_name" placeholder="Nama lengkap" required>
              </div>

              <div class="ac-field">
                <label for="addr_phone">Nomor Telepon <span class="req">*</span></label>
                <input type="tel" id="addr_phone" name="phone_number" placeholder="Contoh: 081234567890" required>
              </div>

              <div class="ac-field">
                <label for="addr_province">Provinsi</label>
                <input type="text" id="addr_province" name="province" placeholder="Provinsi">
              </div>

              <div class="ac-field">
                <label for="addr_city">Kota/Kabupaten</label>
                <input type="text" id="addr_city" name="city" placeholder="Kota/Kabupaten">
              </div>

              <div class="ac-field">
                <label for="addr_district">Kecamatan</label>
                <input type="text" id="addr_district" name="district" placeholder="Kecamatan">
              </div>

              <div class="ac-field">
                <label for="addr_postal">Kode Pos</label>
                <input type="text" id="addr_postal" name="postal_code" placeholder="Kode Pos">
              </div>

              <div class="ac-field ac-field--full">
                <label for="addr_full">Alamat Lengkap <span class="req">*</span></label>
                <textarea id="addr_full" name="full_address" placeholder="Nama jalan, nomor rumah, RT/RW, patokan" required></textarea>
              </div>
            </div>

            <div class="ac-panel__footer">
              <label class="ac-checkbox">
                <input type="checkbox" name="is_primary" value="1">
                Jadikan Alamat Utama
              </label>

              <div class="ac-panel__footer-actions">
                <button type="button" class="ac-btn ac-btn--ghost" data-panel-close="address-form">Batal</button>
                <button type="submit" class="ac-btn ac-btn--primary">Simpan Alamat</button>
              </div>
            </div>
          </div>
        </form>
      </div>

    </main>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/account.js') }}"></script>
@endpush