@extends('frontend.layouts.master')

@section('title', 'Lokasi Mangrove - SIKOMANG')
@section('meta_description', 'Jelajahi berbagai lokasi kawasan mangrove di Indonesia melalui peta interaktif')

@section('styles')
<style>
    #mainMap {
        height: 500px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .filter-card {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .location-list-item {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border-left: 4px solid var(--primary-color);
        cursor: pointer;
    }

    .location-list-item:hover {
        transform: translateX(10px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }

    .location-list-item.active {
        border-left-color: var(--accent-color);
        background: #f8f9fa;
    }

    .stats-mini {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .stats-mini .stat {
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="text-center text-white" data-aos="fade-up">
            <h1 class="mb-3">Peta Lokasi Mangrove</h1>
            <p class="lead mb-0">Jelajahi {{ $locations->count() }} kawasan mangrove yang terdaftar dalam sistem monitoring kami</p>
        </div>
    </div>
</section>

<!-- Map & List Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Map Column -->
            <div class="col-lg-8">
                <div class="filter-card" data-aos="fade-up">
                    <h5 class="mb-3"><i class="fas fa-map me-2"></i> Peta Interaktif</h5>
                    <div id="mainMap"></div>
                </div>
            </div>

            <!-- Location List Column -->
            <div class="col-lg-4">
                <div class="filter-card" data-aos="fade-up" data-aos-delay="100">
                    <h5 class="mb-3"><i class="fas fa-filter me-2"></i> Filter Lokasi</h5>

                    <!-- District Filter -->
                    <div class="mb-3">
                        <label class="form-label">Kabupaten/Kota:</label>
                        <select class="form-select" id="districtFilter">
                            <option value="">Semua</option>
                            @foreach($districts as $district)
                            <option value="{{ $district }}">{{ $district }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="mb-3">
                        <label class="form-label">Cari Nama:</label>
                        <input type="text" class="form-control" id="searchLocation" placeholder="Ketik nama lokasi...">
                    </div>

                    <button class="btn btn-primary w-100" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i> Reset Filter
                    </button>
                </div>

                <!-- Location List -->
                <div id="locationList" style="max-height: 600px; overflow-y: auto;">
                    @foreach($locations as $location)
                    <div class="location-list-item"
                         data-id="{{ $location->id }}"
                         data-lat="{{ $location->latitude }}"
                         data-lng="{{ $location->longitude }}"
                         data-district="{{ $location->district }}"
                         data-name="{{ $location->name }}"
                         onclick="focusLocation({{ $location->id }}, {{ $location->latitude }}, {{ $location->longitude }})"
                         data-aos="fade-up"
                         data-aos-delay="{{ $loop->index * 50 }}">
                        <h6 class="mb-2">{{ $location->name }}</h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $location->district }}, {{ $location->city }}
                        </p>
                        <div class="stats-mini">
                            <span class="stat">
                                <i class="fas fa-tree me-1"></i> {{ number_format($location->area) }} Ha
                            </span>
                            @if($location->images->count() > 0)
                            <span class="stat">
                                <i class="fas fa-camera me-1"></i> {{ $location->images->count() }}
                            </span>
                            @endif
                        </div>
                        <a href="{{ route('frontend.detail', encode_id($location->id)) }}"
                           class="btn btn-sm btn-primary mt-2"
                           onclick="event.stopPropagation()">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3" data-aos="fade-up">
                <div class="p-4">
                    <i class="fas fa-map-marked-alt fa-3x text-primary mb-3"></i>
                    <h3 class="text-primary">{{ $locations->count() }}</h3>
                    <p class="text-muted mb-0">Total Lokasi</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4">
                    <i class="fas fa-layer-group fa-3x text-success mb-3"></i>
                    <h3 class="text-success">{{ $districts->count() }}</h3>
                    <p class="text-muted mb-0">Kabupaten/Kota</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4">
                    <i class="fas fa-tree fa-3x text-success mb-3"></i>
                    <h3 class="text-success">{{ number_format($locations->sum('area')) }}</h3>
                    <p class="text-muted mb-0">Total Hektar</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="p-4">
                    <i class="fas fa-camera fa-3x text-info mb-3"></i>
                    <h3 class="text-info">{{ $locations->sum(function($loc) { return $loc->images->count(); }) }}</h3>
                    <p class="text-muted mb-0">Foto Dokumentasi</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Initialize map
    var map = L.map('mainMap').setView([-2.5489, 118.0149], 5); // Center of Indonesia

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Store markers
    var markers = {};

    // Add markers for each location
    var locations = @json($locations);

    locations.forEach(function(location) {
        var marker = L.marker([location.latitude, location.longitude])
            .addTo(map)
            .bindPopup(`
                <div style="min-width: 200px;">
                    <h6>${location.name}</h6>
                    <p class="mb-2 small">${location.district}, ${location.city}</p>
                    <p class="mb-2 small"><i class="fas fa-tree"></i> ${parseFloat(location.area).toLocaleString()} Ha</p>
                    <a href="/lokasi/${location.id}" class="btn btn-sm btn-primary w-100">Lihat Detail</a>
                </div>
            `);

        markers[location.id] = marker;
    });

    // Focus on specific location
    function focusLocation(id, lat, lng) {
        map.setView([lat, lng], 14);
        markers[id].openPopup();

        // Highlight in list
        document.querySelectorAll('.location-list-item').forEach(function(item) {
            item.classList.remove('active');
        });
        document.querySelector(`[data-id="${id}"]`).classList.add('active');
    }

    // Filter by district
    document.getElementById('districtFilter').addEventListener('change', function() {
        filterLocations();
    });

    // Filter by search
    document.getElementById('searchLocation').addEventListener('input', function() {
        filterLocations();
    });

    function filterLocations() {
        var district = document.getElementById('districtFilter').value.toLowerCase();
        var search = document.getElementById('searchLocation').value.toLowerCase();

        document.querySelectorAll('.location-list-item').forEach(function(item) {
            var itemDistrict = item.getAttribute('data-district').toLowerCase();
            var itemName = item.getAttribute('data-name').toLowerCase();

            var districtMatch = !district || itemDistrict.includes(district);
            var searchMatch = !search || itemName.includes(search);

            if (districtMatch && searchMatch) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function resetFilters() {
        document.getElementById('districtFilter').value = '';
        document.getElementById('searchLocation').value = '';
        filterLocations();
        map.setView([-2.5489, 118.0149], 5);
    }
</script>
@endsection
