<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication') - TheSlowMatcha</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Inter:wght@400;500;600&family=Italianno&display=swap" rel="stylesheet">
    
    <!-- Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @stack('styles')
</head>
<body class="tsm-auth-body">

    <div class="tsm-auth-container">
        <!-- Top Bar Header Global -->
        <header class="tsm-auth-header">
            <a href="{{ url('/') }}" class="tsm-auth-logo">TheSlowMatcha</a>
            <div class="tsm-auth-tagline">Pure Matcha,<br>Better Days.</div>
        </header>

        <!-- Main Content Grid -->
        <div class="tsm-auth-grid">
            <!-- Left Hero Section -->
            <div class="tsm-auth-hero">
                @yield('hero')
            </div>

            <!-- Right Form Card -->
            <div class="tsm-auth-card">
                @yield('card')
            </div>
        </div>
    </div>

    <!-- Auth JS -->
    <script src="{{ asset('js/auth.js') }}"></script>
    @stack('scripts')
</body>
</html>