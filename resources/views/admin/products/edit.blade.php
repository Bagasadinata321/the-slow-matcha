@extends('layouts.admin')

@php
    $isEdit = isset($product) && $product->exists;
    $title = $isEdit ? 'Edit Produk - ' . $product->title : 'Tambah Produk Baru';
    $actionUrl = $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store');
@endphp

@section('title', $title)

@push('styles')
<!-- Editor.js Custom Styles Override -->
<style>
    #editorjs .codex-editor__redactor {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-bottom: 30px !important;
    }

    #editorjs .ce-block__content,
    #editorjs .ce-toolbar__content {
        max-width: 100% !important;
        margin-left: 50px !important;
        margin-right: 0 !important;
    }

    #editorjs .ce-paragraph {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #374151;
    }

    #editorjs .ce-header {
        font-weight: 700 !important;
        color: #111827;
        margin-top: 0.75rem !important;
        margin-bottom: 0.5rem !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if($isEdit)
                    <span class="font-light">Edit Produk:</span> {{ $product->title }}
                @else
                    Tambah Produk Baru
                @endif
            </h1>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg transition-colors">
            &larr; Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
            <p class="text-sm font-bold text-red-800">Terjadi kesalahan pada input form:</p>
            <ul class="mt-1.5 list-disc list-inside text-xs text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="product-form" action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- Nama Produk -->
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $product->title ?? '') }}" class="w-full border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 p-2.5 rounded-lg text-sm transition-all" required>
        </div>

        <!-- Pilihan Jenis Produk (Radiobox Group) -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Jenis Produk <span class="text-red-500">*</span>
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Opsi 1: Matcha -->
                <label class="relative flex items-center p-4 rounded-xl border border-slate-200 cursor-pointer bg-white hover:bg-slate-50 transition-colors shadow-sm has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500">
                    <input type="radio"
                        name="product_type"
                        value="matcha"
                        class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500"
                        {{ old('product_type', $product->product_type ?? 'matcha') == 'matcha' ? 'checked' : '' }}>

                    <div class="ml-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            🍵
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-slate-900">Matcha (Bubuk / Olahan)</span>
                            <span class="block text-xs text-slate-500">Ceremonial Grade, Culinary, Latte, dll.</span>
                        </div>
                    </div>
                </label>

                <!-- Opsi 2: Alat Pembuatan (Tools) -->
                <label class="relative flex items-center p-4 rounded-xl border border-slate-200 cursor-pointer bg-white hover:bg-slate-50 transition-colors shadow-sm has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500">
                    <input type="radio"
                        name="product_type"
                        value="tool"
                        class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500"
                        {{ old('product_type', $product->product_type ?? '') == 'tool' ? 'checked' : '' }}>

                    <div class="ml-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            🥣
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-slate-900">Alat Pembuatan (Tools)</span>
                            <span class="block text-xs text-slate-500">Chasen (Pengocok), Chawan (Mangkuk), dll.</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Deskripsi Utama Produk (Editor.js) -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Deskripsi Produk (Block Editor)</label>
            <div id="editorjs" class="border border-gray-300 rounded-lg p-4 bg-white min-h-[250px] shadow-sm"></div>
            <input type="hidden" name="description" id="description-input" value="{{ old('description', isset($product->content) ? (is_string($product->content) ? $product->content : json_encode($product->content)) : (isset($product->description) ? (is_string($product->description) ? $product->description : json_encode($product->description)) : '')) }}">
        </div>

        <!-- FIELD: DETAIL & CARA PENYAJIAN -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Detail Produk -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">Detail Produk</label>
                <p class="text-xs text-gray-500 mb-2">Informasi spesifikasi seperti Origin, Grade, Shelf Life, dll.</p>
                <textarea name="details" rows="4" class="w-full border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 p-2.5 rounded-lg text-sm transition-all" placeholder="Origin: Uji, Kyoto&#10;Grade: Ceremonial Grade&#10;Shelf Life: 6 Bulan">{{ old('details', $product->details ?? '') }}</textarea>
            </div>

            <!-- Cara Penyajian -->
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-1.5">Cara Penyajian</label>
                <p class="text-xs text-gray-500 mb-2">Panduan langkah penyeduhan dan takaran.</p>
                <textarea name="serving_guide" rows="4" class="w-full border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 p-2.5 rounded-lg text-sm transition-all" placeholder="1. Ayak 2g matcha powder&#10;2. Tuang 70ml air hangat (80°C)&#10;3. Aduk dengan Chasen membentuk huruf W">{{ old('serving_guide', $product->serving_guide ?? '') }}</textarea>
            </div>
        </div>

        <!-- Cover Utama & Galeri Image Dropzone -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Cover Utama -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="block text-sm font-semibold text-gray-800 mb-0.5">Cover Utama Produk</label>
                <p class="text-xs text-gray-500 mb-3">{{ $isEdit ? 'Biarkan kosong jika tidak ingin mengganti cover utama.' : 'Pilih foto cover utama untuk produk ini.' }}</p>

                <label for="cover-input" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-white hover:bg-emerald-50/50 hover:border-emerald-400 transition-all group">
                    <div class="flex flex-col items-center justify-center pt-2 pb-2">
                        <svg class="w-8 h-8 mb-1 text-gray-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-xs text-gray-600 font-medium"><span class="text-emerald-600 underline">Pilih foto cover</span> atau tarik file ke sini</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, WEBP (Max. 2MB)</p>
                    </div>
                    <input type="file" name="image" id="cover-input" class="hidden" accept="image/*">
                </label>

                <div class="mt-4 pt-3 border-t border-gray-200">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Preview Cover:</p>
                    <div id="cover-preview-container" class="inline-block relative">
                        @if($isEdit && $product->coverMedia)
                            <img id="cover-preview" src="{{ asset('storage/' . $product->coverMedia->path) }}" class="w-24 h-24 object-cover rounded-lg border-2 border-white shadow-sm ring-1 ring-gray-200">
                        @else
                            <img id="cover-preview" class="w-24 h-24 object-cover rounded-lg border-2 border-white shadow-sm ring-1 ring-gray-200 hidden">
                        @endif
                    </div>
                </div>
            </div>

            <!-- Galeri Tambahan -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="block text-sm font-semibold text-gray-800 mb-0.5">Gambar Tambahan (Galeri)</label>
                <p class="text-xs text-gray-500 mb-3">Unggah beberapa foto sekaligus untuk melengkapi galeri.</p>

                <label for="gallery-input" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-white hover:bg-emerald-50/50 hover:border-emerald-400 transition-all group">
                    <div class="flex flex-col items-center justify-center pt-2 pb-2">
                        <svg class="w-8 h-8 mb-1 text-gray-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-xs text-gray-600 font-medium"><span class="text-emerald-600 underline">Tambah foto galeri</span> (bisa lebih dari 1)</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, WEBP (Bisa multiple file)</p>
                    </div>
                    <input type="file" name="gallery[]" id="gallery-input" class="hidden" accept="image/*" multiple>
                </label>

                <div class="mt-4 pt-3 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-xs font-semibold text-gray-600">Preview Galeri:</p>
                        @if($isEdit)
                            <button type="button" id="btn-delete-all-gallery" onclick="deleteAllGallery({{ $product->id }})" class="text-xs text-red-600 hover:text-red-700 hover:underline font-semibold transition-colors {{ ($product->galleryMedia && $product->galleryMedia->count() > 0) ? '' : 'hidden' }}">Hapus Semua</button>
                        @endif
                    </div>

                    <div id="gallery-preview-grid" class="flex flex-wrap gap-2.5">
                        @if($isEdit && $product->galleryMedia && $product->galleryMedia->count() > 0)
                            @foreach($product->galleryMedia as $galleryImg)
                            <div class="relative group gallery-item" id="gallery-item-{{ $galleryImg->id }}">
                                <img src="{{ asset('storage/' . $galleryImg->path) }}" class="w-20 h-20 object-cover rounded-lg border-2 border-white shadow-sm ring-1 ring-gray-200">
                                <button type="button" onclick="deleteSingleGallery({{ $galleryImg->id }})" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 shadow-md transition-all transform hover:scale-110" title="Hapus foto ini">
                                    &times;
                                </button>
                            </div>
                            @endforeach
                        @else
                            <p id="no-gallery-text" class="text-xs text-gray-400 italic">Belum ada foto galeri.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        <!-- Area Varian Gram & Kalkulasi Harga Promo -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800">Varian Gram & Harga</h2>
            <button type="button" id="add-variant-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors">+ Tambah Varian</button>
        </div>

        <div id="variant-wrapper" class="space-y-4">
            @php
                $defaultVariants = [
                    ['variant_name' => '', 'price' => '', 'stock' => '', 'discount_price' => '']
                ];
                $variantsData = old('variants', $isEdit && $product->variants ? $product->variants->toArray() : $defaultVariants);
            @endphp

            @foreach($variantsData as $index => $variant)
            @php
                $price = (float)($variant['price'] ?? 0);
                $promoPrice = $variant['promo_price'] ?? $variant['discount_price'] ?? null;
                $hasDiscount = !is_null($promoPrice) && (float)$promoPrice > 0 && (float)$promoPrice < $price;
                $percent = ($price > 0 && $hasDiscount) ? (($price - (float)$promoPrice) / $price) * 100 : '';
                $variantId = $variant['id'] ?? null;
            @endphp
            <div class="variant-item border border-gray-200 p-4 rounded-xl bg-gray-50 transition-all">
                @if($variantId)
                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variantId }}">
                @endif
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Ukuran (Gram/Unit)</label>
                        <input type="text" 
                               name="variants[{{ $index }}][variant_name]" 
                               value="{{ $variant['variant_name'] ?? $variant['gram_size'] ?? '' }}" 
                               class="w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" 
                               placeholder="Contoh: 50g / Basic / Premium" 
                               required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Harga Normal (Rp)</label>
                        <input type="number" name="variants[{{ $index }}][price]" value="{{ $price > 0 ? (int)$price : '' }}" class="price-input w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="150000" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Stok</label>
                        <input type="number" name="variants[{{ $index }}][stock]" value="{{ $variant['stock'] ?? '' }}" class="w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="10" required>
                    </div>
                    <div class="flex items-center justify-between pb-2">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" class="discount-toggle rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500 w-4 h-4" {{ $hasDiscount ? 'checked' : '' }}>
                            <span class="ml-2 text-xs font-semibold text-gray-700">Aktifkan Diskon</span>
                        </label>
                        @if($index > 0)
                        <button type="button" class="remove-variant-btn bg-red-500 hover:bg-red-600 text-white px-2.5 py-1 rounded-lg text-xs transition-colors">Hapus</button>
                        @endif
                    </div>
                </div>

                <!-- Container Diskon -->
                <div class="discount-container grid grid-cols-1 md:grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-200/60 {{ $hasDiscount ? '' : 'hidden' }}">
                    <div>
                        <label class="text-xs font-semibold text-emerald-700">Diskon (%)</label>
                        <input type="number" step="0.1" value="{{ $percent ? number_format((float)$percent, 1, '.', '') : '' }}" class="percent-input w-full border border-emerald-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 rounded-lg text-sm mt-1" placeholder="Contoh: 10" min="0" max="100">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-emerald-700">Harga Promo (Rp)</label>
                        <input type="number" name="variants[{{ $index }}][discount_price]" value="{{ $hasDiscount ? (int)$promoPrice : '' }}" class="discount-input w-full border border-emerald-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 rounded-lg text-sm mt-1" placeholder="Harga setelah diskon">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 pt-4 border-t border-gray-100 flex justify-between items-center">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">Batal</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm">
                {{ $isEdit ? 'Perbarui Produk' : 'Simpan Produk' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Editor.js Core & Plugins CDN -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>

<script>
    const rawDescriptionData = @json(old('description', $product->content ?? $product->description ?? ''));

    // 1. Live Preview Cover File Baru
    document.getElementById('cover-input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewImg = document.getElementById('cover-preview');

        if (file && previewImg) {
            previewImg.src = URL.createObjectURL(file);
            previewImg.classList.remove('hidden');
        }
    });

    // 2. Live Preview Galeri File Baru
    document.getElementById('gallery-input')?.addEventListener('change', function(e) {
        const files = e.target.files;
        const previewGrid = document.getElementById('gallery-preview-grid');

        if (!previewGrid) return;

        if (files.length > 0) {
            const noGalleryText = document.getElementById('no-gallery-text');
            if (noGalleryText) noGalleryText.remove();

            Array.from(files).forEach(file => {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'relative new-gallery-preview';
                imgContainer.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-20 h-20 object-cover rounded-lg border-2 border-emerald-400 shadow-sm opacity-90">`;
                previewGrid.appendChild(imgContainer);
            });
        }
    });

    // 3. Dynamic Variant Indexing
    let variantIndex = {{ count($variantsData ?? []) }};

    document.getElementById('add-variant-btn')?.addEventListener('click', function() {
        const wrapper = document.getElementById('variant-wrapper');
        if (!wrapper) return;

        const newRow = document.createElement('div');
        newRow.className = 'variant-item border border-gray-200 p-4 rounded-xl bg-gray-50 transition-all';

        newRow.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="text-xs font-semibold text-gray-700">Ukuran (Gram/Unit)</label>
                    <input type="text" name="variants[${variantIndex}][variant_name]" class="w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="Contoh: 50g / Basic" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Harga Normal (Rp)</label>
                    <input type="number" name="variants[${variantIndex}][price]" class="price-input w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="150000" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Stok</label>
                    <input type="number" name="variants[${variantIndex}][stock]" class="w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="20" required>
                </div>
                <div class="flex items-center justify-between pb-2">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" class="discount-toggle rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500 w-4 h-4">
                        <span class="ml-2 text-xs font-semibold text-gray-700">Aktifkan Diskon</span>
                    </label>
                    <button type="button" class="remove-variant-btn bg-red-500 hover:bg-red-600 text-white px-2.5 py-1 rounded-lg text-xs transition-colors">Hapus</button>
                </div>
            </div>

            <div class="discount-container grid grid-cols-1 md:grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-200/60 hidden">
                <div>
                    <label class="text-xs font-semibold text-emerald-700">Diskon (%)</label>
                    <input type="number" step="0.1" class="percent-input w-full border border-emerald-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 rounded-lg text-sm mt-1" placeholder="Contoh: 10" min="0" max="100">
                </div>
                <div>
                    <label class="text-xs font-semibold text-emerald-700">Harga Promo (Rp)</label>
                    <input type="number" name="variants[${variantIndex}][discount_price]" class="discount-input w-full border border-emerald-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 rounded-lg text-sm mt-1" placeholder="Harga setelah diskon">
                </div>
            </div>
        `;

        wrapper.appendChild(newRow);
        variantIndex++;
    });

    // Hapus Baris Varian
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant-btn')) {
            e.target.closest('.variant-item').remove();
        }
    });

    // 4. Toggle Visibility Form Diskon
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('discount-toggle')) {
            const item = e.target.closest('.variant-item');
            const discountContainer = item.querySelector('.discount-container');
            const percentInput = item.querySelector('.percent-input');
            const discountInput = item.querySelector('.discount-input');

            if (e.target.checked) {
                discountContainer.classList.remove('hidden');
            } else {
                discountContainer.classList.add('hidden');
                percentInput.value = '';
                discountInput.value = '';
            }
        }
    });

    // 5. Kalkulasi Promo Dua Arah
    document.addEventListener('input', function(e) {
        const container = e.target.closest('.variant-item');
        if (!container) return;

        const toggle = container.querySelector('.discount-toggle');
        if (!toggle || !toggle.checked) return;

        const priceInput = container.querySelector('.price-input');
        const percentInput = container.querySelector('.percent-input');
        const discountInput = container.querySelector('.discount-input');

        const price = parseFloat(priceInput.value) || 0;

        if (e.target.classList.contains('percent-input')) {
            const percent = parseFloat(percentInput.value) || 0;
            if (price > 0 && percent >= 0) {
                const finalPrice = price - (price * (percent / 100));
                discountInput.value = Math.round(finalPrice);
            } else {
                discountInput.value = '';
            }
        }

        if (e.target.classList.contains('discount-input')) {
            const discountPrice = parseFloat(discountInput.value) || 0;
            if (price > 0 && discountPrice > 0 && discountPrice < price) {
                const calculatedPercent = ((price - discountPrice) / price) * 100;
                percentInput.value = calculatedPercent.toFixed(1);
            } else if (!discountInput.value) {
                percentInput.value = '';
            }
        }

        if (e.target.classList.contains('price-input')) {
            if (percentInput.value) {
                percentInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    });

    // 6. AJAX Hapus Seluruh Galeri
    function deleteAllGallery(productId) {
        if (!confirm('Apakah Anda yakin ingin menghapus semua gambar galeri untuk produk ini?')) {
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`/admin/products/${productId}/media/gallery`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const grid = document.getElementById('gallery-preview-grid');
                grid.innerHTML = '<p id="no-gallery-text" class="text-xs text-gray-400 italic">Belum ada foto galeri.</p>';
                document.getElementById('btn-delete-all-gallery')?.classList.add('hidden');
            } else {
                alert(data.message || 'Gagal menghapus galeri.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    // 7. AJAX Hapus Gambar Galeri Satuan
    function deleteSingleGallery(mediaId) {
        if (!confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`/admin/media/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = document.getElementById(`gallery-item-${mediaId}`);
                if (item) {
                    item.style.transition = 'all 0.2s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.remove();
                        const remainingItems = document.querySelectorAll('.gallery-item');
                        if (remainingItems.length === 0) {
                            const grid = document.getElementById('gallery-preview-grid');
                            grid.innerHTML = '<p id="no-gallery-text" class="text-xs text-gray-400 italic">Belum ada foto galeri.</p>';
                            document.getElementById('btn-delete-all-gallery')?.classList.add('hidden');
                        }
                    }, 200);
                }
            } else {
                alert('Gagal menghapus gambar: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    // 8. Inisialisasi Editor.js & Fix Event Submit
    document.addEventListener('DOMContentLoaded', function() {
        let parsedData = {};

        if (rawDescriptionData) {
            if (typeof rawDescriptionData === 'object') {
                parsedData = rawDescriptionData;
            } else {
                try {
                    parsedData = JSON.parse(rawDescriptionData);
                } catch (e) {
                    parsedData = {
                        blocks: [{
                            type: "paragraph",
                            data: { text: rawDescriptionData }
                        }]
                    };
                }
            }
        }

        const editor = new EditorJS({
            holder: 'editorjs',
            placeholder: 'Tuliskan deskripsi produk di sini...',
            data: parsedData,
            tools: {
                header: {
                    class: Header,
                    inlineToolbar: ['link'],
                    config: {
                        placeholder: 'Masukkan Heading...',
                        levels: [1, 2, 3, 4],
                        defaultLevel: 2
                    }
                },
                image: {
                    class: ImageTool,
                    config: {
                        endpoints: {
                            byFile: "{{ route('admin.products.upload-editor-image') }}"
                        },
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        field: 'image',
                        types: 'image/*'
                    }
                },
                list: {
                    class: EditorjsList,
                    inlineToolbar: true,
                    config: {
                        defaultStyle: 'unordered'
                    }
                },
                quote: {
                    class: Quote,
                    inlineToolbar: true
                },
                warning: {
                    class: Warning,
                    inlineToolbar: true
                },
                delimiter: Delimiter
            }
        });

        const productForm = document.getElementById('product-form');
        if (productForm) {
            productForm.addEventListener('submit', function(e) {
                if (!productForm.dataset.editorSaved) {
                    e.preventDefault();
                    editor.save().then((outputData) => {
                        document.getElementById('description-input').value = JSON.stringify(outputData);
                        productForm.dataset.editorSaved = "true";
                        productForm.submit();
                    }).catch((error) => {
                        console.error('Saving failed: ', error);
                        alert('Gagal memproses data deskripsi!');
                    });
                }
            });
        }
    });
</script>
@endpush