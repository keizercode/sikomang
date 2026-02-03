@extends('layouts.app')

@section('title', 'Pemanfaatan Mangrove - SIKOMANG')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@vite([
    'resources/css/hasil-pemantauan.css',
    'resources/css/hasil-pemantauan-popup.css',
    'resources/js/hasil-pemantauan.js'
    ])
@endpush

@section('content')
<div class="hasil-pemantauan-page">
    <div class="mangrove-container">
        {{-- Sidebar --}}
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Pemanfaatan Mangrove</h2>
            </div>

            <div class="stats-section">
                <h3>Total Pemanfaatan Kawasan Mangrove</h3>
                <div class="stat-number primary">23</div>
                <div class="stat-label">Pemanfaatan Aktif (ha)</div>
                <div class="stat-number secondary">297</div>
            </div>

            <div class="info-section">
                <div class="info-header">
                    <h3>Rekomendasi Pengelolaan</h3>
                    <div class="info-icon-wrapper">
                        <img
                            src="https://res.cloudinary.com/dmcvht1vr/image/upload/v1769407250/aec8dafa-beba-46ec-979c-fb12d5f5af5a.png"
                            alt="Info"
                            class="info-icon-img"
                            onclick="toggleMatrix()"
                        >
                    </div>
                </div>

                <div class="recommendation-tags">
                    <span class="tag tag-green">Dilindungi: 6</span>
                    <span class="tag tag-yellow">Pengkayaan: 11</span>
                    <span class="tag tag-orange">Pengkayaan / Rehabilitasi: 1</span>
                    <span class="tag tag-red">Rehabilitasi: 3</span>
                </div>
            </div>

            <div class="geography-section">
                <h3>Sebaran Geografis</h3>
                <ul class="location-list">
                    <li>
                        <span class="location-name">Kecamatan Penjaringan</span>
                        <span class="location-count">11 Sites</span>
                    </li>
                    <li>
                        <span class="location-name">Kecamatan Cilincing</span>
                        <span class="location-count">5 Sites</span>
                    </li>
                    <li>
                        <span class="location-name">Kepulauan Seribu Utara</span>
                        <span class="location-count">3 Sites</span>
                    </li>
                    <li>
                        <span class="location-name">Kepulauan Seribu Selatan</span>
                        <span class="location-count">4 Sites</span>
                    </li>
                </ul>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="main-content">
            {{-- Search + Map Button --}}
            <div class="search-map-container">
                <div class="search-bar">
                    <input type="text" placeholder="Cari lokasi mangrove" id="searchInput">
                    <button class="btn-search">🔍</button>
                </div>

                <button onclick="openMapModal()" class="btn-map">
                    <x-icons.map-hasil-pemantauan />
                </button>
            </div>

            {{-- Filter Tabs by Group --}}
            <div class="filter-tabs">
                <button class="tab active" onclick="filterByGroup('all')">Semua</button>
                <button class="tab" onclick="filterByGroup('penjaringan')">Penjaringan, Jakarta Utara</button>
                <button class="tab" onclick="filterByGroup('cilincing')">Cilincing, Jakarta Utara</button>
                <button class="tab" onclick="filterByGroup('kep-seribu-utara')">Kep. Seribu Utara</button>
                <button class="tab" onclick="filterByGroup('kep-seribu-selatan')">Kep. Seribu Selatan</button>
            </div>

            {{-- Cards Grid --}}
            <div class="cards-grid">
                @foreach($locations as $location)
                    <x-location-card :location="$location" />
                @endforeach
            </div>
        </main>
    </div>

    {{-- Modals --}}
    <x-matrix-modal />
    <x-map-modal />
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // CRITICAL: Set locations data BEFORE loading the script
    window.locationsData = @json($locations);
    console.log('Locations data set:', window.locationsData.length, 'locations');
</script>

@endpush
