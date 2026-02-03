<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'SIKOMANG - Sistem Informasi Kawasan Mangrove - Platform monitoring dan pengelolaan ekosistem mangrove')">
    <meta name="keywords" content="mangrove, ekosistem, monitoring, konservasi, lingkungan">
    <meta name="author" content="SIKOMANG">
    <title>@yield('title', 'SIKOMANG - Sistem Informasi Kawasan Mangrove')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS (for maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2d7a5e;
            --secondary-color: #4a9d7a;
            --accent-color: #ffa726;
            --dark-color: #1a1a1a;
            --light-color: #f8f9fa;
            --text-color: #333;
            --border-color: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            font-size: 2rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--accent-color);
            transition: width 0.3s;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        /* Search Bar */
        .search-form {
            position: relative;
        }

        .search-form input {
            border-radius: 25px;
            padding: 8px 40px 8px 20px;
            border: none;
            width: 250px;
        }

        .search-form button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 70vh;
            background: linear-gradient(135deg, rgba(45, 122, 94, 0.9) 0%, rgba(74, 157, 122, 0.9) 100%),
                        url('{{ asset("assets/images/mangrove-hero.jpg") }}') center/cover;
            color: white;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-section p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .btn-hero {
            background: var(--accent-color);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-hero:hover {
            background: #ff9100;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            color: white;
        }

        /* Stats Section */
        .stats-section {
            background: white;
            padding: 60px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        /* Card Styles */
        .location-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
        }

        .location-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .location-card img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }

        .location-card .card-body {
            padding: 20px;
        }

        .location-card .card-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .location-card .badge {
            font-size: 0.75rem;
            padding: 5px 10px;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 60px 0 20px;
        }

        .footer h5 {
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: var(--accent-color);
        }

        .footer .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .footer .social-links a:hover {
            background: var(--accent-color);
            transform: translateY(-3px);
        }

        /* Section Title */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            margin: 20px auto;
            border-radius: 2px;
        }

        /* Pagination */
        .pagination {
            margin-top: 40px;
        }

        .pagination .page-link {
            color: var(--primary-color);
            border: 1px solid var(--border-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section p {
                font-size: 1rem;
            }

            .search-form input {
                width: 150px;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('frontend.home') }}">
                <i class="fas fa-leaf"></i>
                <span>SIKOMANG</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}" href="{{ route('frontend.home') }}">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.locations') ? 'active' : '' }}" href="{{ route('frontend.locations') }}">
                            <i class="fas fa-map-marked-alt me-1"></i> Lokasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.gallery') ? 'active' : '' }}" href="{{ route('frontend.gallery') }}">
                            <i class="fas fa-images me-1"></i> Galeri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.monitoring') ? 'active' : '' }}" href="{{ route('frontend.monitoring') }}">
                            <i class="fas fa-chart-line me-1"></i> Monitoring
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}" href="{{ route('frontend.about') }}">
                            <i class="fas fa-info-circle me-1"></i> Tentang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}" href="{{ route('frontend.contact') }}">
                            <i class="fas fa-envelope me-1"></i> Kontak
                        </a>
                    </li>
                    <li class="nav-item ms-3">
                        <form class="search-form" action="{{ route('frontend.search') }}" method="GET">
                            <input type="text" name="q" class="form-control" placeholder="Cari lokasi..." value="{{ request('q') }}">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-leaf me-2"></i> SIKOMANG</h5>
                    <p>Sistem Informasi Kawasan Mangrove untuk monitoring dan pengelolaan ekosistem mangrove di Indonesia.</p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('frontend.home') }}"><i class="fas fa-chevron-right me-2"></i> Beranda</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.locations') }}"><i class="fas fa-chevron-right me-2"></i> Lokasi</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.gallery') }}"><i class="fas fa-chevron-right me-2"></i> Galeri</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.monitoring') }}"><i class="fas fa-chevron-right me-2"></i> Monitoring</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Informasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('frontend.about') }}"><i class="fas fa-chevron-right me-2"></i> Tentang Kami</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.contact') }}"><i class="fas fa-chevron-right me-2"></i> Hubungi Kami</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i> Kebijakan Privasi</a></li>
                        <li class="mb-2"><a href="#"><i class="fas fa-chevron-right me-2"></i> Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Jl. Mangrove No. 123, Jakarta</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +62 21 1234 5678</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@sikomang.id</li>
                        <li class="mb-2"><i class="fas fa-clock me-2"></i> Senin - Jumat, 08:00 - 17:00</li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 40px 0 20px;">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} SIKOMANG. All rights reserved. | Developed with <i class="fas fa-heart text-danger"></i> for Indonesian Mangroves</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary rounded-circle" style="position: fixed; bottom: 30px; right: 30px; display: none; z-index: 999; width: 50px; height: 50px;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom JS -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Back to Top Button
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('#backToTop').fadeIn();
            } else {
                $('#backToTop').fadeOut();
            }
        });

        $('#backToTop').click(function() {
            $('html, body').animate({scrollTop: 0}, 600);
            return false;
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            var target = $(this.hash);
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 600);
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
