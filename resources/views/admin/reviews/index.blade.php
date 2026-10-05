@extends('layouts.admin')

@section('title', 'Kelola Ulasan & Rating')

@section('content')
<div class="p-6 space-y-6" x-data="{ replyModal: false, selectedReview: null, replyText: '' }">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ulasan & Rating Pelanggan</h1>
            <p class="text-sm text-gray-500">Moderasi ulasan produk, balasan penjual, dan visibilitas rating.</p>
        </div>
    </div>

    {{-- Search & Filter Section --}}
    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            
            {{-- Search Bar --}}
            <div class="md:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari Produk, Invoice, Nama Pelanggan..." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            {{-- Filter Rating --}}
            <div>
                <select name="rating" class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Semua Rating --</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>★ 5 Bintang</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>★ 4 Bintang</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>★ 3 Bintang</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>★ 2 Bintang</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>★ 1 Bintang</option>
                </select>
            </div>

            {{-- Submit & Reset Button --}}
            <div class="flex gap-2">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'rating']))
                    <a href="{{ route('admin.reviews.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Reviews Table Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 font-medium text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Produk & Invoice</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Rating & Komentar</th>
                        <th class="py-3 px-4">Balasan Penjual</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @if($reviews->isEmpty())
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <p class="text-sm font-medium">Belum ada ulasan yang sesuai filter.</p>
                            </td>
                        </tr>
                    @else
                        @foreach($reviews as $review)
                            <tr class="hover:bg-gray-50/80 transition-colors" id="review-row-{{ $review->id }}">
                                
                                {{-- Produk & Invoice --}}
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="font-semibold text-gray-900 block truncate">{{ $review->product->title ?? 'Produk Dihapus' }}</span>
                                    <a href="{{ route('admin.orders.show', $review->order_id) }}" class="text-xs text-emerald-600 hover:underline block mt-0.5">
                                        #{{ $review->order->invoice_number ?? '-' }}
                                    </a>
                                </td>

                                {{-- Pelanggan --}}
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-gray-800 block">{{ $review->user->name ?? 'User' }}</span>
                                    <span class="text-xs text-gray-400 block">{{ $review->created_at->format('d M Y, H:i') }}</span>
                                </td>

                                {{-- Rating & Komentar --}}
                                <td class="py-3.5 px-4 max-w-md">
                                    <div class="text-amber-400 text-xs font-bold mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                        <span class="text-gray-500 text-xs ml-1">({{ $review->rating }}/5)</span>
                                    </div>
                                    <p class="text-xs text-gray-600 italic">"{{ $review->comment ?? 'Tanpa komentar.' }}"</p>
                                </td>

                                {{-- Balasan Penjual --}}
                                <td class="py-3.5 px-4 max-w-xs" id="reply-container-{{ $review->id }}">
                                    @if($review->admin_reply)
                                        <div class="p-2 bg-slate-50 border-l-2 border-emerald-500 rounded text-xs text-gray-600">
                                            <span class="font-semibold text-emerald-700 block text-[11px]">Dibalas:</span>
                                            <span class="reply-text">{{ $review->admin_reply }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic reply-text">Belum dibalas</span>
                                    @endif
                                </td>

                                {{-- Status Approval --}}
                                <td class="py-3.5 px-4">
                                    <span id="status-badge-{{ $review->id }}" 
                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $review->is_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $review->is_approved ? 'Tampil' : 'Disembunyikan' }}
                                    </span>
                                </td>

                                {{-- Action Buttons --}}
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        
                                        {{-- Tombol Open Modal --}}
                                        <button type="button" 
                                                x-on:click="replyModal = true; selectedReview = {{ $review->id }}; replyText = {{ json_encode($review->admin_reply ?? '') }}"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                                title="Balas Ulasan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                        </button>

                                        {{-- Tombol Toggle Status --}}
                                        <button type="button" 
                                                onclick="toggleReviewStatus({{ $review->id }}, this)"
                                                class="p-2 {{ $review->is_approved ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition-colors"
                                                title="{{ $review->is_approved ? 'Sembunyikan Ulasan' : 'Tampilkan Ulasan' }}">
                                            <svg id="icon-approved-{{ $review->id }}" class="w-4 h-4 {{ $review->is_approved ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.01 10.01 0 013.682-.763c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                                            </svg>
                                            <svg id="icon-hidden-{{ $review->id }}" class="w-4 h-4 {{ $review->is_approved ? 'hidden' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL BALASAN ADMIN --}}
    <div x-show="replyModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        
        <div x-on:click.away="replyModal = false" 
             class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-lg font-bold text-gray-800">Balas Ulasan Pelanggan</h3>
                <button type="button" x-on:click="replyModal = false" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
            </div>

            <form x-on:submit.prevent="submitReplyAjax()" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tulis Balasan Penjual</label>
                    <textarea name="admin_reply" x-model="replyText" rows="4" required
                              placeholder="Terima kasih atas ulasan Anda! Kami senang Anda menyukai produk kami."
                              class="w-full border border-gray-300 rounded-lg text-sm p-3 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" x-on:click="replyModal = false" 
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
                        Simpan Balasan
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function submitReplyAjax() {
        const alpineData = Alpine.$data(document.querySelector('[x-data]'));
        const reviewId = alpineData.selectedReview;
        const replyText = alpineData.replyText;

        if (!reviewId || !replyText.trim()) return;

        fetch(`/admin/reviews/${reviewId}/reply`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ admin_reply: replyText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update UI Balasan secara live
                const replyContainer = document.getElementById(`reply-container-${reviewId}`);
                replyContainer.innerHTML = `
                    <div class="p-2 bg-slate-50 border-l-2 border-emerald-500 rounded text-xs text-gray-600">
                        <span class="font-semibold text-emerald-700 block text-[11px]">Dibalas:</span>
                        <span class="reply-text">${data.admin_reply}</span>
                    </div>
                `;
                alpineData.replyModal = false;

                // Pop-up Notifikasi Berhasil (SweetAlert2)
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Balasan ulasan berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                // Pop-up Notifikasi Gagal
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Gagal menyimpan balasan ulasan.',
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan koneksi/jaringan ke server.',
            });
        });
    }

    function toggleReviewStatus(reviewId, btnElement) {
        fetch(`/admin/reviews/${reviewId}/toggle`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById(`status-badge-${reviewId}`);
                const iconApproved = document.getElementById(`icon-approved-${reviewId}`);
                const iconHidden = document.getElementById(`icon-hidden-${reviewId}`);

                if (data.is_approved) {
                    badge.textContent = 'Tampil';
                    badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800';
                    btnElement.className = 'p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors';
                    btnElement.title = 'Sembunyikan Ulasan';
                    iconApproved.classList.remove('hidden');
                    iconHidden.classList.add('hidden');
                } else {
                    badge.textContent = 'Disembunyikan';
                    badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600';
                    btnElement.className = 'p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors';
                    btnElement.title = 'Tampilkan Ulasan';
                    iconApproved.classList.add('hidden');
                    iconHidden.classList.remove('hidden');
                }

                // Toast Notification Pojok Kanan Atas
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message || 'Status visibilitas ulasan berhasil diperbarui.',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan jaringan.',
            });
        });
    }
</script>
@endpush