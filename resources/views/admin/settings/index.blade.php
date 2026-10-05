@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
<div class="p-6 space-y-6">

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div id="alert-success" class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 bg-emerald-600 text-white rounded-md flex items-center justify-center font-bold text-xs">✓</span>
                <span class="font-medium text-emerald-900">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('alert-success').style.display='none'" class="text-emerald-700 font-bold px-2">✕</button>
        </div>
    @endif

    {{-- HEADER HALAMAN & LINK TO LINKTREE --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengaturan Website</h1>
            <p class="text-sm text-gray-500">Kelola identitas website, video hero homepage, kontak resmi, dan SEO toko Anda.</p>
        </div>

        <a href="{{ route('admin.linktree.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs transition-colors">
            <span>🌳</span>
            <span>Buka Linktree Builder →</span>
        </a>
    </div>

    {{-- FORM UTAMA PENGATURAN TOKO --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- CARD 1: IDENTITAS TOKO & LOGO --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs space-y-5">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-base font-bold text-gray-800">1. Identitas Toko & Visual</h2>
                <p class="text-xs text-gray-400">Informasi utama yang akan ditampilkan pada header, footer, dan tab browser.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Toko / Website <span class="text-red-500">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name ?? '') }}" required
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 font-bold text-gray-900 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('site_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tagline / Slogan</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings->site_tagline ?? '') }}" placeholder="Contoh: Pure Matcha, Better Days."
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('site_tagline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Logo Toko (Maks 2MB)</label>
                    <div class="flex items-center gap-4">
                        @if(!empty($settings->logo))
                            <div class="w-16 h-16 border rounded-xl p-1 bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="max-h-full max-w-full object-contain">
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>
                    @error('logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Favicon (.ico, .png, .svg)</label>
                    <div class="flex items-center gap-4">
                        @if(!empty($settings->favicon))
                            <div class="w-12 h-12 border rounded-xl p-1 bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="max-h-full max-w-full object-contain">
                            </div>
                        @endif
                        <input type="file" name="favicon" accept="image/png,image/x-icon,image/svg+xml"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>
                    @error('favicon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- CARD 2: HOMEPAGE HERO & VIDEO BACKGROUND (BARU) --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs space-y-5">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-base font-bold text-gray-800">2. Hero Homepage & Video Background</h2>
                <p class="text-xs text-gray-400">Atur judul banner utama dan latar belakang video untuk halaman beranda (Homepage).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Hero (Headline)</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $settings->hero_title ?? '') }}" placeholder="Contoh: Nikmati Matcha Premium Khas Jepang"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('hero_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Subjudul Hero</label>
                    <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $settings->hero_subtitle ?? '') }}" placeholder="Sub-deskripsi singkat di bawah judul"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('hero_subtitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Upload File Video MP4/WebM --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Upload Video Hero (MP4/WebM, Maks 20MB)</label>
                    @if(!empty($settings->hero_video_path))
                        <div class="mb-2">
                            <video src="{{ asset('storage/' . $settings->hero_video_path) }}" class="w-full h-32 object-cover rounded-xl border bg-black" controls muted></video>
                            <span class="text-[10px] text-gray-400">Video tersimpan saat ini</span>
                        </div>
                    @endif
                    <input type="file" name="hero_video" accept="video/mp4,video/webm"
                        class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    @error('hero_video') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>

                {{-- Opsi Alternatif: Link Direct Video URL --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Atau Gunakan Direct Video URL (CDN / Direct Link)</label>
                    <input type="url" name="hero_video_url" value="{{ old('hero_video_url', $settings->hero_video_url ?? '') }}" placeholder="https://domain.com/video.mp4"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    <span class="text-[10px] text-gray-400">Dipakai jika tidak ingin mengunggah file ke server hosting Anda.</span>
                    @error('hero_video_url') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- CARD 3: KONTAK & MEDIA SOSIAL --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs space-y-5">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-base font-bold text-gray-800">3. Kontak & Media Sosial</h2>
                <p class="text-xs text-gray-400">Digunakan untuk fitur pesan otomatis WhatsApp dan link social media.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone ?? '') }}" placeholder="628123456789"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 font-mono text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    <span class="text-[10px] text-gray-400">Gunakan format internasional (misal: 628xxx).</span>
                    @error('contact_phone') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Resmi</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email ?? '') }}" placeholder="info@matchastore.com"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('contact_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">URL Instagram</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url ?? '') }}" placeholder="https://instagram.com/username"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('instagram_url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">URL TikTok</label>
                    <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $settings->tiktok_url ?? '') }}" placeholder="https://tiktok.com/@username"
                        class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('tiktok_url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alamat Fisik / Toko</label>
                    <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap toko..."
                        class="w-full border border-gray-300 rounded-lg text-sm p-3 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">{{ old('address', $settings->address ?? '') }}</textarea>
                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- CARD 4: SEO & METATAG --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs space-y-5">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-base font-bold text-gray-800">4. Search Engine Optimization (SEO)</h2>
                <p class="text-xs text-gray-400">Deskripsi default yang akan dibaca oleh Google dan platform media sosial.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Meta Description Default</label>
                <textarea name="site_description" rows="3" placeholder="Tuliskan deskripsi singkat toko untuk pencarian Google..."
                    class="w-full border border-gray-300 rounded-lg text-sm p-3 text-gray-800 focus:ring-emerald-500 focus:border-emerald-500">{{ old('site_description', $settings->site_description ?? '') }}</textarea>
                @error('site_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- TOMBOL SIMPAN PENGATURAN --}}
        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-8 py-3 rounded-xl transition-colors shadow-md flex items-center gap-2">
                <span>💾</span>
                <span>Simpan Pengaturan Toko</span>
            </button>
        </div>

    </form>
</div>
@endsection