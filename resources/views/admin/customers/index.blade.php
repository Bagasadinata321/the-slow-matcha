@extends('layouts.admin')

@section('title', 'Kelola Pelanggan')

@section('content')
<div class="p-6 space-y-6">

    {{-- Alert Notification Modern --}}
@if(session('success'))
    <div id="alert-success" class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl shadow-sm transition-all duration-300">
        <div class="flex items-center gap-3.5">
            {{-- Icon Badge --}}
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-600 text-white shrink-0 shadow-md shadow-emerald-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            {{-- Text Content --}}
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800">Berhasil!</h4>
                <p class="text-sm font-medium text-emerald-700 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>

        {{-- Close Button --}}
        <button type="button" onclick="document.getElementById('alert-success').remove()" class="p-1.5 text-emerald-500 hover:text-emerald-800 hover:bg-emerald-100 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Pelanggan</h1>
            <p class="text-sm text-gray-500">Kelola informasi pelanggan, status akses, dan pantau LTV.</p>
        </div>
        
        {{-- Tombol Ekspor CSV --}}
        <a href="{{ route('admin.customers.export', request()->query()) }}" class="inline-flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Ekspor Data (CSV)
        </a>
    </div>

    {{-- Filter & Search Form --}}
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Cari nama, email, atau HP..." 
                class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">

            <select name="tier" class="border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-600 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Tier LTV</option>
                <option value="vip" {{ request('tier') === 'vip' ? 'selected' : '' }}>Pelanggan VIP (> Rp 400k)</option>
                <option value="regular" {{ request('tier') === 'regular' ? 'selected' : '' }}>Pelanggan Regular</option>
            </select>

            <select name="status" class="border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-600 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Status Akun</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Diblokir</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium px-3 py-2 rounded-lg text-sm transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4 text-center">Status Akun</th>
                        <th class="py-3 px-4 text-center">Total Pesanan</th>
                        <th class="py-3 px-4 text-right">Lifetime Value</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($customers as $customer)
                        @php $ltv = $customer->orders_sum_total_amount ?? 0; @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-gray-800">
                                <div class="flex items-center gap-2">
                                    <span>{{ $customer->name }}</span>
                                    @if($ltv >= 400000)
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200" title="Pelanggan Setia">VIP</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 text-xs">
                                <p>{{ $customer->email }}</p>
                                <p class="text-gray-400">{{ $customer->phone ?? '-' }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($customer->status)
                                    <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-semibold">Aktif</span>
                                @else
                                    <span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-full text-xs font-semibold">Diblokir</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                    {{ $customer->orders_count }} Pesanan
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-gray-900">
                                Rp {{ number_format($ltv, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-emerald-600 hover:text-emerald-800 font-medium text-xs bg-emerald-50 px-3 py-1.5 rounded-lg transition-colors">
                                        Detail
                                    </a>

                                    {{-- Form Block / Unblock --}}
                                    <form action="{{ route('admin.customers.toggle-status', $customer->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun pelanggan ini?')">
                                        @csrf
                                        @method('PATCH')
                                        @if($customer->status)
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors" title="Blokir Akun">
                                                Blokir
                                            </button>
                                        @else
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-800 font-medium text-xs bg-emerald-50 px-2.5 py-1.5 rounded-lg transition-colors" title="Buka Blokir">
                                                Aktifkan
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Data pelanggan tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $customers->links() }}
        </div>
    </div>

</div>
@endsection