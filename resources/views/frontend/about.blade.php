@extends('frontend.layouts.master')

@section('title', 'Tentang Kami - SIKOMANG')
@section('meta_description', 'Tentang SIKOMANG - Sistem Informasi Kawasan Mangrove untuk monitoring dan pengelolaan ekosistem mangrove di Indonesia')

@section('styles')
<style>
    .about-hero {
        background: linear-gradient(135deg, rgba(45, 122, 94, 0.95) 0%, rgba(74, 157, 122, 0.95) 100%),
                    url('{{ asset("assets/images/mangrove-about.jpg") }}') center/cover;
        color: white;
        padding: 120px 0;
    }

    .feature-box {
        text-align: center;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s;
        height: 100%;
    }

    .feature-box:hover {
        transform: translateY(-10px);
    }

    .feature-box .icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }

    .team-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }

    .team-card:hover {
        transform: translateY(-10px);
    }

    .team-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }

    .team-card .card-body {
        padding: 25px;
        text-align: center;
    }

    .timeline {
        position: relative;
        padding: 40px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
        transform: translateX(-50%);
    }

    .timeline-item {
        margin-bottom: 50px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 20px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary-color);
        border: 4px solid white;
        box-shadow: 0 0 0 4px var(--primary-color);
        transform: translateX(-50%);
        z-index: 2;
    }

    .timeline-content {
        width: 45%;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .timeline-item:nth-child(odd) .timeline-content {
        margin-left: auto;
    }

    .stats-highlight {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 60px 0;
        border-radius: 20px;
        margin: 40px 0;
    }

    @media (max-width: 768px) {
        .timeline::before {
            left: 20px;
        }

        .timeline-item::before {
            left: 20px;
        }

        .timeline-content {
            width: calc(100% - 60px);
            margin-left: 60px !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h1 class="display-4 mb-4">Tentang SIKOMANG</h1>
            <p class="lead">Sistem Informasi Kawasan Mangrove</p>
            <p class="mt-4" style="max-width: 800px; margin: 0 auto;">
                Platform digital untuk monitoring, pengelolaan, dan konservasi ekosistem mangrove di Indonesia.
                Kami berkomitmen untuk menjaga keberlanjutan ekosistem mangrove demi masa depan yang lebih hijau.
            </p>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="p-5 h-100" style="background: linear-gradient(135deg, #f8f9fa, white); border-radius: 15px;">
                    <div class="mb-4">
                        <i class="fas fa-eye fa-3x text-primary"></i>
                    </div>
                    <h2 class="mb-4">Visi Kami</h2>
                    <p class="lead">Menjadi platform terdepan dalam monitoring dan konservasi ekosistem mangrove di Indonesia, mendukung kelestarian lingkungan untuk generasi mendatang.</p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="p-5 h-100" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border-radius: 15px;">
                    <div class="mb-4">
                        <i class="fas fa-bullseye fa-3x"></i>
                    </div>
                    <h2 class="mb-4">Misi Kami</h2>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle me-3"></i> Menyediakan data akurat tentang kawasan mangrove</li>
                        <li class="mb-3"><i class="fas fa-check-circle me-3"></i> Memfasilitasi monitoring real-time kondisi ekosistem</li>
                        <li class="mb-3"><i class="fas fa-check-circle me-3"></i> Mendukung kolaborasi antar stakeholder</li>
                        <li class="mb-3"><i class="fas fa-check-circle me-3"></i> Meningkatkan kesadaran masyarakat akan pentingnya mangrove</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Highlight -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="stats-highlight" data-aos="zoom-in">
            <div class="row text-center g-4">
                <div class="col-lg-3 col-md-6">
                    <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                    <h2>{{ $stats['total_locations'] }}+</h2>
                    <p>Lokasi Terdaftar</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <i class="fas fa-tree fa-3x mb-3"></i>
                    <h2>{{ number_format($stats['total_area']) }}</h2>
                    <p>Hektar Kawasan</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <i class="fas fa-camera fa-3x mb-3"></i>
                    <h2>{{ $stats['total_images'] }}+</h2>
                    <p>Dokumentasi Foto</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <i class="fas fa-seedling fa-3x mb-3"></i>
                    <h2>{{ $stats['total_species'] }}+</h2>
                    <p>Spesies Tercatat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Fitur Unggulan</h2>
            <p>Platform lengkap dengan berbagai fitur untuk mendukung konservasi mangrove</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box">
                    <div class="icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h4>Pemetaan Digital</h4>
                    <p class="text-muted">Visualisasi geografis kawasan mangrove dengan koordinat GPS yang akurat dan peta interaktif.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box">
                    <div class="icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h4>Database Terpadu</h4>
                    <p class="text-muted">Informasi lengkap tentang biodiversitas, aktivitas, dan program di setiap kawasan mangrove.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-box">
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Monitoring Real-time</h4>
                    <p class="text-muted">Pantau kondisi dan perkembangan ekosistem mangrove secara berkala dan terstruktur.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-box">
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4>Sistem Pelaporan</h4>
                    <p class="text-muted">Platform untuk melaporkan dan tracking penanganan kerusakan ekosistem mangrove.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-box">
                    <div class="icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <h4>Galeri Dokumentasi</h4>
                    <p class="text-muted">Dokumentasi visual lengkap dari berbagai lokasi kawasan mangrove di Indonesia.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-box">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Kolaborasi</h4>
                    <p class="text-muted">Memfasilitasi kerjasama antar stakeholder untuk konservasi yang lebih efektif.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Mangroves Matter -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Mengapa Mangrove Penting?</h2>
            <p>Manfaat ekosistem mangrove bagi kehidupan dan lingkungan</p>
        </div>

        <div class="row g-4 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="{{ asset('assets/images/mangrove-importance.jpg') }}" alt="Mangrove" class="img-fluid rounded-3 shadow-lg">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="ps-lg-4">
                    <div class="mb-4">
                        <h4><i class="fas fa-shield-alt text-primary me-3"></i> Perlindungan Pesisir</h4>
                        <p class="text-muted">Melindungi garis pantai dari abrasi, tsunami, dan badai dengan sistem akar yang kuat.</p>
                    </div>
                    <div class="mb-4">
                        <h4><i class="fas fa-fish text-primary me-3"></i> Habitat Biodiversitas</h4>
                        <p class="text-muted">Rumah bagi berbagai spesies ikan, burung, dan satwa liar yang unik dan khas.</p>
                    </div>
                    <div class="mb-4">
                        <h4><i class="fas fa-cloud text-primary me-3"></i> Penyerap Karbon</h4>
                        <p class="text-muted">Mampu menyerap karbon 4x lebih banyak dibanding hutan tropis, membantu mitigasi perubahan iklim.</p>
                    </div>
                    <div class="mb-4">
                        <h4><i class="fas fa-coins text-primary me-3"></i> Sumber Ekonomi</h4>
                        <p class="text-muted">Mendukung perikanan, ekowisata, dan mata pencaharian masyarakat pesisir.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="text-white mb-3">Bergabunglah dalam Gerakan Konservasi Mangrove</h2>
                <p class="text-white mb-0">Bersama kita jaga ekosistem mangrove untuk masa depan yang lebih hijau dan berkelanjutan.</p>
            </div>
            <div class="col-lg-4 text-lg-end text-start mt-3 mt-lg-0" data-aos="fade-left">
                <a href="{{ route('frontend.contact') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-hands-helping me-2"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
