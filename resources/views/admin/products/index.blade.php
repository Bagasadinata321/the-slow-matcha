@extends('layouts.admin')

@section('title', 'Daftar Produk Matcha')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog Produk Matcha</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola daftar produk, varian ukuran, harga, dan ketersediaan stok.</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-200">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    <!-- Alert Notification -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-6 shadow-sm">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Card Wrapper & Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Informasi Produk</th>
                        <th class="py-4 px-6">Varian & Harga</th>
                        <th class="py-4 px-6">Total Stok</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/50 transition-colors">

                        <!-- Column 1: Info & Cover Gambar -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden border border-slate-200 flex-shrink-0">
                                    @if($product->coverMedia)
                                    <img src="{{ asset('storage/' . $product->coverMedia->path) }}"
                                        alt="{{ $product->title }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-box text-lg"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="font-semibold text-slate-900 hover:text-emerald-600 transition-colors">
                                        {{ $product->title }}
                                    </h2>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Column 2: Varian & Harga Badge -->
                        <td class="py-4 px-6">
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->variants as $variant)
                                <div class="bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-lg text-xs space-x-1">
                                    <span class="font-bold text-slate-700">{{ $variant->gram_size }}g:</span>
                                    @if($variant->discount_price)
                                    <span class="line-through text-slate-400">
                                        Rp{{ number_format($variant->price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-emerald-600 font-semibold">
                                        Rp{{ number_format($variant->discount_price, 0, ',', '.') }}
                                    </span>
                                    @else
                                    <span class="text-slate-800 font-medium">
                                        Rp{{ number_format($variant->price, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>

                        <!-- Column 3: Total Stok -->
                        <td class="py-4 px-6">
                            @php $totalStock = $product->variants->sum('stock'); @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $totalStock > 0 ? 'bg-slate-100 text-slate-700' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                <i class="fa-solid fa-cubes text-slate-400 text-xs"></i>
                                {{ $totalStock }} pcs
                            </span>
                        </td>

                        <!-- Column 4: Tombol Aksi -->
                        <!-- Column 4: Tombol Aksi -->
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                <!-- Tombol Edit -->
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
                                    Edit
                                </a>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada data produk yang ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection