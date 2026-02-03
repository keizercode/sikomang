<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIKOMANG - Sistem Informasi dan Komunikasi Mangrove DKI Jakarta">
    <title>@yield('title', 'SIKOMANG')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="https://pencil-matter-70015947.figma.site/_assets/v11/5a52c0026642845f54f76f85096c3a34c237af42.png">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-gradient {
            background: linear-gradient(135deg, var(--color-primary) 100%);
        }

        .quote-icon {
            color: #f59e0b;
        }

        .article-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .article-card {
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--color-primary-light);
        }

        .btn-primary {
            background-color: var(--color-primary-light);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--color-primary-dark);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background text-secondary antialiased">
    @include('frontend.components.navbar')

    <main>
        @yield('content')
    </main>

    @include('frontend.components.footer')
    @stack('scripts')
</body>
</html>
