@extends('frontend.layouts.master')

@section('title', $location->name . ' - SIKOMANG')
@section('meta_description', 'Detail informasi kawasan mangrove ' . $location->name . ' di ' . $location->district . ', ' . $location->city)

@section('styles')
<style>
    .detail-hero {
        background: linear-gradient(135deg, rgba(45, 122, 94, 0.95) 0%, rgba(74, 157, 122, 0.95) 100%);
        color: white;
        padding: 80px 0 40px;
    }

    .detail-tabs .nav-link {
        color: var(--primary-color);
        font-weight: 500;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 15px 25px;
        transition: all 0.3s;
    }

    .detail-tabs .nav-link:hover {
        color: var(--secondary-color);
    }

    .detail-tabs .nav-link.active {
        color: var(--primary-color);
        background: transparent;
        border-bottom-color: var(--accent-color);
    }

    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        border-left: 4px solid var(--primary-color);
    }

    .info-card h5 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        width: 180px;
        flex-shrink: 0;
    }

    .info-value {
        color: #333;
        flex: 1;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .gallery-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.3s;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .gallery-item:hover {
        transform: scale(1.05);
    }

    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .gallery-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        color: white;
        padding: 30px 15px 10px;
        font-size: 0.9rem;
    }

    .damage-card {
        border-left: 4px solid #ffa726;
        border-radius: 10px;
        padding: 20px;
        background: white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .damage-card.priority-low {
        border-left-color: #4caf50;
    }

    .damage-card.priority-medium {
        border-left-color: #ffa726;
    }

    .damage-card.priority-high {
        border-left-color: #f44336;
    }

    .action-item {
        background: #f8f9fa;
        border-left: 3px solid var(--primary-color);
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    #map {
        height: 400px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .species-badge {
        display: inline-block;
        background: #e8f5e9;
        color: #2e7d32;
        padding: 8px 15px;
        border-radius: 20px;
        margin: 5px;
        font-size: 0.9rem;
    }

    .related-card {
        transition: transform 0.3s;
    }

    .related-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<!-- Detail Hero -->
<section class="detail-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="mb-3">{{ $location->name }}</h1>
                <p class="mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $location->address }}, {{ $location->district }}, {{ $location->city }}, {{ $location->province }}
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-tree me-1"></i> {{ number_format($location->area) }} Hektar
                    </span>
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="fas fa-camera me-1"></i> {{ $location->images->count() }} Foto
                    </span>
                    @if($location->damages->count() > 0)
                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ $location->damages->count() }} Laporan
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                <a href="{{ route('frontend.locations') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Detail Content -->
<section class="py-5">
    <div class="container">
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs detail-tabs mb-4" role="tablist" data-aos="fade-up">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#info">
                    <i class="fas fa-info-circle me-2"></i> Informasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#species">
                    <i class="fas fa-seedling me-2"></i> Keanekaragaman
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#activities">
                    <i class="fas fa-running me-2"></i> Aktivitas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#programs">
                    <i class="fas fa-project-diagram me-2"></i> Program
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#gallery">
                    <i class="fas fa-images me-2"></i> Galeri
                </a>
            </li>
            @if($location->damages->count() > 0)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#damages">
                    <i class="fas fa-exclamation-triangle me-2"></i> Kerusakan
                </a>
            </li>
            @endif
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content">
            <!-- Tab: Informasi Dasar -->
            <div class="tab-pane fade show active" id="info">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="info-card" data-aos="fade-up">
                            <h5><i class="fas fa-map-marker-alt"></i> Informasi Lokasi</h5>
                            <div class="info-row">
                                <div class="info-label">Nama Lokasi</div>
                                <div class="info-value">{{ $location->name }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Alamat</div>
                                <div class="info-value">{{ $location->address }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Kecamatan</div>
                                <div class="info-value">{{ $location->district }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Kota/Kabupaten</div>
                                <div class="info-value">{{ $location->city }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Provinsi</div>
                                <div class="info-value">{{ $location->province }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Luas Kawasan</div>
                                <div class="info-value">{{ number_format($location->area, 2) }} Hektar</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Koordinat</div>
                                <div class="info-value">{{ $location->latitude }}, {{ $location->longitude }}</div>
                            </div>
                        </div>

                        @if($location->locationDetail && $location->locationDetail->description)
                        <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                            <h5><i class="fas fa-file-alt"></i> Deskripsi</h5>
                            <div class="text-muted" style="line-height: 1.8;">
                                {!! nl2br(e($location->locationDetail->description)) !!}
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <div class="info-card" data-aos="fade-up" data-aos-delay="200">
                            <h5><i class="fas fa-map"></i> Peta Lokasi</h5>
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Keanekaragaman Hayati -->
            <div class="tab-pane fade" id="species">
                <div class="row">
                    @if($location->locationDetail)
                    <div class="col-lg-6">
                        <div class="info-card" data-aos="fade-up">
                            <h5><i class="fas fa-tree"></i> Vegetasi</h5>
                            @if(count($location->locationDetail->vegetation) > 0)
                                <div class="mt-3">
                                    @foreach($location->locationDetail->vegetation as $plant)
                                        <span class="species-badge">{{ $plant }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Belum ada data vegetasi yang tercatat.</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                            <h5><i class="fas fa-paw"></i> Fauna</h5>
                            @if(count($location->locationDetail->fauna) > 0)
                                <div class="mt-3">
                                    @foreach($location->locationDetail->fauna as $animal)
                                        <span class="species-badge">{{ $animal }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Belum ada data fauna yang tercatat.</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada data keanekaragaman hayati yang tercatat untuk lokasi ini.
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tab: Aktivitas -->
            <div class="tab-pane fade" id="activities">
                @if($location->locationDetail && $location->locationDetail->activities_detail)
                <div class="info-card" data-aos="fade-up">
                    <h5><i class="fas fa-running"></i> Aktivitas di Sekitar Kawasan</h5>

                    @if($location->locationDetail->activities_detail['description'])
                    <div class="mb-4">
                        <h6 class="text-muted">Deskripsi Umum:</h6>
                        <p class="text-justify">{{ $location->locationDetail->activities_detail['description'] }}</p>
                    </div>
                    @endif

                    @if(isset($location->locationDetail->activities_detail['items']) && count($location->locationDetail->activities_detail['items']) > 0)
                    <h6 class="text-muted mb-3">Daftar Aktivitas:</h6>
                    <ul class="list-unstyled">
                        @foreach($location->locationDetail->activities_detail['items'] as $activity)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ $activity }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Belum ada data aktivitas yang tercatat untuk lokasi ini.
                </div>
                @endif
            </div>

            <!-- Tab: Program & Pemanfaatan -->
            <div class="tab-pane fade" id="programs">
                @if($location->locationDetail)
                <div class="row">
                    <div class="col-lg-4">
                        <div class="info-card" data-aos="fade-up">
                            <h5><i class="fas fa-leaf"></i> Pemanfaatan Hutan</h5>
                            @if(count($location->locationDetail->forest_utilization) > 0)
                                <ul class="list-unstyled mt-3">
                                    @foreach($location->locationDetail->forest_utilization as $util)
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        {{ $util }}
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mt-3">Belum ada data pemanfaatan.</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                            <h5><i class="fas fa-project-diagram"></i> Program</h5>
                            @if(count($location->locationDetail->programs) > 0)
                                <ul class="list-unstyled mt-3">
                                    @foreach($location->locationDetail->programs as $program)
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        {{ $program }}
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mt-3">Belum ada program yang tercatat.</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="info-card" data-aos="fade-up" data-aos-delay="200">
                            <h5><i class="fas fa-users"></i> Stakeholder</h5>
                            @if(count($location->locationDetail->stakeholders) > 0)
                                <ul class="list-unstyled mt-3">
                                    @foreach($location->locationDetail->stakeholders as $stakeholder)
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-primary me-2"></i>
                                        {{ $stakeholder }}
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mt-3">Belum ada stakeholder yang tercatat.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Belum ada data program dan pemanfaatan yang tercatat untuk lokasi ini.
                </div>
                @endif
            </div>

            <!-- Tab: Galeri -->
            <div class="tab-pane fade" id="gallery">
                @if($location->images->count() > 0)
                <div class="gallery-grid" data-aos="fade-up">
                    @foreach($location->images as $image)
                    <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#imageModal{{ $loop->index }}">
                        <img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->caption }}">
                        @if($image->caption)
                        <div class="gallery-caption">{{ $image->caption }}</div>
                        @endif
                    </div>

                    <!-- Modal for each image -->
                    <div class="modal fade" id="imageModal{{ $loop->index }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 bg-white rounded-circle" data-bs-dismiss="modal"></button>
                                    <img src="{{ Storage::url($image->image_path) }}" alt="{{ $image->caption }}" class="w-100">
                                    @if($image->caption)
                                    <div class="p-3">
                                        <p class="mb-0">{{ $image->caption }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-camera me-2"></i>
                    Belum ada foto yang tersedia untuk lokasi ini.
                </div>
                @endif
            </div>

            <!-- Tab: Kerusakan -->
            @if($location->damages->count() > 0)
            <div class="tab-pane fade" id="damages">
                @foreach($location->damages as $damage)
                <div class="damage-card priority-{{ $damage->priority }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0">{{ $damage->title }}</h5>
                        <div class="text-end">
                            @if($damage->priority == 'low')
                                <span class="badge bg-success">Prioritas Rendah</span>
                            @elseif($damage->priority == 'medium')
                                <span class="badge bg-warning">Prioritas Sedang</span>
                            @else
                                <span class="badge bg-danger">Prioritas Tinggi</span>
                            @endif

                            @if($damage->status == 'pending')
                                <span class="badge bg-secondary ms-2">Pending</span>
                            @elseif($damage->status == 'in_progress')
                                <span class="badge bg-info ms-2">Dalam Proses</span>
                            @else
                                <span class="badge bg-success ms-2">Selesai</span>
                            @endif
                        </div>
                    </div>

                    <p class="text-muted mb-3">{{ $damage->description }}</p>

                    @if($damage->actions->count() > 0)
                    <h6 class="mb-3"><i class="fas fa-tools me-2"></i> Aksi Penanganan:</h6>
                    @foreach($damage->actions as $action)
                    <div class="action-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                {{ $action->action_description }}
                            </div>
                            @if($action->action_date)
                            <small class="text-muted">{{ \Carbon\Carbon::parse($action->action_date)->format('d M Y') }}</small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Related Locations -->
@if($relatedLocations->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Lokasi Lainnya</h2>
            <p>Jelajahi kawasan mangrove lainnya yang mungkin menarik bagi Anda</p>
        </div>

        <div class="row g-4">
            @foreach($relatedLocations as $related)
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="location-card card related-card">
                    @if($related->images->count() > 0)
                        <img src="{{ Storage::url($related->images->first()->image_path) }}"
                             alt="{{ $related->name }}"
                             class="card-img-top">
                    @else
                        <img src="{{ asset('assets/images/default-mangrove.jpg') }}"
                             alt="{{ $related->name }}"
                             class="card-img-top">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $related->name }}</h5>
                        <p class="text-muted mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $related->district }}, {{ $related->city }}
                        </p>
                        <a href="{{ route('frontend.detail', encode_id($related->id)) }}"
                           class="btn btn-primary w-100">
                            <i class="fas fa-eye me-2"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@section('scripts')
<script>
    // Initialize Leaflet Map
    var map = L.map('map').setView([{{ $location->latitude }}, {{ $location->longitude }}], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([{{ $location->latitude }}, {{ $location->longitude }}]).addTo(map);
    marker.bindPopup("<b>{{ $location->name }}</b><br>{{ $location->address }}").openPopup();
</script>
@endsection
