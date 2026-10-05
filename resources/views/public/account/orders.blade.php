@extends('layouts.app')

@section('title', 'Riwayat Pesanan — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
<div class="tsm-account">
  <div class="ac-shell">

    @include('public.account.sidebar', ['active' => 'orders'])

    <main class="ac-main">
      <nav class="ac-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('public.account.profile.edit') }}">Akun</a>
        <span>&rsaquo;</span>
        Riwayat Pesanan
      </nav>

      <div class="ac-head">
        <div>
          <h1>Riwayat Pesanan</h1>
          <p>Lacak status transaksi dan riwayat pembelian Anda.</p>
        </div>
      </div>

      {{-- Tab Filter Status --}}
      <div class="ac-card" style="margin-bottom: 16px; padding: 8px 12px; display: flex; gap: 8px; overflow-x: auto;">
        <a href="{{ route('public.account.orders.index') }}" class="ac-btn {{ !$status ? 'ac-btn--primary' : '' }}" style="font-size: 12px; padding: 6px 12px;">Semua</a>
        <a href="{{ route('public.account.orders.index', ['status' => 'pending']) }}" class="ac-btn {{ $status == 'pending' ? 'ac-btn--primary' : '' }}" style="font-size: 12px; padding: 6px 12px;">Menunggu</a>
        <a href="{{ route('public.account.orders.index', ['status' => 'processing']) }}" class="ac-btn {{ $status == 'processing' ? 'ac-btn--primary' : '' }}" style="font-size: 12px; padding: 6px 12px;">Diproses</a>
        <a href="{{ route('public.account.orders.index', ['status' => 'shipped']) }}" class="ac-btn {{ $status == 'shipped' ? 'ac-btn--primary' : '' }}" style="font-size: 12px; padding: 6px 12px;">Dikirim</a>
        <a href="{{ route('public.account.orders.index', ['status' => 'completed']) }}" class="ac-btn {{ $status == 'completed' ? 'ac-btn--primary' : '' }}" style="font-size: 12px; padding: 6px 12px;">Selesai</a>
      </div>

      {{-- Daftar Pesanan --}}
      @forelse($orders as $order)
        <div class="ac-card" style="margin-bottom: 16px; padding: 18px 22px;">
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--ac-border, #eee); padding-bottom: 12px; margin-bottom: 12px;">
            <div>
              <strong>#{{ $order->invoice_number }}</strong>
              <span style="font-size: 12px; color: #777; margin-left: 8px;">{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>
            <div>
              <span class="ac-badge" style="padding: 4px 8px; border-radius: 4px; font-size: 11px; text-transform: uppercase; font-weight: bold; background: #e0f2fe; color: #0369a1;">
                {{ $order->order_status }}
              </span>
            </div>
          </div>

          @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <div>
                <p style="margin: 0; font-weight: 500;">{{ $item->product_name }}</p>
                <p style="margin: 0; font-size: 12px; color: #666;">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
              </div>
              <span style="font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
          @endforeach

          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--ac-border, #eee); padding-top: 12px; margin-top: 12px;">
            <div>
              <span style="font-size: 12px; color: #666;">Total Transaksi:</span>
              <strong style="font-size: 16px; color: var(--ac-green-deep);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
            </div>

            <div style="display: flex; gap: 8px;">
              <a href="{{ route('public.account.orders.show', $order->invoice_number) }}" class="ac-btn" style="font-size: 12px; border: 1px solid #ccc;">Detail</a>

              <form action="{{ route('public.account.orders.reorder', $order->invoice_number) }}" method="POST">
                @csrf
                <button type="submit" class="ac-btn ac-btn--primary" style="font-size: 12px;">Beli Lagi</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="ac-card" style="padding: 32px; text-align: center; color: #777;">
          Belum ada riwayat pesanan.
        </div>
      @endforelse

      {{-- Pagination --}}
      <div style="margin-top: 16px;">
        {{ $orders->links() }}
      </div>

    </main>
  </div>
</div>
@endsection