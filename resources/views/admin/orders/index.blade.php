@extends('layouts.admin') {{-- Sesuaikan dengan master layout admin Anda --}}

@section('title', 'Daftar Pesanan')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pesanan</h1>
            <p class="text-sm text-gray-500">Kelola pesanan masuk, pembaruan status, dan pengiriman resi.</p>
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

    {{-- Search & Filter Section --}}
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            
            {{-- Search Bar --}}
            <div class="md:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari No. Invoice, Nama Customer, No. HP..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            {{-- Filter Status Order --}}
            <div>
                <select name="order_status" class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Semua Status Order --</option>
                    <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="completed" {{ request('order_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            {{-- Filter Status Pembayaran & Button Submit --}}
            <div class="flex gap-2">
                <select name="payment_status" class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Status Bayar --</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    <option value="expired" {{ request('payment_status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                </select>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'order_status', 'payment_status']))
                    <a href="{{ route('admin.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Orders Table Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Invoice / Tanggal</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Ringkasan Item</th>
                        <th class="py-3 px-4">Total Bayar</th>
                        <th class="py-3 px-4">Status Bayar</th>
                        <th class="py-3 px-4">Status Pesanan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            
                            {{-- Invoice & Date --}}
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-semibold text-emerald-600 hover:text-emerald-700 block">
                                    {{ $order->invoice_number }}
                                </a>
                                <span class="text-xs text-gray-400 block mt-0.5">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </span>
                            </td>

                            {{-- Customer Info --}}
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-gray-800 block">{{ $order->customer_name }}</span>
                                <span class="text-xs text-gray-500 block mt-0.5">{{ $order->customer_phone }}</span>
                            </td>

                            {{-- Items Summary --}}
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="text-xs text-gray-600 line-clamp-1">
                                    {{ $order->items->pluck('product_name')->implode(', ') }}
                                </span>
                                <span class="text-xs text-gray-400 block mt-0.5">
                                    {{ $order->items->sum('quantity') }} Total Item
                                </span>
                            </td>

                            {{-- Total Amount --}}
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-900 block">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                                <span class="text-[11px] text-gray-400 block uppercase font-medium">
                                    {{ $order->payment_method }}
                                </span>
                            </td>

                            {{-- Payment Status Badge --}}
                            <td class="py-3.5 px-4">
                                @switch($order->payment_status)
                                    @case('paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Lunas
                                        </span>
                                        @break
                                    @case('unpaid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            Belum Bayar
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            Gagal
                                        </span>
                                        @break
                                    @case('expired')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            Kadaluarsa
                                        </span>
                                        @break
                                @endswitch
                            </td>

                            {{-- Order Status Badge --}}
                            <td class="py-3.5 px-4">
                                @switch($order->order_status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                        @break
                                    @case('processing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Diproses
                                        </span>
                                        @break
                                    @case('shipped')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                            Dikirim
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Selesai
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            Batal
                                        </span>
                                        @break
                                @endswitch

                                @if($order->tracking_number)
                                    <span class="block text-[11px] font-mono text-gray-500 mt-1">Resi: {{ $order->tracking_number }}</span>
                                @endif
                            </td>

                            {{-- Action Buttons --}}
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="inline-flex items-center justify-center p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" 
                                   title="Lihat Detail Pesanan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">Belum ada data pesanan yang sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($orders->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection