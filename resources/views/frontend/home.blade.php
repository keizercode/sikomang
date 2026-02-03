@extends('frontend.layouts.master')

@section('title', 'SIKOMANG - Sistem Informasi Kawasan Mangrove')
@section('meta_description', 'Platform monitoring dan pengelolaan ekosistem mangrove di Indonesia. Jelajahi lokasi, galeri, dan data monitoring kawasan mangrove.')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="hero-content" data-aos="fade-up">
                    <h1>Lindungi Ekosistem Mangrove Indonesia</h1>
                    <p>Platform monitoring dan pengelolaan kawasan mangrove untuk masa depan yang lebih hijau</p>
                    <a href="{{ route('frontend.locations') }}" class="btn-hero me-3">
                        <i class="fas fa-map-marked-alt me-2"></i> Jelajahi Lokasi
                    </a>
                    <a href="{{ route('frontend.about') }}" class="btn btn-outline-light">
                        <i class="fas fa-info-circle me-2"></i> Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>{{ $stats['total_locations'] }}</h3>
                    <p>Lokasi Mangrove</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <i class="fas fa-tree"></i>
                    <h3>{{ number_format($stats['total_area'], 0) }}</h3>
                    <p>Hektar Kawasan</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <i class="fas fa-camera"></i>
                    <h3>{{ $stats['total_images'] }}</h3>
                    <p>Foto Dokumentasi</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card">
                    <i class="fas fa-seedling"></i>
                    <h3>{{ $stats['total_species'] }}</h3>
                    <p>Spesies Terdaftar</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Locations -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Lokasi Mangrove Terbaru</h2>
            <p>Jelajahi berbagai kawasan mangrove yang terdaftar dalam sistem monitoring kami</p>
        </div>

        @if($locations->count() > 0)
        <div class="row g-4">
            @foreach($locations as $location)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                <div class="location-card card h-100">
                    @if($location->images->count() > 0)
                        <img src="{{ Storage::url($location->images->first()->image_path) }}"
                             alt="{{ $location->name }}"
                             class="card-img-top">
                    @else
                        <img src="{{ asset('assets/images/default-mangrove.jpg') }}"
                             alt="{{ $location->name }}"
                             class="card-img-top">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $location->name }}</h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $location->district }}, {{ $location->city }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-success">
                                <i class="fas fa-tree me-1"></i> {{ number_format($location->area) }} Ha
                            </span>
                            @if($location->images->count() > 0)
                            <span class="badge bg-info">
                                <i class="fas fa-camera me-1"></i> {{ $location->images->count() }} Foto
                            </span>
                            @endif
                        </div>
                        @if($location->locationDetail && $location->locationDetail->description)
                        <p class="card-text text-muted small">
                            {{ Str::limit(strip_tags($location->locationDetail->description), 100) }}
                        </p>
                        @endif
                        <a href="{{ route('frontend.detail', encode_id($location->id)) }}"
                           class="btn btn-primary w-100">
                            <i class="fas fa-eye me-2"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $locations->links() }}
        </div>
        @else
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-map-marked-alt fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Belum ada lokasi mangrove terdaftar</h4>
            <p class="text-muted">Data lokasi akan ditampilkan di sini setelah admin menambahkannya.</p>
        </div>
        @endif
    </div>
</section>

<!-- Call to Action -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="text-white mb-3">Ikut Berkontribusi dalam Konservasi Mangrove</h2>
                <p class="text-white mb-0">Laporkan kondisi mangrove di sekitar Anda atau ikuti program penanaman bersama kami</p>
            </div>
            <div class="col-lg-4 text-lg-end text-start mt-3 mt-lg-0" data-aos="fade-left">
                <a href="{{ route('frontend.contact') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-envelope me-2"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Fitur SIKOMANG</h2>
            <p>Platform lengkap untuk monitoring dan pengelolaan ekosistem mangrove</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 3rem; color: var(--primary-color);">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h4>Pemetaan Lokasi</h4>
                    <p class="text-muted">Visualisasi geografis kawasan mangrove dengan koordinat akurat</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 3rem; color: var(--primary-color);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Monitoring Real-time</h4>
                    <p class="text-muted">Pantau kondisi dan perkembangan ekosistem mangrove secara berkala</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 3rem; color: var(--primary-color);">
                        <i class="fas fa-images"></i>
                    </div>
                    <h4>Galeri Dokumentasi</h4>
                    <p class="text-muted">Dokumentasi visual lengkap dari berbagai lokasi mangrove</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 3rem; color: var(--primary-color);">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h4>Data Keanekaragaman</h4>
                    <p class="text-muted">Informasi lengkap tentang flora dan fauna di kawasan mangrove</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 3rem; color: var(--primary-color);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4>Laporan Kerusakan</h4>
                    <p class="text-muted">Sistem pelaporan dan tracking penanganan kerusakan ekosistem</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 3rem; color: var(--primary-color);">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Kolaborasi Stakeholder</h4>
                    <p class="text-muted">Platform kolaborasi untuk semua pihak terkait konservasi mangrove</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
