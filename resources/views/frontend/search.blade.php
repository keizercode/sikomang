@extends('frontend.layouts.master')

@section('title', 'Hasil Pencarian: ' . $query . ' - SIKOMANG')
@section('meta_description', 'Hasil pencarian untuk: ' . $query)

@section('content')
<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="text-white" data-aos="fade-up">
            <h1 class="mb-3">Hasil Pencarian</h1>
            <p class="lead mb-4">Menampilkan hasil untuk: <strong>"{{ $query }}"</strong></p>

            <!-- Search Form -->
            <form action="{{ route('frontend.search') }}" method="GET" class="d-flex" style="max-width: 600px;">
                <input type="text" name="q" class="form-control form-control-lg me-2"
                       placeholder="Cari lokasi mangrove..."
                       value="{{ $query }}"
                       required>
                <button type="submit" class="btn btn-light btn-lg">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-5">
    <div class="container">
        @if($locations->count() > 0)
        <!-- Results Count -->
        <div class="mb-4" data-aos="fade-up">
            <p class="text-muted">
                Ditemukan <strong>{{ $locations->total() }}</strong> lokasi untuk pencarian Anda
            </p>
        </div>

        <!-- Results Grid -->
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
                        <h5 class="card-title">
                            @php
                                // Highlight search term in title
                                $highlighted = str_ireplace($query, '<mark>' . $query . '</mark>', $location->name);
                            @endphp
                            {!! $highlighted !!}
                        </h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $location->district }}, {{ $location->city }}, {{ $location->province }}
                        </p>
                        @if($location->address && stripos($location->address, $query) !== false)
                        <p class="text-muted small mb-3">
                            @php
                                $highlightedAddress = str_ireplace($query, '<mark>' . $query . '</mark>', $location->address);
                            @endphp
                            {!! $highlightedAddress !!}
                        </p>
                        @endif
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
            {{ $locations->appends(['q' => $query])->links() }}
        </div>
        @else
        <!-- No Results -->
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-search fa-4x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Tidak Ada Hasil Ditemukan</h3>
            <p class="text-muted mb-4">
                Maaf, kami tidak menemukan hasil untuk "<strong>{{ $query }}</strong>".
                Silakan coba dengan kata kunci lain.
            </p>

            <!-- Search Suggestions -->
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-3">Tips Pencarian:</h5>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Gunakan kata kunci yang lebih umum
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Periksa ejaan kata kunci
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Coba gunakan nama kabupaten/kota
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Gunakan kata kunci yang berbeda
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-5">
                <h5 class="mb-3">Atau jelajahi halaman lain:</h5>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('frontend.locations') }}" class="btn btn-primary">
                        <i class="fas fa-map-marked-alt me-2"></i> Semua Lokasi
                    </a>
                    <a href="{{ route('frontend.gallery') }}" class="btn btn-primary">
                        <i class="fas fa-images me-2"></i> Galeri
                    </a>
                    <a href="{{ route('frontend.monitoring') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line me-2"></i> Monitoring
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Popular Searches -->
@if($locations->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <h4 class="mb-4 text-center" data-aos="fade-up">Pencarian Populer</h4>
        <div class="text-center" data-aos="fade-up" data-aos-delay="100">
            <a href="{{ route('frontend.search', ['q' => 'jakarta']) }}" class="btn btn-outline-primary m-2">Jakarta</a>
            <a href="{{ route('frontend.search', ['q' => 'surabaya']) }}" class="btn btn-outline-primary m-2">Surabaya</a>
            <a href="{{ route('frontend.search', ['q' => 'mangrove']) }}" class="btn btn-outline-primary m-2">Mangrove</a>
            <a href="{{ route('frontend.search', ['q' => 'konservasi']) }}" class="btn btn-outline-primary m-2">Konservasi</a>
            <a href="{{ route('frontend.search', ['q' => 'pesisir']) }}" class="btn btn-outline-primary m-2">Pesisir</a>
        </div>
    </div>
</section>
@endif
@endsection

@section('styles')
<style>
    mark {
        background-color: #fff3cd;
        padding: 2px 4px;
        border-radius: 3px;
        font-weight: 600;
    }
</style>
@endsection
