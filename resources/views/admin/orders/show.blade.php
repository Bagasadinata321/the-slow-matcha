@extends('layouts.admin') {{-- Sesuaikan dengan master layout admin Anda --}}

@section('title', 'Detail Pesanan - ' . $order->invoice_number)

@section('content')
<div class="p-6 space-y-6">

    {{-- Header & Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $order->invoice_number }}</h1>
                <p class="text-sm text-gray-500">Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WITA</p>
            </div>
        </div>
    </div>

    {{-- Alert Success Notification --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Order Items & Payment Breakdown (2 Cols) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Items Table Card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Rincian Produk</h2>
                    <span class="text-xs font-semibold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">
                        {{ $order->items->sum('quantity') }} Items
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 text-xs uppercase tracking-wider">
                                <th class="py-3 px-4">Produk</th>
                                <th class="py-3 px-4 text-center">Harga</th>
                                <th class="py-3 px-4 text-center">Jumlah</th>
                                <th class="py-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <span class="font-semibold text-gray-800 block">{{ $item->product_name }}</span>
                                        <span class="text-xs text-emerald-600 font-medium block mt-0.5">Ukuran: {{ $item->gram_size }}g</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-medium">
                                        {{ $item->quantity }}x
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-semibold text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Payment Summary Footer --}}
                <div class="p-4 border-t border-gray-100 bg-gray-50/30 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal Produk</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Biaya Pengiriman (Ongkir)</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-base text-gray-900 pt-2 border-t border-gray-200">
                        <span>Total Pembayaran</span>
                        <span class="text-emerald-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Customer & Shipping Details Card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Informasi Pelanggan & Pengiriman</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 block uppercase font-medium">Nama Pembeli</span>
                        <span class="font-semibold text-gray-800 block mt-1">{{ $order->customer_name }}</span>
                        @if($order->user)
                            <span class="inline-block text-[11px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-1">Akun Terdaftar</span>
                        @else
                            <span class="inline-block text-[11px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded mt-1">Guest / Tanpa Akun</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block uppercase font-medium">Nomor WhatsApp / HP</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1 mt-1">
                            {{ $order->customer_phone }}
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12c0 2.17.69 4.19 1.87 5.84L2.5 21.5l3.8-1.32C7.88 21.28 9.87 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-xs text-gray-400 block uppercase font-medium">Alamat Lengkap Pengiriman</span>
                        <p class="text-gray-700 mt-1 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                            {{ $order->shipping_address }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column: Status Update Form Card (1 Col) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-5">
                <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-3">Update Status Pesanan</h2>

                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    {{-- Status Pesanan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status Pesanan</label>
                        <select name="order_status" class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                            <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    {{-- Status Pembayaran --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status Pembayaran</label>
                        <select name="payment_status" class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="expired" {{ $order->payment_status == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                        </select>
                    </div>

                    {{-- Nomor Resi --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nomor Resi Pengiriman</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" 
                            placeholder="Contoh: JX123456789" 
                            class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 font-mono focus:ring-emerald-500 focus:border-emerald-500">
                        <span class="text-[11px] text-gray-400 mt-1 block">Wajib diisi jika status diubah menjadi 'Dikirim'.</span>
                    </div>

                    {{-- Method Payment Display Only --}}
                    <div class="pt-2">
                        <span class="text-xs text-gray-400 block uppercase font-medium">Metode Pembayaran</span>
                        <span class="text-sm font-semibold text-gray-700 uppercase block mt-0.5">{{ $order->payment_method }}</span>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection