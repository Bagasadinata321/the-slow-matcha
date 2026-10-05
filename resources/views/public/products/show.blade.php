@extends('layouts.app')

@section('title', $product->title . ' — TheSlowMatcha')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endpush

@section('content')
<div class="shop-container">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Home</a> <span>/</span> 
        <a href="{{ route('public.products.index') }}">Shop</a> <span>/</span> 
        <span style="color: var(--text-primary);">{{ $product->title }}</span>
    </div>

    <!-- PRODUCT DETAIL LAYOUT -->
    <div class="product-show-layout">

        <!-- LEFT: GALLERY -->
        <div class="show-gallery">
            @php
                $coverPath = $product->coverMedia ? asset('storage/' . $product->coverMedia->path) : asset('images/placeholder.jpg');
            @endphp
            <div class="main-img-box">
                <span class="card-badge" style="position: absolute; top: 16px; left: 16px;">BEST SELLER</span>
                <img id="mainImage" src="{{ $coverPath }}" alt="{{ $product->title }}">
                <div class="zoom-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>

            <!-- THUMBNAILS -->
            <div class="gallery-thumbs-track">
                <div class="thumb-card active" onclick="switchImage('{{ $coverPath }}', this)">
                    <img src="{{ $coverPath }}">
                </div>
                @if($product->galleryMedia)
                    @foreach($product->galleryMedia as $gallery)
                        @php $gPath = asset('storage/' . $gallery->path); @endphp
                        <div class="thumb-card" onclick="switchImage('{{ $gPath }}', this)">
                            <img src="{{ $gPath }}">
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- RIGHT: PRODUCT INFO -->
        <div class="show-info">
            <div class="eyebrow">{{ ucfirst($product->product_type ?? 'Matcha') }}</div>
            <h1>{{ $product->title }}</h1>

            <div class="rating-row" style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span class="rating-stars" style="color: #f59e0b;">
                    @php $avg = $product->averageRating(); @endphp
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($avg) ? '★' : '☆' }}
                    @endfor
                </span>
                <span style="font-size: 14px; color: var(--text-muted);">
                    {{ $avg > 0 ? $avg : 'Belum ada rating' }} ({{ $product->totalReviews() }} ulasan)
                </span>
            </div>

            <!-- PRICE DISPLAY (DENGAN SUPPORT HARGA PROMO & DISKON) -->
            <div class="show-price" id="displayPrice">
                Rp {{ number_format($product->variants->first()->price ?? 0, 0, ',', '.') }}
            </div>
            <div id="displayPromo" style="font-size: 14px; margin-top: -12px; margin-bottom: 16px; display: none;">
                <s id="displayNormalPrice" style="color: var(--text-muted, #888);"></s> 
                <span id="displayDiscountBadge" style="background: var(--promo, #e53935); color:#fff; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600; margin-left:6px;"></span>
            </div>

            <!-- SIZE OPTIONS -->
            <div class="option-title">Pilih Ukuran</div>
            <div class="size-pills">
                @foreach($product->variants as $variant)
                    @php
                        $hasDiscount = $variant->promo_price && $variant->promo_price < $variant->price;
                        $effectivePrice = $hasDiscount ? $variant->promo_price : $variant->price;
                        $discountPercent = $hasDiscount ? round((($variant->price - $variant->promo_price) / $variant->price) * 100) : 0;
                    @endphp
                    <div class="size-btn {{ $loop->first ? 'active' : '' }}" 
                        data-price="{{ $variant->price }}" 
                        data-effective-price="{{ $effectivePrice }}"
                        data-promo-price="{{ $variant->promo_price ?? 0 }}"
                        data-discount="{{ $discountPercent }}"
                        data-stock="{{ $variant->stock }}"
                        data-variant-id="{{ $variant->id }}"
                        onclick="selectVariant(this)">
                        <div>{{ $variant->variant_name }}</div>
                    </div>
                @endforeach
            </div>

            <!-- QUANTITY & STOCK -->
            <div class="qty-stock-grid">
                <div>
                    <div class="option-title">Quantity</div>
                    <div class="stepper">
                        <button type="button" id="qtyMinus">−</button>
                        <input type="text" id="qtyInput" value="1" readonly>
                        <button type="button" id="qtyPlus">+</button>
                    </div>
                </div>
                <div>
                    <div class="option-title">Stock</div>
                    <div style="font-weight: 600; color: var(--brand-dark); font-size: 14px;" id="stockStatus">Ready Stock</div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-row">
                <button class="btn-add-cart">ADD TO CART</button>
                <button class="btn-wishlist">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>
            </div>

            <!-- TRUST BOX -->
            <div class="trust-card-box">
                <div class="trust-row">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <div>
                        <strong>Free Shipping</strong>
                        <div style="color: var(--text-muted);">Gratis ongkir untuk pembelian di atas Rp 500.000</div>
                    </div>
                </div>
                <div class="trust-row">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <div>
                        <strong>100% Authentic</strong>
                        <div style="color: var(--text-muted);">Produk asli dari Jepang</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- BOTTOM SECTION: TABS & LIFESTYLE IMAGE -->
    <div class="tab-section-grid">
        <div>
            <div class="tab-nav">
                <button class="tab-link active" onclick="openTab('deskripsi', this)">DESKRIPSI</button>
                <button class="tab-link" onclick="openTab('detail', this)">DETAIL</button>
                <button class="tab-link" onclick="openTab('cara', this)">CARA PENYAJIAN</button>
                <button class="tab-link" onclick="openTab('reviews', this)">ULASAN ({{ $product->totalReviews() }})</button>
            </div>

            <!-- TAB DESKRIPSI (RENDER HASIL PARSER EDITOR.JS) -->
            <div id="deskripsi" class="tab-pane editorjs-content active">
                @if(!empty($parsedContent))
                    {!! $parsedContent !!}
                @else
                    <p>{{ $product->excerpt ?? 'Tidak ada deskripsi produk.' }}</p>
                @endif
            </div>

            <div id="detail" class="tab-pane">
                @if(!empty($product->details))
                    <p>{!! nl2br(e($product->details)) !!}</p>
                @else
                    <p>Origin: Uji, Kyoto, Japan<br>Grade: Ceremonial Grade<br>Shelf Life: 6 Bulan setelah dibuka</p>
                @endif
            </div>

            <div id="cara" class="tab-pane">
                @if(!empty($product->serving_guide))
                    <p>{!! nl2br(e($product->serving_guide)) !!}</p>
                @else
                    <p>Ayak 2g matcha, tambahkan 70ml air hangat (80°C), lalu aduk dengan Chasen membentuk huruf W hingga berbusa.</p>
                @endif
            </div>
            <!-- TAB ULASAN / REVIEWS -->
            <div id="reviews" class="tab-pane">
                @if($product->reviews->isEmpty())
                    <p style="color: var(--text-muted); font-style: italic;">Belum ada ulasan untuk produk ini.</p>
                @else
                    <div class="reviews-list" style="display: flex; flex-direction: column; gap: 20px;">
                        @foreach($product->reviews as $review)
                            <div class="review-item" style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <strong style="font-size: 15px;">{{ $review->user->name ?? 'Pembeli' }}</strong>
                                    <span style="font-size: 12px; color: var(--text-muted);">
                                        {{ $review->created_at->format('d M Y') }}
                                    </span>
                                </div>

                                <div style="color: #f59e0b; margin-bottom: 8px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>

                                @if(!empty($review->comment))
                                    <p style="font-size: 14px; margin: 0; color: var(--text-primary);">
                                        {{ $review->comment }}
                                    </p>
                                @endif

                                {{-- Balasan Admin --}}
                                @if(!empty($review->admin_reply))
                                    <div style="margin-top: 12px; padding: 12px; background: rgba(0,0,0,0.03); border-left: 3px solid var(--brand-dark, #2d5a27); border-radius: 4px;">
                                        <strong style="font-size: 13px; color: var(--brand-dark, #2d5a27); display: block; margin-bottom: 4px;">
                                            Balasan Penjual:
                                        </strong>
                                        <p style="font-size: 13px; margin: 0; color: var(--text-muted);">
                                            {{ $review->admin_reply }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="lifestyle-media-box">
            <img src="{{ $coverPath }}" alt="Lifestyle Matcha">
        </div>
    </div>
    <!-- RELATED PRODUCTS SECTION -->
    <div style="margin-top: 80px; padding-top: 48px; border-top: 1px solid var(--border-color);">
        <div style="text-align: center; margin-bottom: 36px;">
            <h2 style="font-size: 28px; font-weight: 500; font-family: Georgia, serif; margin-bottom: 8px;">
                You May Also Like
            </h2>
            <p style="color: var(--text-muted); font-size: 14px;">
                Eksplorasi varian matcha unggulan kami lainnya
            </p>
        </div>

        <div class="product-grid">
            @foreach($relatedProducts as $related)
                @php
                    $relCover = $related->coverMedia ? asset('storage/' . $related->coverMedia->path) : asset('images/placeholder.jpg');
                    $firstVariant = $related->variants->first();
                @endphp
                <a href="{{ route('public.products.show', $related->slug) }}" class="card-item">
                    <div class="card-thumb">
                        <span class="card-badge">RECOMMENDED</span>
                        <img src="{{ $relCover }}" alt="{{ $related->title }}">
                    </div>
                    <div class="card-cat">{{ strtoupper($related->product_type ?? 'Matcha') }}</div>
                    <div class="card-title">{{ $related->title }}</div>
                    <div class="card-bottom">
                        <div class="card-price">
                            Rp {{ number_format($firstVariant->price ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="add-btn-icon">+</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // 1. Switch Galeri Gambar
    function switchImage(src, elem) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-card').forEach(c => c.classList.remove('active'));
        elem.classList.add('active');
    }

    // 2. Select Varian Produk
    function selectVariant(elem) {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        elem.classList.add('active');
        
        const price = parseFloat(elem.dataset.price);
        const effectivePrice = parseFloat(elem.dataset.effectivePrice);
        const discount = parseInt(elem.dataset.discount);
        const stock = parseInt(elem.dataset.stock);

        // Update Tampilan Harga Utama
        const displayPrice = document.getElementById('displayPrice');
        if (displayPrice) {
            displayPrice.innerText = 'Rp ' + effectivePrice.toLocaleString('id-ID');
        }

        // Update Tampilan Coret / Badge Promo
        const promoBox = document.getElementById('displayPromo');
        if (promoBox) {
            if (discount > 0) {
                promoBox.style.display = 'block';
                document.getElementById('displayNormalPrice').innerText = 'Rp ' + price.toLocaleString('id-ID');
                document.getElementById('displayDiscountBadge').innerText = 'Hemat ' + discount + '%';
            } else {
                promoBox.style.display = 'none';
            }
        }

        // Update Tampilan Stok & Reset Qty
        const stockStatus = document.getElementById('stockStatus');
        if (stockStatus) {
            if (stock > 0) {
                stockStatus.innerText = 'Ready Stock (' + stock + ')';
                stockStatus.style.color = 'var(--brand-dark, #2d5a27)';
            } else {
                stockStatus.innerText = 'Stok Habis';
                stockStatus.style.color = 'var(--promo, #e53935)';
            }
        }

        const qtyInput = document.getElementById('qtyInput');
        if (qtyInput) qtyInput.value = 1;
    }

    // 3. Tab Navigation
    function openTab(tabId, elem) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
        
        document.getElementById(tabId).classList.add('active');
        elem.classList.add('active');
    }

    // 4. Inisialisasi Event Listener saat DOM Ready
    document.addEventListener('DOMContentLoaded', () => {
        // Run Varian Pertama
        const activeVariant = document.querySelector('.size-btn.active');
        if (activeVariant) selectVariant(activeVariant);

        // Qty Stepper (Single Event Listener)
        const qtyInput = document.getElementById('qtyInput');
        const qtyMinus = document.getElementById('qtyMinus');
        const qtyPlus  = document.getElementById('qtyPlus');

        if (qtyMinus && qtyPlus && qtyInput) {
            qtyMinus.addEventListener('click', () => {
                let currentVal = parseInt(qtyInput.value) || 1;
                if (currentVal > 1) {
                    qtyInput.value = currentVal - 1;
                }
            });

            qtyPlus.addEventListener('click', () => {
                let currentVal = parseInt(qtyInput.value) || 1;
                const currentActiveVariant = document.querySelector('.size-btn.active');
                const maxStock = currentActiveVariant ? parseInt(currentActiveVariant.dataset.stock) : 999;

                if (currentVal < maxStock) {
                    qtyInput.value = currentVal + 1;
                } else {
                    alert('Mencapai batas stok yang tersedia.');
                }
            });
        }

        // Add to Cart Action
        const btnAddCart = document.querySelector('.btn-add-cart');
        if (btnAddCart) {
            btnAddCart.addEventListener('click', (e) => {
                e.preventDefault();

                const currentActiveVariant = document.querySelector('.size-btn.active');
                const variantId = currentActiveVariant ? currentActiveVariant.dataset.variantId : null;
                const quantity = qtyInput ? parseInt(qtyInput.value) : 1;

                if (!variantId) {
                    alert('Silakan pilih varian produk terlebih dahulu.');
                    return;
                }

                btnAddCart.disabled = true;

                fetch("{{ route('public.cart.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        variant_id: variantId,
                        quantity: quantity
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btnAddCart.disabled = false;

                    if (data.success) {
                        alert(data.message);
                        
                        const cartBadge = document.querySelector('.cart-badge');
                        if (cartBadge && data.total_items) {
                            cartBadge.textContent = data.total_items;
                        }
                    } else {
                        alert(data.message || 'Gagal menambahkan produk ke keranjang.');
                    }
                })
                .catch(() => {
                    btnAddCart.disabled = false;
                    alert('Terjadi kesalahan koneksi ke server.');
                });
            });
        }
    });
</script>
@endpush