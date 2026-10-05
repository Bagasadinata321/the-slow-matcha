@extends('layouts.admin')

@section('title', 'Pengaturan Menu Linktree')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<div class="p-6 space-y-6">

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div id="alert-success" class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-4 text-emerald-800 text-sm">
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 bg-emerald-600 text-white rounded-md flex items-center justify-center font-bold text-xs">✓</span>
                <span class="font-medium text-emerald-900">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('alert-success').remove()" class="text-emerald-700 font-bold px-2">✕</button>
        </div>
    @endif

    {{-- ALERT ERROR VALIDATION --}}
    @if($errors->any())
        <div id="alert-error" class="p-4 bg-rose-50 border border-rose-200 rounded-xl mb-4 text-rose-800 text-sm space-y-1">
            <div class="font-bold flex items-center justify-between">
                <span>Terjadi kesalahan pada input data:</span>
                <button type="button" onclick="document.getElementById('alert-error').remove()" class="text-rose-700 font-bold px-2">✕</button>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Menu Builder Linktree</h1>
            <p class="text-sm text-gray-500">Atur struktur menu, icon, dan tautan secara sekaligus dalam satu halaman.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- FORM TAMBAH MENU UTAMA BARU (Route: admin.linktree.store) --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs space-y-4 sticky top-6">
                <div class="border-b pb-3">
                    <h2 class="text-base font-bold text-gray-800">1. Tambah Menu Utama</h2>
                    <p class="text-xs text-gray-400">Buat item menu utama baru ke dalam daftar.</p>
                </div>

                <form action="{{ route('admin.linktree.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama / Label Menu <span class="text-red-500">*</span></label>
                        <input type="text" name="label" value="{{ old('label') }}" placeholder="Contoh: Website Utama / Shop" required
                            class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium px-4 py-2.5 rounded-xl text-sm transition-colors shadow-xs">
                        + Tambahkan ke Menu
                    </button>
                </form>
            </div>
        </div>

        {{-- FORM SIMPAN BATCH / UPDATE ALL (Route: admin.linktree.update-all) --}}
        <div class="lg:col-span-8 space-y-4">
            <form action="{{ route('admin.linktree.update-all') }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs space-y-4">
                    <div class="border-b pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-gray-800">2. Kelola & Lengkapi Menu</h2>
                            <p class="text-xs text-gray-400">Lengkapi icon & URL menu/sub-menu, lalu klik Simpan Perubahan di bawah.</p>
                        </div>
                    </div>

                    {{-- List Parent / Menu Utama --}}
                    <div id="sortable-menu-list" class="space-y-3">
                        @forelse($linktreeItems as $item)
                            <div data-id="{{ $item->id }}" class="menu-item-block border border-gray-200 rounded-xl bg-white shadow-2xs overflow-hidden">
                                
                                {{-- Hidden ID Item --}}
                                <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">

                                {{-- Header Card --}}
                                <div class="p-3.5 bg-gray-50 flex items-center justify-between border-b border-gray-100 select-none">
                                    <div class="flex items-center gap-3">
                                        <span class="drag-handle cursor-grab text-gray-400 hover:text-gray-700 px-1 text-lg">⋮⋮</span>
                                        <span class="text-base">{{ $item->has_sub ? '📁' : '🔗' }}</span>
                                        <div>
                                            <span class="font-bold text-gray-800 text-sm block" id="label-display-{{ $item->id }}">{{ $item->label }}</span>
                                            <span class="text-[11px] text-gray-400 font-mono">
                                                {{ $item->has_sub ? 'Parent Menu (Tidak Menggunakan Link Direct)' : ($item->url ?? 'Belum ada URL') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="toggleAccordion('detail-{{ $item->id }}')" class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-lg text-xs font-semibold flex items-center gap-1 transition-colors">
                                            <span>Lengkapi / Edit</span>
                                            <span class="text-[10px]">▼</span>
                                        </button>

                                        <button type="button" onclick="deleteMenu('{{ $item->id }}')" class="text-red-400 hover:text-red-600 p-1 text-xs font-bold" title="Hapus Menu">✕</button>
                                    </div>
                                </div>

                                {{-- PANEL EDIT ITEM UTAMA --}}
                                <div id="detail-{{ $item->id }}" class="hidden p-4 bg-slate-50/50 border-b border-gray-200 space-y-4">
                                    
                                    {{-- BOX PARENT & TOMBOL TAMBAH SUB-MENU --}}
                                    <div class="p-3.5 bg-amber-50/80 border border-amber-200/80 rounded-xl">
                                        <div class="flex items-center justify-between">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="hidden" name="items[{{ $item->id }}][has_sub]" value="0">
                                                <input type="checkbox" id="checkbox-has-sub-{{ $item->id }}" name="items[{{ $item->id }}][has_sub]" value="1" {{ old('items.'.$item->id.'.has_sub', $item->has_sub) ? 'checked' : '' }}
                                                    onchange="toggleParentMode('{{ $item->id }}', this.checked)"
                                                    class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                                                <span class="text-xs font-bold text-amber-900">Jadikan Parent (Memiliki Sub-Menu)</span>
                                            </label>

                                            <button type="button" id="btn-add-sub-{{ $item->id }}" 
                                                onclick="addDynamicSubMenu('{{ $item->id }}')" 
                                                class="{{ $item->has_sub ? '' : 'hidden' }} px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-colors shadow-2xs">
                                                + Tambah Sub-Menu
                                            </button>
                                        </div>
                                        <p class="text-[11px] text-amber-700 pl-6 mt-1">Jika dicentang, menu ini hanya sebagai pembungkus dropdown dan tidak memiliki URL langsung.</p>
                                    </div>

                                    {{-- FIELD NAMA & ICON MENU UTAMA --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">NAMA / LABEL MENU <span class="text-red-500">*</span></label>
                                            <input type="text" name="items[{{ $item->id }}][label]" value="{{ old('items.'.$item->id.'.label', $item->label) }}" required
                                                oninput="document.getElementById('label-display-{{ $item->id }}').innerText = this.value"
                                                class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2 bg-white font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">PILIH ICON MENU</label>
                                            @php $currentIcon = old('items.'.$item->id.'.icon', $item->icon); @endphp
                                            <select name="items[{{ $item->id }}][icon]" class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2 bg-white focus:ring-emerald-500 focus:border-emerald-500">
                                                <option value="link" {{ $currentIcon == 'link' ? 'selected' : '' }}>🔗 Default Link</option>
                                                <option value="shopping-bag" {{ $currentIcon == 'shopping-bag' ? 'selected' : '' }}>🛍️ E-Commerce</option>
                                                <option value="instagram" {{ $currentIcon == 'instagram' ? 'selected' : '' }}>📸 Instagram</option>
                                                <option value="tiktok" {{ $currentIcon == 'tiktok' ? 'selected' : '' }}>🎵 TikTok</option>
                                                <option value="whatsapp" {{ $currentIcon == 'whatsapp' ? 'selected' : '' }}>💬 WhatsApp Chat</option>
                                                <option value="globe" {{ $currentIcon == 'globe' ? 'selected' : '' }}>🌐 Website / Portal</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- FIELD URL MENU UTAMA --}}
                                    <div id="url-box-{{ $item->id }}" class="{{ $item->has_sub ? 'hidden' : '' }}">
                                        <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">URL / LINK TUJUAN <span class="text-red-500">*</span></label>
                                        <input type="url" id="url-input-{{ $item->id }}" name="items[{{ $item->id }}][url]" value="{{ old('items.'.$item->id.'.url', $item->url ?? 'https://') }}" {{ $item->has_sub ? '' : 'required' }}
                                            placeholder="https://example.com"
                                            class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2 bg-white font-mono focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                </div>

                                {{-- CONTAINER SUB-MENU --}}
                                @php $children = $item->subItems ?? $item->children ?? $item->subs; @endphp
                                <div id="sub-wrapper-{{ $item->id }}" class="{{ $item->has_sub ? '' : 'hidden' }} p-3 pl-6 bg-gray-100/70 border-t border-gray-200">
                                    <div class="text-[10px] font-bold uppercase text-gray-500 tracking-wider mb-2">Daftar Sub-Menu:</div>
                                    
                                    <div id="sub-sortable-{{ $item->id }}" class="sub-sortable-container space-y-2">
                                        @if($children && count($children) > 0)
                                            @foreach($children as $child)
                                                <div data-id="{{ $child->id }}" class="sub-item-block border border-gray-200 rounded-xl bg-white overflow-hidden shadow-2xs">
                                                    <input type="hidden" name="subs[{{ $child->id }}][id]" value="{{ $child->id }}">
                                                    
                                                    <div class="p-3 bg-white space-y-3">
                                                        <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                                            <div class="flex items-center gap-2">
                                                                <span class="sub-drag-handle cursor-grab text-gray-300 hover:text-gray-600 text-sm">⋮⋮</span>
                                                                <span class="text-emerald-600 font-bold text-xs">↳ Sub-Menu</span>
                                                            </div>
                                                            <button type="button" onclick="deleteMenu('{{ $child->id }}')" class="text-red-400 hover:text-red-600 text-xs px-1 font-semibold">✕ Hapus</button>
                                                        </div>

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                            <div>
                                                                <label class="block text-[10px] font-semibold text-gray-600 uppercase mb-1">Nama Sub-Menu <span class="text-red-500">*</span></label>
                                                                <input type="text" name="subs[{{ $child->id }}][label]" value="{{ old('subs.'.$child->id.'.label', $child->label) }}" required
                                                                    class="w-full border border-gray-300 rounded text-xs px-2.5 py-1.5 bg-white font-bold text-gray-900 focus:ring-emerald-500">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-semibold text-gray-600 uppercase mb-1">Icon Sub-Menu</label>
                                                                @php $childIcon = old('subs.'.$child->id.'.icon', $child->icon); @endphp
                                                                <select name="subs[{{ $child->id }}][icon]" class="w-full border border-gray-300 rounded text-xs px-2 py-1.5 bg-white focus:ring-emerald-500">
                                                                    <option value="link" {{ $childIcon == 'link' ? 'selected' : '' }}>🔗 Default Link</option>
                                                                    <option value="shopping-bag" {{ $childIcon == 'shopping-bag' ? 'selected' : '' }}>🛍️ E-Commerce</option>
                                                                    <option value="instagram" {{ $childIcon == 'instagram' ? 'selected' : '' }}>📸 Instagram</option>
                                                                    <option value="tiktok" {{ $childIcon == 'tiktok' ? 'selected' : '' }}>🎵 TikTok</option>
                                                                    <option value="whatsapp" {{ $childIcon == 'whatsapp' ? 'selected' : '' }}>💬 WhatsApp</option>
                                                                    <option value="globe" {{ $childIcon == 'globe' ? 'selected' : '' }}>🌐 Website / Link</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-[10px] font-semibold text-gray-600 uppercase mb-1">URL / Link Tujuan Sub-Menu <span class="text-red-500">*</span></label>
                                                            <input type="url" name="subs[{{ $child->id }}][url]" value="{{ old('subs.'.$child->id.'.url', $child->url ?? 'https://') }}" required
                                                                placeholder="https://tokopedia.com/toko-anda"
                                                                class="w-full border border-gray-300 rounded text-xs px-2.5 py-1.5 bg-white font-mono focus:ring-emerald-500">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @empty
                            <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                                <p class="text-xs text-gray-400">Belum ada item menu. Tambahkan item baru di sebelah kiri.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- TOMBOL SIMPAN SEMUA KANAN BAWAH --}}
                    @if($linktreeItems->count() > 0)
                        <div class="pt-4 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-colors shadow-md flex items-center gap-2">
                                <span>💾</span>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    @endif

                </div>
            </form>
        </div>

    </div>
</div>

{{-- FORM DELETE GLOBAL --}}
<form id="global-action-form" action="" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="_method" id="global-action-method" value="POST">
</form>

<script>
    let newSubCounter = 0;

    function toggleAccordion(elementId) {
        const target = document.getElementById(elementId);
        if (target) {
            target.classList.toggle('hidden');
        }
    }

    function toggleParentMode(itemId, isParent) {
        const urlBox = document.getElementById(`url-box-${itemId}`);
        const btnAddSub = document.getElementById(`btn-add-sub-${itemId}`);
        const subWrapper = document.getElementById(`sub-wrapper-${itemId}`);
        const inputUrl = document.getElementById(`url-input-${itemId}`);

        if (isParent) {
            if (urlBox) urlBox.classList.add('hidden');
            if (btnAddSub) btnAddSub.classList.remove('hidden');
            if (subWrapper) subWrapper.classList.remove('hidden');
            if (inputUrl) {
                inputUrl.removeAttribute('required');
                inputUrl.value = '';
            }
        } else {
            if (urlBox) urlBox.classList.remove('hidden');
            if (btnAddSub) btnAddSub.classList.add('hidden');
            if (subWrapper) subWrapper.classList.add('hidden');
            if (inputUrl) {
                inputUrl.setAttribute('required', 'required');
                if (!inputUrl.value.trim()) inputUrl.value = 'https://';
            }
        }
    }

    function addDynamicSubMenu(parentId) {
        const checkbox = document.getElementById(`checkbox-has-sub-${parentId}`);
        if (checkbox && !checkbox.checked) {
            checkbox.checked = true;
            toggleParentMode(parentId, true);
        }

        newSubCounter++;
        const tempId = `new_${parentId}_${newSubCounter}`;
        const container = document.getElementById(`sub-sortable-${parentId}`);

        if (!container) return;

        const html = `
            <div id="sub-card-${tempId}" class="sub-item-block border-2 border-dashed border-emerald-300 rounded-xl bg-emerald-50/30 overflow-hidden p-3 space-y-3">
                <input type="hidden" name="new_subs[${tempId}][parent_id]" value="${parentId}">
                
                <div class="flex items-center justify-between border-b border-emerald-100 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="sub-drag-handle cursor-grab text-emerald-600 font-bold text-xs">⋮⋮ ✨ Sub-Menu Baru</span>
                    </div>
                    <button type="button" onclick="document.getElementById('sub-card-${tempId}').remove()" class="text-red-500 hover:text-red-700 text-xs font-semibold">✕ Batal</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-600 uppercase mb-1">Nama Sub-Menu <span class="text-red-500">*</span></label>
                        <input type="text" name="new_subs[${tempId}][label]" placeholder="Contoh: Tokopedia" required
                            class="w-full border border-gray-300 rounded text-xs px-2.5 py-1.5 bg-white font-bold text-gray-900 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-600 uppercase mb-1">Icon Sub-Menu</label>
                        <select name="new_subs[${tempId}][icon]" class="w-full border border-gray-300 rounded text-xs px-2 py-1.5 bg-white focus:ring-emerald-500">
                            <option value="link">🔗 Default Link</option>
                            <option value="shopping-bag">🛍️ E-Commerce</option>
                            <option value="instagram">📸 Instagram</option>
                            <option value="tiktok">🎵 TikTok</option>
                            <option value="whatsapp">💬 WhatsApp</option>
                            <option value="globe">🌐 Website / Link</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-600 uppercase mb-1">URL / Link Tujuan Sub-Menu <span class="text-red-500">*</span></label>
                    <input type="url" name="new_subs[${tempId}][url]" value="https://" required
                        placeholder="https://tokopedia.com/toko-anda"
                        class="w-full border border-gray-300 rounded text-xs px-2.5 py-1.5 bg-white font-mono focus:ring-emerald-500">
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
    }

    function deleteMenu(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus menu ini?')) return;

        const form = document.getElementById('global-action-form');
        document.getElementById('global-action-method').value = 'DELETE';
        
        let deleteUrl = "{{ route('admin.linktree.destroy', ':id') }}";
        form.action = deleteUrl.replace(':id', id);
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const reorderUrl = '{{ route("admin.linktree.reorder") }}';

        // 1. Drag & Drop Menu Utama
        const mainContainer = document.getElementById('sortable-menu-list');
        if (mainContainer) {
            new Sortable(mainContainer, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () {
                    saveOrder(mainContainer, reorderUrl);
                }
            });
        }

        // 2. Drag & Drop Sub-Menu
        document.querySelectorAll('.sub-sortable-container').forEach(container => {
            new Sortable(container, {
                handle: '.sub-drag-handle',
                animation: 150,
                onEnd: function () {
                    saveOrder(container, reorderUrl);
                }
            });
        });
    });

    function saveOrder(container, url) {
        const itemElements = container.querySelectorAll(':scope > .menu-item-block, :scope > .sub-item-block');
        const order = Array.from(itemElements)
            .map(el => el.getAttribute('data-id'))
            .filter(id => id && !id.startsWith('new_'));

        if (order.length > 0) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(res => res.json())
            .then(data => {
                console.log("Urutan berhasil disimpan:", data);
            })
            .catch(err => console.error("Reorder Error:", err));
        }
    }
</script>
@endsection