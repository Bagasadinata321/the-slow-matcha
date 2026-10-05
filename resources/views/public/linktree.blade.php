<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteSettings->site_name ?? 'TheSlowMatcha' }} — Links & Rituals</title>

    <!-- Favicon -->
    @if(!empty($siteSettings->favicon))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $siteSettings->favicon) }}">
    @endif
    <!-- FontAwesome CDN (Untuk Icon Menu) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/public-linktree.css') }}">
</head>
<body>

    <div class="tree-container">
        
        <!-- BRAND HEADER -->
        <div class="brand-header">
            <div class="profile-avatar">
                @if(!empty($siteSettings->logo))
                    <img src="{{ asset('storage/' . $siteSettings->logo) }}" alt="{{ $siteSettings->site_name }}">
                @else
                    {{ substr($siteSettings->site_name ?? 'M', 0, 1) }}
                @endif
            </div>
            <h1 class="brand-title">{{ $siteSettings->site_name ?? 'TheSlowMatcha' }}</h1>
            <p class="brand-tagline">{{ $siteSettings->tagline ?? 'Pure Matcha. Real Ritual.' }}</p>
        </div>

        <!-- PROMO BANNER DINAMIS -->
        @if($promoBanner)
            <a href="{{ $promoBanner->url ?? '#' }}" class="promo-banner">
                <div class="promo-info">
                    <h4>{{ $promoBanner->title }}</h4>
                    <p>{{ $promoBanner->subtitle ?? 'Penawaran Spesial' }}</p>
                </div>
                <span class="promo-tag">Promo</span>
            </a>
        @endif

<!-- LIST MENU LINKTREE -->
<div class="links-wrapper">

    <!-- Tombol WA Default -->
    @if($siteSettings && !empty($siteSettings->whatsapp_number))
        <a href="https://wa.me/{{ $siteSettings->whatsapp_number }}?text=Halo%20{{ urlencode($siteSettings->site_name ?? 'The Slow Matcha') }},%20saya%20ingin%20bertanya." class="link-btn" target="_blank">
            <div class="label-wrap">
                <span class="icon">💬</span> Order / Chat via WhatsApp
            </div>
            <span class="arrow">→</span>
        </a>
    @endif

    <!-- Loop Menu dari Database -->
@foreach($links as $link)
    @if($link->has_sub)
        <!-- Parent Menu -->
        <div class="link-accordion">
            <div class="accordion-header">
                <div class="label-wrap">
                    <!-- Gunakan tag <i> untuk merender class icon -->
                    <i class="fa-solid fa-{{ $link->icon ?? 'link' }} icon"></i>
                    <span class="menu-title">{{ $link->label }}</span>
                </div>
                <span class="chevron">▼</span>
            </div>
            <div class="accordion-body">
                @foreach($link->children as $child)
                    <a href="{{ $child->url }}" class="sub-link" target="_blank">
                        <i class="fa-brands fa-{{ $child->icon ?? 'instagram' }} icon" style="margin-right: 8px;"></i>
                        <span class="menu-title">{{ $child->label }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <!-- Direct Link -->
        <a href="{{ $link->url }}" class="link-btn" target="_blank">
            <div class="label-wrap">
                <i class="fa-solid fa-{{ $link->icon ?? 'link' }} icon"></i>
                <span class="menu-title">{{ $link->label }}</span>
            </div>
            <span class="arrow">→</span>
        </a>
    @endif
@endforeach

</div>
        <!-- FOOTER -->
        <div class="tree-footer">
            © {{ date('Y') }} <a href="/">{{ $siteSettings->site_name ?? 'TheSlowMatcha' }}</a>. All Rights Reserved.
        </div>

    </div>
<script src="{{ asset('js/public-linktree.js') }}" defer></script>
</body>
</html>