@extends('layouts.admin')

@section('title', 'Kelola Linktree')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    {{-- Header & Notifikasi --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Navigation Linktree</h1>
            <p class="text-sm text-gray-500">Atur urutan, menu utama, sub-menu, dan tautan sosial media Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Menu
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            <p class="font-bold mb-1">Terjadi kesalahan validasi:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Batch Simpan --}}
    <form action="{{ route('admin.linktree.update-all') }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="space-y-4" id="linktree-container">
            @forelse($items as $item)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 text-gray-700" id="item-card-{{ $item->id }}">
                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {{-- Baris Utama Item --}}
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                            
                            {{-- Title --}}
                            <div class="md:col-span-4">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Menu</label>
                                <input type="text" 
                                       name="items[{{ $item->id }}][title]" 
                                       value="{{ old('items.'.$item->id.'.title', $item->title) }}" 
                                       required 
                                       class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            {{-- Tipe Menu (Parent / Single Link) --}}
                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe Menu</label>
                                <div class="flex items-center gap-4 py-1.5">
                                    <label class="inline-flex items-center text-xs text-gray-700 cursor-pointer">
                                        <input type="radio" 
                                               name="items[{{ $item->id }}][has_sub]" 
                                               value="0" 
                                               {{ !$item->has_sub ? 'checked' : '' }} 
                                               onchange="toggleParentMode('{{ $item->id }}', false)"
                                               class="text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                        <span class="ml-1.5">Single Link</span>
                                    </label>
                                    <label class="inline-flex items-center text-xs text-gray-700 cursor-pointer">
                                        <input type="radio" 
                                               name="items[{{ $item->id }}][has_sub]" 
                                               value="1" 
                                               {{ $item->has_sub ? 'checked' : '' }} 
                                               onchange="toggleParentMode('{{ $item->id }}', true)"
                                               class="text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                        <span class="ml-1.5">Parent Sub-menu</span>
                                    </label>
                                </div>
                            </div>

                            {{-- URL Input (Aktif jika Single Link) --}}
                            <div class="md:col-span-4 {{ $item->has_sub ? 'hidden' : '' }}" id="url-box-{{ $item->id }}">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">URL / Link Target</label>
                                <input type="url" 
                                       id="url-input-{{ $item->id }}"
                                       name="items[{{ $item->id }}][url]" 
                                       value="{{ $item->has_sub ? '' : old('items.'.$item->id.'.url', $item->url ?? 'https://') }}" 
                                       {{ $item->has_sub ? '' : 'required' }}
                                       placeholder="https://example.com"
                                       class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2 font-mono focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            {{-- Urutan/Order --}}
                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan</label>
                                <input type="number" 
                                       name="items[{{ $item->id }}][order]" 
                                       value="{{ old('items.'.$item->id.'.order', $item->order ?? 0) }}" 
                                       class="w-full border border-gray-300 rounded-lg text-xs px-2 py-2 text-center focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        {{-- Tombol Aksi Item Utama --}}
                        <div class="flex items-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-gray-100">
                            <button type="button" 
                                    id="btn-add-sub-{{ $item->id }}" 
                                    onclick="addSubMenu('{{ $item->id }}')" 
                                    class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-2.5 py-2 rounded-lg transition flex items-center gap-1 {{ !$item->has_sub ? 'hidden' : '' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Sub-menu
                            </button>
                            <button type="button" onclick="confirmDelete('{{ route('admin.linktree.destroy', $item->id) }}')" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-semibold p-2 rounded-lg transition" title="Hapus Menu">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Container Sub-Menu --}}
                    <div id="sub-wrapper-{{ $item->id }}" class="mt-4 pt-3 border-t border-dashed border-gray-200 pl-4 md:pl-8 space-y-3 {{ !$item->has_sub ? 'hidden' : '' }}">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Daftar Sub-Menu:</p>
                        <div id="sub-container-{{ $item->id }}" class="space-y-2">
                            @if($item->subs && count($item->subs) > 0)
                                @foreach($item->subs as $sub)
                                    <div class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-lg border border-gray-200" id="sub-item-{{ $sub->id }}">
                                        <input type="hidden" name="items[{{ $item->id }}][subs][{{ $sub->id }}][id]" value="{{ $sub->id }}">
                                        
                                        <div class="grid grid-cols-12 gap-2 flex-1">
                                            <div class="col-span-5">
                                                <input type="text" 
                                                       name="items[{{ $item->id }}][subs][{{ $sub->id }}][title]" 
                                                       value="{{ old('items.'.$item->id.'.subs.'.$sub->id.'.title', $sub->title) }}" 
                                                       placeholder="Judul Sub-menu" 
                                                       required 
                                                       class="w-full border border-gray-300 rounded-md text-xs px-2.5 py-1.5">
                                            </div>
                                            <div class="col-span-5">
                                                <input type="url" 
                                                       name="items[{{ $item->id }}][subs][{{ $sub->id }}][url]" 
                                                       value="{{ old('items.'.$item->id.'.subs.'.$sub->id.'.url', $sub->url ?? 'https://') }}" 
                                                       placeholder="https://example.com" 
                                                       required 
                                                       class="w-full border border-gray-300 rounded-md text-xs px-2.5 py-1.5 font-mono">
                                            </div>
                                            <div class="col-span-2">
                                                <input type="number" 
                                                       name="items[{{ $item->id }}][subs][{{ $sub->id }}][order]" 
                                                       value="{{ old('items.'.$item->id.'.subs.'.$sub->id.'.order', $sub->order ?? 0) }}" 
                                                       placeholder="Urutan" 
                                                       class="w-full border border-gray-300 rounded-md text-xs px-2.5 py-1.5 text-center">
                                            </div>
                                        </div>

                                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                    Belum ada menu Linktree yang dibuat. Klik tombol <strong>"Tambah Menu"</strong> di atas.
                </div>
            @endforelse
        </div>

        {{-- Floating/Fixed Bottom Bar untuk Simpan --}}
        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg hover:shadow-xl transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Modal Tambah Menu Baru --}}
<div id="addModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-800">Tambah Menu Utama Baru</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <form action="{{ route('admin.linktree.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Menu</label>
                <input type="text" name="title" required placeholder="Contoh: Website Resmi" class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe Menu</label>
                <select name="has_sub" id="modal_has_sub" onchange="toggleModalUrlInput(this.value)" class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2">
                    <option value="0">Single Link (Tautan Langsung)</option>
                    <option value="1">Parent Sub-menu (Punya Anak Menu)</option>
                </select>
            </div>

            <div id="modal_url_box">
                <label class="block text-xs font-semibold text-gray-600 mb-1">URL Target</label>
                <input type="url" name="url" id="modal_url_input" value="https://" placeholder="https://example.com" class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan</label>
                <input type="number" name="order" value="0" class="w-full border border-gray-300 rounded-lg text-xs px-3 py-2">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg">Tambah</button>
            </div>
        </form>
    </div>
</div>

{{-- Form Tersembunyi untuk Hapus --}}
<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    // 1. Toggle Parent vs Single Link Mode
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

    // 2. Tambah Baris Sub-menu Dinamis
    function addSubMenu(parentId) {
        const container = document.getElementById(`sub-container-${parentId}`);
        const tempId = 'new_' + Date.now();

        const subHtml = `
            <div class="flex items-center gap-2 bg-slate-50 p-2.5 rounded-lg border border-gray-200" id="sub-item-${tempId}">
                <input type="hidden" name="items[${parentId}][subs][${tempId}][id]" value="">
                
                <div class="grid grid-cols-12 gap-2 flex-1">
                    <div class="col-span-5">
                        <input type="text" 
                               name="items[${parentId}][subs][${tempId}][title]" 
                               placeholder="Judul Sub-menu" 
                               required 
                               class="w-full border border-gray-300 rounded-md text-xs px-2.5 py-1.5">
                    </div>
                    <div class="col-span-5">
                        <input type="url" 
                               name="items[${parentId}][subs][${tempId}][url]" 
                               value="https://" 
                               placeholder="https://example.com" 
                               required 
                               class="w-full border border-gray-300 rounded-md text-xs px-2.5 py-1.5 font-mono">
                    </div>
                    <div class="col-span-2">
                        <input type="number" 
                               name="items[${parentId}][subs][${tempId}][order]" 
                               value="0" 
                               placeholder="Urutan" 
                               class="w-full border border-gray-300 rounded-md text-xs px-2.5 py-1.5 text-center">
                    </div>
                </div>

                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', subHtml);
    }

    // 3. Modal Control
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function toggleModalUrlInput(val) {
        const box = document.getElementById('modal_url_box');
        const input = document.getElementById('modal_url_input');
        if (val === '1') {
            box.classList.add('hidden');
            input.removeAttribute('required');
        } else {
            box.classList.remove('hidden');
            input.setAttribute('required', 'required');
        }
    }

    // 4. Konfirmasi Hapus
    function confirmDelete(url) {
        if (confirm('Apakah Anda yakin ingin menghapus menu ini beserta seluruh anak menunya?')) {
            const form = document.getElementById('deleteForm');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection