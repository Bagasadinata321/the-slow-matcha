@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@push('styles')
<!-- Editor.js Custom Styles Override -->
<style>
    /* 1. Hilangkan/kurangi padding horizontal pada area editor */
    #editorjs .codex-editor__redactor {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-bottom: 30px !important;
    }

    /* 2. Buat isi blok dan toolbar menyesuaikan lebar penuh container */
    #editorjs .ce-block__content,
    #editorjs .ce-toolbar__content {
        max-width: 100% !important;
        margin-left: 50px !important;
        margin-right: 0 !important;
    }

    /* 3. Penataan visual khusus elemen Heading & Paragraf */
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
            <h1 class="text-2xl font-bold text-gray-900">Tambah Produk Baru</h1>
            <p class="text-xs text-gray-500 mt-0.5">Isi formulir di bawah ini untuk menambahkan produk matcha baru.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg transition-colors">
            &larr; Kembali
        </a>
    </div>

    <form id="product-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Nama Produk -->
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Nama Produk</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Ceremonial Grade Matcha" class="w-full border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 p-2.5 rounded-lg text-sm transition-all" required>
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
                            🍃
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
                            🍵
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-slate-900">Alat Pembuatan (Tools)</span>
                            <span class="block text-xs text-slate-500">Chasen (Pengocok), Chawan (Mangkuk), dll.</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Deskripsi Produk (Editor.js) -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-800 mb-1.5">Deskripsi Produk (Block Editor)</label>

            <div id="editorjs" class="border border-gray-300 rounded-lg p-4 bg-white min-h-[250px] shadow-sm"></div>

            <input type="hidden" name="description" id="description-input" value="{{ old('description') }}">
        </div>

        <!-- Cover Utama & Galeri Image Dropzone -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Cover Utama -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="block text-sm font-semibold text-gray-800 mb-0.5">Cover Utama Produk</label>
                <p class="text-xs text-gray-500 mb-3">Gambar utama yang tampil di kartu katalog depan.</p>

                <label for="cover-input" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-white hover:bg-emerald-50/50 hover:border-emerald-400 transition-all group">
                    <div class="flex flex-col items-center justify-center pt-2 pb-2">
                        <svg class="w-8 h-8 mb-1 text-gray-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-xs text-gray-600 font-medium"><span class="text-emerald-600 underline">Pilih foto cover</span> atau tarik file ke sini</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, WEBP (Max. 2MB)</p>
                    </div>
                    <input type="file" name="image" id="cover-input" class="hidden" accept="image/*" required>
                </label>

                <div class="mt-4 pt-3 border-t border-gray-200">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Preview Cover:</p>
                    <div id="cover-preview-container" class="inline-block relative">
                        <img id="cover-preview" class="w-24 h-24 object-cover rounded-lg border-2 border-white shadow-sm ring-1 ring-gray-200 hidden">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-xs text-gray-600 font-medium"><span class="text-emerald-600 underline">Tambah foto galeri</span> (bisa lebih dari 1)</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, WEBP (Bisa multiple file)</p>
                    </div>
                    <input type="file" name="gallery[]" id="gallery-input" class="hidden" accept="image/*" multiple>
                </label>

                <div class="mt-4 pt-3 border-t border-gray-200">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Preview Galeri:</p>
                    <div id="gallery-preview-grid" class="flex flex-wrap gap-2.5">
                        <p id="no-gallery-text" class="text-xs text-gray-400 italic">Belum ada foto galeri yang dipilih.</p>
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
            <!-- Varian Default 0 -->
            <div class="variant-item border border-gray-200 p-4 rounded-xl bg-gray-50 transition-all">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Ukuran (Gram)</label>
                        <input type="number" name="variants[0][gram_size]" class="w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="30" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Harga Normal (Rp)</label>
                        <input type="number" name="variants[0][price]" class="price-input w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="100000" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Stok</label>
                        <input type="number" name="variants[0][stock]" class="w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="50" required>
                    </div>
                    <div class="flex items-center justify-between pb-2">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" class="discount-toggle rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500 w-4 h-4">
                            <span class="ml-2 text-xs font-semibold text-gray-700">Aktifkan Diskon</span>
                        </label>
                    </div>
                </div>

                <!-- Container Diskon (Muncul hanya jika Checkbox dicentang) -->
                <div class="discount-container grid grid-cols-1 md:grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-200/60 hidden">
                    <div>
                        <label class="text-xs font-semibold text-emerald-700">Diskon (%)</label>
                        <input type="number" class="percent-input w-full border border-emerald-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 rounded-lg text-sm mt-1" placeholder="Contoh: 10" min="0" max="100">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-emerald-700">Harga Promo (Rp)</label>
                        <input type="number" name="variants[0][discount_price]" class="discount-input w-full border border-emerald-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 rounded-lg text-sm mt-1" placeholder="Harga setelah diskon">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-gray-100 flex justify-between items-center">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">Batal</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm">Simpan Produk</button>
        </div>
    </form>
</div>
@if ($errors->any())
    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg">
        <p class="font-bold mb-2">Gagal Menyimpan! Perbaiki error berikut:</p>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection

@push('scripts')
<!-- Editor.js Core & Plugins CDN -->
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script>

<script>
    // 1. Live Preview Cover File
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
        previewGrid.innerHTML = '';

        if (files.length > 0) {
            Array.from(files).forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'w-20 h-20 object-cover rounded-lg border-2 border-white shadow-sm ring-1 ring-gray-200';
                previewGrid.appendChild(img);
            });
        } else {
            previewGrid.innerHTML = '<p id="no-gallery-text" class="text-xs text-gray-400 italic">Belum ada foto galeri yang dipilih.</p>';
        }
    });

    // 3. Dynamic Variant Indexing
    let variantIndex = 1;

    document.getElementById('add-variant-btn')?.addEventListener('click', function() {
        const wrapper = document.getElementById('variant-wrapper');
        if (!wrapper) return;

        const newRow = document.createElement('div');
        newRow.className = 'variant-item border border-gray-200 p-4 rounded-xl bg-gray-50 transition-all';

        newRow.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Ukuran (Gram)</label>
                        <input type="number" name="variants[${variantIndex}][gram_size]" class="w-full border border-gray-300 p-2 rounded-lg text-sm mt-1" placeholder="50" required>
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
                        <input type="number" class="percent-input w-full border border-emerald-300 focus:ring-emerald-500 focus:border-emerald-500 p-2 rounded-lg text-sm mt-1" placeholder="Contoh: 10" min="0" max="100">
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

    // 4. Toggle Visibility Form Diskon & Reset Input ketika Uncheck
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
                percentInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }
        }
    });

    // 6. Inisialisasi Editor.js
    document.addEventListener('DOMContentLoaded', function() {
        const descriptionInput = document.getElementById('description-input');
        const initialDataInput = descriptionInput ? descriptionInput.value : '';
        let parsedData = {};

        try {
            parsedData = initialDataInput ? JSON.parse(initialDataInput) : {};
        } catch (e) {
            if (initialDataInput) {
                parsedData = {
                    blocks: [{
                        type: "paragraph",
                        data: {
                            text: initialDataInput
                        }
                    }]
                };
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
                productForm.submit(); // Jalankan submit asli setelah data tersimpan
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