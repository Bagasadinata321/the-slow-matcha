@extends('layouts.admin')

@section('title', 'Detail Pelanggan - ' . $customer->name)

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customers.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h1>
                <p class="text-sm text-gray-500">Terdaftar sejak {{ $customer->created_at->translatedFormat('d F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Ringkasan Metrics (LTV & Orders) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Lifetime Value</p>
            <p class="text-2xl font-bold text-emerald-600 mt-2">
                Rp {{ number_format($customer->orders->sum('total_amount'), 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">
                {{ $customer->orders->count() }} Pesanan
            </p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Kontak Pelanggan</p>
            <p class="text-sm font-semibold text-gray-800 mt-2">{{ $customer->email }}</p>
            <p class="text-xs text-gray-500">{{ $customer->phone ?? 'Tidak ada nomor telp' }}</p>
        </div>
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">Riwayat Pesanan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Invoice</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-center">Status Pembayaran</th>
                        <th class="py-3 px-4 text-center">Status Pesanan</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($customer->orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-emerald-600">
                                {{ $order->invoice_number }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-gray-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-gray-600 hover:text-emerald-600 font-medium text-xs bg-gray-100 px-3 py-1.5 rounded-lg transition-colors">
                                    Lihat Pesanan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Pelanggan ini belum pernah melakukan transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection