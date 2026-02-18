@extends('layouts.frontend.app')

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

            {{-- ── Statistik Utama (dinamis dari database) ─────────── --}}
            <div class="stats-section">
                <h3>Total Pemanfaatan Kawasan Mangrove</h3>

                <div class="stat-number primary">{{ $totalSites }}</div>
                <div class="stat-label">Total Titik Monitoring</div>

                <div class="stat-number secondary">{{ $totalArea }}</div>
                <div class="stat-label">Total Luas (ha)</div>
            </div>

            {{-- ── Rekomendasi Pengelolaan (dinamis dari database) ─── --}}
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
                    @if($typeStats['dilindungi'] > 0)
                        <span class="tag tag-green">Dilindungi: {{ $typeStats['dilindungi'] }}</span>
                    @endif
                    @if($typeStats['pengkayaan'] > 0)
                        <span class="tag tag-yellow">Pengkayaan: {{ $typeStats['pengkayaan'] }}</span>
                    @endif
                    @if($typeStats['rehabilitasi'] > 0)
                        <span class="tag tag-red">Rehabilitasi: {{ $typeStats['rehabilitasi'] }}</span>
                    @endif
                    @if($typeStats['restorasi'] > 0)
                        <span class="tag tag-orange">Restorasi: {{ $typeStats['restorasi'] }}</span>
                    @endif
                </div>
            </div>

            {{-- ── Sebaran Geografis (dinamis dari database) ───────── --}}
            <div class="geography-section">
                <h3>Sebaran Geografis</h3>
                <ul class="location-list">
                    <li>
                        <span class="location-name">Kecamatan Penjaringan</span>
                        <span class="location-count">{{ $regionStats['penjaringan'] }} Sites</span>
                    </li>
                    <li>
                        <span class="location-name">Kecamatan Cilincing</span>
                        <span class="location-count">{{ $regionStats['cilincing'] }} Sites</span>
                    </li>
                    <li>
                        <span class="location-name">Kepulauan Seribu Utara</span>
                        <span class="location-count">{{ $regionStats['kep_seribu_utara'] }} Sites</span>
                    </li>
                    <li>
                        <span class="location-name">Kepulauan Seribu Selatan</span>
                        <span class="location-count">{{ $regionStats['kep_seribu_selatan'] }} Sites</span>
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
                <button class="tab active" onclick="filterByGroup('all')">
                    Semua
                    <span class="tab-count">{{ $totalSites }}</span>
                </button>
                <button class="tab" onclick="filterByGroup('penjaringan')">
                    Penjaringan, Jakarta Utara
                    <span class="tab-count">{{ $regionStats['penjaringan'] }}</span>
                </button>
                <button class="tab" onclick="filterByGroup('cilincing')">
                    Cilincing, Jakarta Utara
                    <span class="tab-count">{{ $regionStats['cilincing'] }}</span>
                </button>
                <button class="tab" onclick="filterByGroup('kep-seribu-utara')">
                    Kep. Seribu Utara
                    <span class="tab-count">{{ $regionStats['kep_seribu_utara'] }}</span>
                </button>
                <button class="tab" onclick="filterByGroup('kep-seribu-selatan')">
                    Kep. Seribu Selatan
                    <span class="tab-count">{{ $regionStats['kep_seribu_selatan'] }}</span>
                </button>
            </div>

            {{-- Cards Grid --}}
            <div class="cards-grid">
                @forelse($locations as $location)
                    <x-shared.location-card :location="$location" />
                @empty
                    <div class="empty-state">
                        <p>Belum ada data lokasi monitoring.</p>
                    </div>
                @endforelse
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
    window.locationsData = @json($locations);
    console.log('Locations data set:', window.locationsData.length, 'locations');
</script>
@endpush
