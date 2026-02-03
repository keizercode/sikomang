@extends('frontend.layouts.master')

@section('title', 'Galeri Foto - SIKOMANG')
@section('meta_description', 'Galeri foto dokumentasi kawasan mangrove dari berbagai lokasi di Indonesia')

@section('styles')
<style>
    .gallery-container {
        column-count: 3;
        column-gap: 20px;
    }

    @media (max-width: 992px) {
        .gallery-container {
            column-count: 2;
        }
    }

    @media (max-width: 576px) {
        .gallery-container {
            column-count: 1;
        }
    }

    .gallery-item-masonry {
        break-inside: avoid;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        cursor: pointer;
    }

    .gallery-item-masonry:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .gallery-item-masonry img {
        width: 100%;
        display: block;
        border-radius: 15px;
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        color: white;
        padding: 40px 20px 15px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .gallery-item-masonry:hover .gallery-overlay {
        opacity: 1;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 40px;
    }

    .filter-btn {
        padding: 10px 25px;
        border: 2px solid var(--primary-color);
        background: white;
        color: var(--primary-color);
        border-radius: 25px;
        font-weight: 500;
        transition: all 0.3s;
        cursor: pointer;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--primary-color);
        color: white;
    }

    .modal-img {
        max-height: 80vh;
        width: auto;
        max-width: 100%;
        margin: 0 auto;
        display: block;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="text-center text-white" data-aos="fade-up">
            <h1 class="mb-3">Galeri Foto Mangrove</h1>
            <p class="lead mb-0">{{ $images->total() }} foto dokumentasi dari berbagai kawasan mangrove</p>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-5">
    <div class="container">
        <!-- Filter Buttons -->
        <div class="filter-buttons" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-th me-2"></i> Semua Foto
            </button>
            @foreach($locationImages as $locationId => $imgs)
                @php
                    $location = $imgs->first()->location;
                @endphp
                <button class="filter-btn" data-filter="loc-{{ $locationId }}">
                    <i class="fas fa-map-marker-alt me-2"></i> {{ $location->name }}
                </button>
            @endforeach
        </div>

        <!-- Gallery Grid -->
        @if($images->count() > 0)
        <div class="gallery-container" data-aos="fade-up" data-aos-delay="200">
            @foreach($images as $image)
            <div class="gallery-item-masonry"
                 data-category="loc-{{ $image->location_id }}"
                 data-bs-toggle="modal"
                 data-bs-target="#imageModal{{ $loop->index }}">
                <img src="{{ Storage::url($image->image_path) }}"
                     alt="{{ $image->caption ?? $image->location->name }}"
                     loading="lazy">
                <div class="gallery-overlay">
                    <h6 class="mb-1">{{ $image->location->name }}</h6>
                    @if($image->caption)
                    <p class="small mb-0">{{ $image->caption }}</p>
                    @endif
                </div>
            </div>

            <!-- Modal for full image -->
            <div class="modal fade" id="imageModal{{ $loop->index }}" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content bg-dark">
                        <div class="modal-header border-0">
                            <h5 class="modal-title text-white">{{ $image->location->name }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <img src="{{ Storage::url($image->image_path) }}"
                                 alt="{{ $image->caption ?? $image->location->name }}"
                                 class="modal-img">
                            @if($image->caption)
                            <p class="text-white text-center mt-3">{{ $image->caption }}</p>
                            @endif
                        </div>
                        <div class="modal-footer border-0">
                            <a href="{{ route('frontend.detail', encode_id($image->location_id)) }}"
                               class="btn btn-primary">
                                <i class="fas fa-map-marker-alt me-2"></i> Lihat Lokasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $images->links() }}
        </div>
        @else
        <div class="text-center py-5" data-aos="fade-up">
            <i class="fas fa-images fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Belum ada foto dalam galeri</h4>
            <p class="text-muted">Foto-foto dari admin akan ditampilkan di sini.</p>
        </div>
        @endif
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h3 class="mb-3">Punya Foto Mangrove?</h3>
                <p class="text-muted mb-0">Bagikan dokumentasi Anda untuk memperkaya database kami tentang ekosistem mangrove di Indonesia.</p>
            </div>
            <div class="col-lg-4 text-lg-end text-start mt-3 mt-lg-0" data-aos="fade-left">
                <a href="{{ route('frontend.contact') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload me-2"></i> Kirim Foto
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item-masonry');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            // Filter items
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                    // Add fade in animation
                    item.style.animation = 'fadeIn 0.5s';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
