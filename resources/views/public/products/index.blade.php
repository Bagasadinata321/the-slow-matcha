@extends('layouts.app')

@section('title', 'Shop All Matcha — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shop.css') }}">
<style>
    /* Utility style untuk harga coret & badge promo pada katalog */
    .card-thumb {
        position: relative;
    }
    .card-badge.promo-badge {
        background-color: #e53935;
        color: #ffffff;
        font-weight: 700;
    }
    .price-strike {
        text-decoration: line-through;
        color: #9ca3af;
        font-size: 12px;
        margin-right: 4px;
        font-weight: 400;
    }
    .price-promo {
        color: #e53935;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="shop-container">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Home</a> <span>/</span> <span style="color: var(--text-primary);">Shop</span>
    </div>

    <!-- CATALOG HEADER -->
    <div class="catalog-header">
        <h1>Shop All Matcha</h1>
        <p>Temukan matcha terbaik untuk ritualmu. Dari ceremonial grade hingga daily matcha, dipilih langsung dari Jepang.</p>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <div class="filter-pills">
            <a href="{{ route('public.products.index') }}" class="pill {{ !request('category') && !request('type') ? 'active' : '' }}">All</a>
            <a href="{{ route('public.products.index', ['type' => 'matcha']) }}" class="pill {{ request('type') == 'matcha' ? 'active' : '' }}">Matcha</a>
            <a href="{{ route('public.products.index', ['type' => 'tool']) }}" class="pill {{ request('type') == 'tool' ? 'active' : '' }}">Accessories / Tools</a>
        </div>
        <div style="font-size: 13px; color: var(--text-muted);">
            Sort by: <span style="color: var(--text-primary); font-weight: 600;">Featured</span>
        </div>
    </div>

    @php
        // Kelompokkan produk berdasarkan product_type ('matcha' dan 'tool')
        $matchaProducts = $products->where('product_type', 'matcha');
        $toolProducts   = $products->where('product_type', 'tool');
    @endphp

    <!-- SECTION 1: MATCHA PRODUCTS -->
    @if(!request('type') || request('type') == 'matcha')
        <div class="section-title-wrap" style="margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); text-transform: uppercase; letter-spacing: 1px;">Matcha Collection</h2>
        </div>

        <div class="product-grid">
            @forelse($matchaProducts as $product)
                @php
                    $cover = $product->coverMedia ? asset('storage/' . $product->coverMedia->path) : asset('images/placeholder.jpg');

                    // Filter varian yang diskon
                    $promoVariants = $product->variants ? $product->variants->filter(function ($v) {
                        return !empty($v->promo_price) && $v->promo_price < $v->price;
                    }) : collect();

                    $hasPromo = $promoVariants->isNotEmpty();

                    // Hitung diskon % tertinggi untuk badge
                    $maxDiscountPercent = 0;
                    if ($hasPromo) {
                        $maxDiscountPercent = $promoVariants->max(function ($v) {
                            return round((($v->price - $v->promo_price) / $v->price) * 100);
                        });
                    }

                    // Cari varian terendah berdasarkan harga promo/efektif
                    $cheapestVariant = $product->variants ? $product->variants->sortBy(function ($v) {
                        return (!empty($v->promo_price) && $v->promo_price < $v->price) ? $v->promo_price : $v->price;
                    })->first() : null;

                    $isCheapestPromo = $cheapestVariant && !empty($cheapestVariant->promo_price) && $cheapestVariant->promo_price < $cheapestVariant->price;
                @endphp

                <a href="{{ route('public.products.show', $product->slug) }}" class="card-item">
                    <div class="card-thumb">
                        @if($hasPromo)
                            <span class="card-badge promo-badge">SAVE {{ $maxDiscountPercent }}%</span>
                        @elseif($loop->first)
                            <span class="card-badge best-seller">BEST SELLER</span>
                        @endif
                        <img src="{{ $cover }}" alt="{{ $product->title }}">
                    </div>
                    <div class="card-cat">
                        {{ $product->product_type === 'matcha' ? 'MATCHA POWDER' : 'ACCESSORIES' }}
                    </div>
                    <div class="card-title">{{ $product->title }}</div>
                    <div class="card-bottom">
                        <div class="card-price">
                            @if($cheapestVariant)
                                @if($isCheapestPromo)
                                    <span class="price-strike">Rp {{ number_format($cheapestVariant->price, 0, ',', '.') }}</span>
                                    <span class="price-promo">Rp {{ number_format($cheapestVariant->promo_price, 0, ',', '.') }}</span>
                                @else
                                    Start from Rp <strong style="font-weight: 700; color: var(--text-primary);">{{ number_format($cheapestVariant->price, 0, ',', '.') }}</strong>
                                @endif
                            @else
                                Rp <strong style="font-weight: 700; color: var(--text-primary);">{{ number_format($product->price ?? 0, 0, ',', '.') }}</strong>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <p style="grid-column: 1/-1; color: var(--text-muted); padding: 20px 0;">Belum ada produk Matcha.</p>
            @endforelse
        </div>
    @endif

    <!-- GARIS PEMISAH (DIVIDER) -->
    @if(!request('type') && $matchaProducts->count() > 0 && $toolProducts->count() > 0)
        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 50px 0;">
    @endif

    <!-- SECTION 2: TOOLS / ACCESSORIES PRODUCTS -->
    @if(!request('type') || request('type') == 'tool')
        <div class="section-title-wrap" style="margin-bottom: 20px; {{ !request('type') ? 'margin-top: 10px;' : '' }}">
            <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary); text-transform: uppercase; letter-spacing: 1px;">Tools & Accessories</h2>
        </div>

        <div class="product-grid">
            @forelse($toolProducts as $product)
                @php
                    $cover = $product->coverMedia ? asset('storage/' . $product->coverMedia->path) : asset('images/placeholder.jpg');

                    $promoVariants = $product->variants ? $product->variants->filter(function ($v) {
                        return !empty($v->promo_price) && $v->promo_price < $v->price;
                    }) : collect();

                    $hasPromo = $promoVariants->isNotEmpty();

                    $maxDiscountPercent = 0;
                    if ($hasPromo) {
                        $maxDiscountPercent = $promoVariants->max(function ($v) {
                            return round((($v->price - $v->promo_price) / $v->price) * 100);
                        });
                    }

                    $cheapestVariant = $product->variants ? $product->variants->sortBy(function ($v) {
                        return (!empty($v->promo_price) && $v->promo_price < $v->price) ? $v->promo_price : $v->price;
                    })->first() : null;

                    $isCheapestPromo = $cheapestVariant && !empty($cheapestVariant->promo_price) && $cheapestVariant->promo_price < $cheapestVariant->price;
                @endphp

                <a href="{{ route('public.products.show', $product->slug) }}" class="card-item">
                    <div class="card-thumb">
                        @if($hasPromo)
                            <span class="card-badge promo-badge">SAVE {{ $maxDiscountPercent }}%</span>
                        @endif
                        <img src="{{ $cover }}" alt="{{ $product->title }}">
                    </div>
                    <div class="card-cat">ACCESSORIES</div>
                    <div class="card-title">{{ $product->title }}</div>
                    <div class="card-bottom">
                        <div class="card-price">
                            @if($cheapestVariant)
                                @if($isCheapestPromo)
                                    <span class="price-strike">Rp {{ number_format($cheapestVariant->price, 0, ',', '.') }}</span>
                                    <span class="price-promo">Rp {{ number_format($cheapestVariant->promo_price, 0, ',', '.') }}</span>
                                @else
                                    Rp <strong style="font-weight: 700; color: var(--text-primary);">{{ number_format($cheapestVariant->price, 0, ',', '.') }}</strong>
                                @endif
                            @else
                                Rp <strong style="font-weight: 700; color: var(--text-primary);">{{ number_format($product->price ?? 0, 0, ',', '.') }}</strong>
                            @endif
                        </div>
                        <div class="add-btn-icon">+</div>
                    </div>
                </a>
            @empty
                <p style="grid-column: 1/-1; color: var(--text-muted); padding: 20px 0;">Belum ada produk Tools/Accessories.</p>
            @endforelse
        </div>
    @endif

    <!-- FOOTER FEATURES BANNER -->
    <div class="features-banner" style="margin-top: 60px;">
        <div class="feature-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <div>
                <div class="feature-title">100% PURE</div>
                <div class="feature-desc">Tanpa campuran apapun</div>
            </div>
        </div>
        <div class="feature-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <div>
                <div class="feature-title">CEREMONIAL GRADE</div>
                <div class="feature-desc">Kualitas terbaik dari Jepang</div>
            </div>
        </div>
        <div class="feature-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <div>
                <div class="feature-title">FRESH & AUTHENTIC</div>
                <div class="feature-desc">Diproses dengan standar tinggi</div>
            </div>
        </div>
        <div class="feature-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <div>
                <div class="feature-title">FAST SHIPPING</div>
                <div class="feature-desc">Pengiriman aman & cepat</div>
            </div>
        </div>
    </div>

</div>
@endsection