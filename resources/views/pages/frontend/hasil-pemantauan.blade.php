@extends('layouts.frontend.app')

@section('title', 'Pemanfaatan Mangrove - SIKOMANG')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@vite([
    'resources/css/hasil-pemantauan.css',
    'resources/css/hasil-pemantauan-popup.css',
    'resources/js/hasil-pemantauan.js'
    ])

<style>
    /* ============================================================
       VIEW TOGGLE SWITCH
       ============================================================ */
    .view-toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .view-toggle-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4c5250;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        white-space: nowrap;
        transition: color 0.25s ease;
    }

    .view-toggle-label.active-label {
        color: #009966;
    }

    .toggle-track {
        position: relative;
        width: 60px;
        height: 30px;
        background: #e5e7eb;
        border-radius: 99px;
        cursor: pointer;
        transition: background 0.3s ease;
        flex-shrink: 0;
        border: 2px solid transparent;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.12);
    }

    .toggle-track.map-active {
        background: #009966;
    }

    .toggle-thumb {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 22px;
        height: 22px;
        background: #fff;
        border-radius: 50%;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 2px 6px rgba(0,0,0,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toggle-track.map-active .toggle-thumb {
        transform: translateX(30px);
    }

    .toggle-thumb svg {
        width: 11px;
        height: 11px;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .toggle-track.map-active .toggle-thumb svg {
        opacity: 0.9;
    }

    /* ============================================================
       VIEW CONTAINERS
       ============================================================ */
    .view-container {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .view-container.hidden-view {
        display: none;
    }

    /* ============================================================
       MAP VIEW
       ============================================================ */
    #map-view-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0;
        min-height: 0;
    }

    .map-toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #fff;
        border-radius: 12px 12px 0 0;
        border: 1px solid #e5e7eb;
        border-bottom: none;
        flex-wrap: wrap;
    }

    .map-legend-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #4c5250;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-right: 4px;
    }

    .map-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        padding: 4px 10px;
        border-radius: 99px;
        border: 1.5px solid transparent;
        transition: all 0.2s ease;
        user-select: none;
    }

    .map-legend-item:hover {
        background: #f3f4f6;
    }

    .map-legend-item.active-filter {
        border-color: currentColor;
        background: rgba(0,0,0,0.04);
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .map-stats-badge {
        margin-left: auto;
        font-size: 0.75rem;
        color: #6b7280;
        background: #f9fafb;
        padding: 4px 12px;
        border-radius: 99px;
        border: 1px solid #e5e7eb;
    }

    #leaflet-main-map {
        width: 100%;
        height: 540px;
        border-radius: 0 0 12px 12px;
        border: 1px solid #e5e7eb;
        background: #f0f4f0;
    }

    /* Leaflet popup — reuse existing monitoring.css + hasil-pemantauan-popup.css classes */
    #leaflet-main-map .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.18);
        padding: 0;
        overflow: hidden;
        min-width: 240px;
    }

    #leaflet-main-map .leaflet-popup-content {
        margin: 0;
        width: auto !important;
    }

    /* Custom marker pin */
    .custom-marker-pin {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* density badge colours inside map popup */
    .custom-leaflet-popup .density-badge.jarang  { background:#e6f4f1; color:#00724c; }
    .custom-leaflet-popup .density-badge.sedang  { background:#fef9e7; color:#7d6200; }
    .custom-leaflet-popup .density-badge.lebat   { background:#eae6f4; color:#4a3d8f; }

    /* ============================================================
       RICH MAP POPUP  (.mpp-*)
       ============================================================ */
    .mpp-popup {
        width: 300px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        overflow: hidden;
    }

    /* ── image / placeholder ── */
    .mpp-img-wrap,
    .mpp-img-placeholder {
        position: relative;
        width: 100%;
        height: 150px;
        overflow: hidden;
        background: #f0f4f0;
    }
    .mpp-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .35s ease;
    }
    .mpp-popup:hover .mpp-img { transform: scale(1.04); }

    .mpp-img-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #9ca3af;
        font-size: 0.72rem;
    }
    .mpp-img-placeholder svg {
        width: 38px; height: 38px;
        opacity: .45;
    }

    /* ── type ribbon overlaid on image ── */
    .mpp-type-ribbon {
        position: absolute;
        top: 10px; right: 0;
        padding: 3px 10px 3px 8px;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        border-radius: 3px 0 0 3px;
        box-shadow: -2px 2px 6px rgba(0,0,0,.18);
    }

    /* ── body ── */
    .mpp-body {
        padding: 12px 14px 14px;
    }
    .mpp-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.3;
        margin-bottom: 7px;
    }

    /* ── status badges ── */
    .mpp-damage-badge,
    .mpp-ok-badge {
        display: inline-block;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 99px;
        margin-bottom: 10px;
    }
    .mpp-damage-badge {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .mpp-ok-badge {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    /* ── info grid ── */
    .mpp-grid {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 12px;
    }
    .mpp-row {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 0.76rem;
    }
    .mpp-row-full { flex-direction: column; gap: 3px; }
    .mpp-lbl {
        color: #6b7280;
        white-space: nowrap;
        min-width: 90px;
        flex-shrink: 0;
    }
    .mpp-val {
        color: #1f2937;
        font-weight: 500;
        flex: 1;
    }
    .mpp-coords {
        font-size: 0.7rem;
        font-family: monospace;
        color: #4b5563;
    }
    .mpp-empty {
        color: #9ca3af;
        font-style: italic;
        font-size: 0.72rem;
    }

    /* ── species pills ── */
    .mpp-species {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 2px;
    }
    .mpp-species-pill {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        font-size: 0.67rem;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 99px;
    }
    .mpp-species-pill.mpp-more {
        background: #f3f4f6;
        color: #6b7280;
        border-color: #e5e7eb;
    }

    /* ── damage list ── */
    .mpp-damage-list {
        list-style: none;
        padding: 0; margin: 2px 0 0;
    }
    .mpp-damage-list li {
        font-size: 0.72rem;
        color: #b91c1c;
        padding: 1px 0;
        padding-left: 10px;
        position: relative;
    }
    .mpp-damage-list li::before {
        content: '•';
        position: absolute; left: 0;
        color: #fca5a5;
    }
    .mpp-more-item { color: #9ca3af !important; }

    /* ── CTA link ── */
    .mpp-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 7px 0;
        background: #009966;
        color: #fff !important;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none !important;
        transition: background .2s;
    }
    .mpp-link:hover { background: #007a52; }

    /* override leaflet wrapper for wider popup */
    #leaflet-main-map .leaflet-popup-content-wrapper {
        min-width: 300px;
    }

    /* ============================================================
       FILTER COUNT UPDATE
       ============================================================ */
    .map-filter-count {
        font-size: 0.7rem;
        color: #9ca3af;
        margin-left: 2px;
    }

    /* ============================================================
       SEARCH BAR + MAP BUTTON ROW  (override to add toggle)
       ============================================================ */
    .search-map-container {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* When map view is active, hide card-only elements */
    body.map-view-active .filter-tabs {
        display: none;
    }

    body.map-view-active .cards-grid {
        display: none;
    }

    body.map-view-active #map-view-container {
        display: flex;
    }

    /* When grid view, hide map */
    body:not(.map-view-active) #map-view-container {
        display: none;
    }

    /* Density badge on popup */
    .density-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 99px;
        background: #f3f4f6;
        color: #374151;
    }
</style>
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

            {{-- ── Top Row: Search + Map Button + VIEW TOGGLE ──────── --}}
            <div class="search-map-container">
                <div class="search-bar">
                    <input type="text" placeholder="Cari lokasi mangrove" id="searchInput">
                    <button class="btn-search">🔍</button>
                </div>

                <button onclick="openMapModal()" class="btn-map">
                    <x-icons.map-hasil-pemantauan />
                </button>

                {{-- View Toggle Switch --}}
                <div class="view-toggle-wrapper" style="margin-left: auto;">
                    <span class="view-toggle-label active-label" id="label-grid">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:-2px;margin-right:3px">
                            <rect x="1" y="1" width="6" height="6" rx="1.5"/>
                            <rect x="9" y="1" width="6" height="6" rx="1.5"/>
                            <rect x="1" y="9" width="6" height="6" rx="1.5"/>
                            <rect x="9" y="9" width="6" height="6" rx="1.5"/>
                        </svg>
                        Grid
                    </span>

                    <div class="toggle-track" id="viewToggleTrack" onclick="toggleView()" title="Switch view">
                        <div class="toggle-thumb">
                            <svg viewBox="0 0 16 16" fill="#009966">
                                <path d="M8 0C5.2 0 2.9 2.3 2.9 5.1c0 3.8 5.1 10.9 5.1 10.9s5.1-7.1 5.1-10.9C13.1 2.3 10.8 0 8 0zm0 7c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                            </svg>
                        </div>
                    </div>

                    <span class="view-toggle-label" id="label-peta">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:-2px;margin-right:3px">
                            <path d="M8 0C5.2 0 2.9 2.3 2.9 5.1c0 3.8 5.1 10.9 5.1 10.9s5.1-7.1 5.1-10.9C13.1 2.3 10.8 0 8 0zm0 7c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                        </svg>
                        Peta
                    </span>
                </div>
            </div>

            {{-- ── GRID VIEW elements ───────────────────────────────── --}}

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

            {{-- ── MAP VIEW ─────────────────────────────────────────── --}}
            <div id="map-view-container">
                {{-- Map legend + filter toolbar --}}
                <div class="map-toolbar">
                    <span class="map-legend-title">Tipe:</span>

                    <div class="map-legend-item active-filter" data-type="dilindungi" onclick="filterMapByType('dilindungi', this)" style="color: #016630;">
                        <span class="legend-dot" style="background:#016630;"></span>
                        Dilindungi
                        <span class="map-filter-count" id="count-dilindungi">({{ $typeStats['dilindungi'] }})</span>
                    </div>

                    <div class="map-legend-item active-filter" data-type="pengkayaan" onclick="filterMapByType('pengkayaan', this)" style="color: #894B00;">
                        <span class="legend-dot" style="background:#894B00;"></span>
                        Pengkayaan
                        <span class="map-filter-count" id="count-pengkayaan">({{ $typeStats['pengkayaan'] }})</span>
                    </div>

                    <div class="map-legend-item active-filter" data-type="rehabilitasi" onclick="filterMapByType('rehabilitasi', this)" style="color: #9F2D00;">
                        <span class="legend-dot" style="background:#9F2D00;"></span>
                        Rehabilitasi
                        <span class="map-filter-count" id="count-rehabilitasi">({{ $typeStats['rehabilitasi'] }})</span>
                    </div>

                    <div class="map-legend-item active-filter" data-type="restorasi" onclick="filterMapByType('restorasi', this)" style="color: #9F0712;">
                        <span class="legend-dot" style="background:#9F0712;"></span>
                        Restorasi
                        <span class="map-filter-count" id="count-restorasi">({{ $typeStats['restorasi'] }})</span>
                    </div>

                    <div class="map-stats-badge" id="map-stats-badge">
                        Menampilkan <strong id="map-visible-count">{{ $totalSites }}</strong> lokasi
                    </div>
                </div>

                {{-- Leaflet Map --}}
                <div id="leaflet-main-map"></div>
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
    // ================================================================
    // DATA
    // ================================================================
    window.locationsData = @json($locations);
    console.log('Locations data set:', window.locationsData.length, 'locations');

    // ================================================================
    // TYPE COLOR MAP
    // ================================================================
    const TYPE_COLORS = {
        'pengkayaan':  '#894B00',
        'rehabilitasi':'#9F2D00',
        'dilindungi':  '#016630',
        'restorasi':   '#9F0712',
    };

    const TYPE_LABELS = {
        'pengkayaan':  'Pengkayaan',
        'rehabilitasi':'Rehabilitasi',
        'dilindungi':  'Dilindungi',
        'restorasi':   'Restorasi',
    };

    // ================================================================
    // VIEW TOGGLE
    // ================================================================
    let isMapView = false;
    let mapInstance = null;
    let allMarkers = {};  // keyed by type
    let activeFilters = new Set(['dilindungi', 'pengkayaan', 'rehabilitasi', 'restorasi']);

    function toggleView() {
        isMapView = !isMapView;

        const track = document.getElementById('viewToggleTrack');
        const labelGrid = document.getElementById('label-grid');
        const labelPeta = document.getElementById('label-peta');

        if (isMapView) {
            track.classList.add('map-active');
            document.body.classList.add('map-view-active');
            labelGrid.classList.remove('active-label');
            labelPeta.classList.add('active-label');

            // Init map on first open
            requestAnimationFrame(() => {
                initMainMap();
            });
        } else {
            track.classList.remove('map-active');
            document.body.classList.remove('map-view-active');
            labelGrid.classList.add('active-label');
            labelPeta.classList.remove('active-label');
        }
    }

    // ================================================================
    // LEAFLET MAP INIT
    // ================================================================
    function initMainMap() {
        if (mapInstance) {
            mapInstance.invalidateSize();
            return;
        }

        // Default center: Jakarta Bay area
        mapInstance = L.map('leaflet-main-map', {
            center: [-6.12, 106.82],
            zoom: 11,
            zoomControl: true,
            attributionControl: true,
        });

        // Tile layer — OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(mapInstance);

        // Plot all markers
        plotMarkers();

        // Auto-fit bounds to all markers
        fitMapBounds();
    }

    function createMarkerIcon(color, type) {
        const size = 20;
        const svgPin = `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${Math.round(size * 1.4)}" viewBox="0 0 20 28">
            <path d="M10 0C4.5 0 0 4.5 0 10c0 7.5 10 18 10 18s10-10.5 10-18C20 4.5 15.5 0 10 0z"
                fill="${color}" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
            <circle cx="10" cy="10" r="4" fill="rgba(255,255,255,0.85)"/>
        </svg>`;

        return L.divIcon({
            html: svgPin,
            className: 'custom-marker-pin',
            iconSize: [size, Math.round(size * 1.4)],
            iconAnchor: [size / 2, Math.round(size * 1.4)],
            popupAnchor: [0, -Math.round(size * 1.4)],
        });
    }

    function buildPopupContent(loc) {
        const rawType     = (loc.type || '').toLowerCase().trim();
        const typeKey     = Object.keys(TYPE_COLORS).find(k => rawType.includes(k)) || 'pengkayaan';
        const color       = TYPE_COLORS[typeKey];
        const typeLabel   = TYPE_LABELS[typeKey] || loc.type;

        // ── Data fields ────────────────────────────────────────────
        const area        = loc.area        || 'Belum diidentifikasi';
        const densityRaw  = (loc.density    || '').toLowerCase();
        const densityLabel = densityRaw.charAt(0).toUpperCase() + densityRaw.slice(1);
        const year        = loc.year        || '-';
        const coords      = loc.coords      || `${loc.latitude}, ${loc.longitude}`;
        const species     = loc.species     || null;
        const damageCount = parseInt(loc.damage_count) || 0;
        const damages     = Array.isArray(loc.damages) ? loc.damages : [];
        const detailUrl   = `/monitoring/lokasi/${loc.slug}`;

        // ── Image ──────────────────────────────────────────────────
        const images     = Array.isArray(loc.images) ? loc.images : [];
        const imgSrc     = images.length > 0 ? images[0] : null;
        const imgHtml    = imgSrc
            ? `<div class="mpp-img-wrap">
                   <img src="${imgSrc}" alt="${loc.name}" class="mpp-img"
                        onerror="this.parentElement.style.display='none'">
                   <div class="mpp-type-ribbon" style="background:${color};">${typeLabel}</div>
               </div>`
            : `<div class="mpp-img-placeholder" style="border-top:4px solid ${color};">
                   <svg viewBox="0 0 48 48" fill="none" stroke="${color}" stroke-width="1.5">
                       <rect x="6" y="10" width="36" height="28" rx="3"/>
                       <circle cx="18" cy="21" r="4"/>
                       <path d="M6 32l10-8 8 6 6-5 12 9"/>
                   </svg>
                   <span>Belum ada foto</span>
                   <div class="mpp-type-ribbon" style="background:${color};">${typeLabel}</div>
               </div>`;

        // ── Damage badge ───────────────────────────────────────────
        const damageBadge = damageCount > 0
            ? `<span class="mpp-damage-badge">⚠ ${damageCount} kerusakan aktif</span>`
            : `<span class="mpp-ok-badge">✓ Kondisi baik</span>`;

        // ── Species pills ──────────────────────────────────────────
        const speciesHtml = species && species !== 'Belum diidentifikasi'
            ? `<div class="mpp-species">${
                  species.split(',').slice(0, 3).map(s =>
                      `<span class="mpp-species-pill">${s.trim()}</span>`
                  ).join('')
              }${species.split(',').length > 3 ? `<span class="mpp-species-pill mpp-more">+${species.split(',').length - 3}</span>` : ''}</div>`
            : `<span class="mpp-empty">Belum diidentifikasi</span>`;

        return `
        <div class="mpp-popup">
            ${imgHtml}
            <div class="mpp-body">
                <div class="mpp-title">${loc.name || 'Lokasi Mangrove'}</div>
                ${damageBadge}

                <div class="mpp-grid">
                    <div class="mpp-row">
                        <span class="mpp-lbl">📅 Tahun</span>
                        <span class="mpp-val">${year}</span>
                    </div>
                    <div class="mpp-row">
                        <span class="mpp-lbl">📐 Luas</span>
                        <span class="mpp-val">${area}</span>
                    </div>
                    <div class="mpp-row">
                        <span class="mpp-lbl">🌿 Kerapatan</span>
                        <span class="mpp-val">
                            <span class="density-badge ${densityRaw}">${densityLabel}</span>
                        </span>
                    </div>
                    <div class="mpp-row">
                        <span class="mpp-lbl">📍 Koordinat</span>
                        <span class="mpp-val mpp-coords">${coords}</span>
                    </div>
                    <div class="mpp-row mpp-row-full">
                        <span class="mpp-lbl">🌳 Spesies</span>
                        <span class="mpp-val">${speciesHtml}</span>
                    </div>
                    ${damageCount > 0 && damages.length > 0 ? `
                    <div class="mpp-row mpp-row-full">
                        <span class="mpp-lbl">⚠ Kerusakan</span>
                        <span class="mpp-val">
                            <ul class="mpp-damage-list">
                                ${damages.slice(0,2).map(d => `<li>${d}</li>`).join('')}
                                ${damages.length > 2 ? `<li class="mpp-more-item">+${damages.length - 2} lainnya...</li>` : ''}
                            </ul>
                        </span>
                    </div>` : ''}
                </div>

                <a href="${detailUrl}" class="popup-link mpp-link">
                    <span>Lihat Detail Lengkap</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" style="vertical-align:-1px">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>`;
    }

    function getTypeKey(loc) {
        const raw = (loc.type || '').toLowerCase();
        return Object.keys(TYPE_COLORS).find(k => raw.includes(k)) || 'pengkayaan';
    }

    function plotMarkers() {
        // Clear existing
        Object.values(allMarkers).forEach(group => {
            group.forEach(m => mapInstance.removeLayer(m));
        });
        allMarkers = {};

        let validCount = 0;

        window.locationsData.forEach(loc => {
            const lat = parseFloat(loc.latitude);
            const lng = parseFloat(loc.longitude);

            if (!lat || !lng || isNaN(lat) || isNaN(lng)) return;

            const typeKey = getTypeKey(loc);
            const color   = TYPE_COLORS[typeKey];
            const icon    = createMarkerIcon(color, typeKey);

            const marker = L.marker([lat, lng], { icon })
                .bindPopup(buildPopupContent(loc), {
                    maxWidth: 340,
                    className: 'custom-leaflet-popup',
                });

            if (!allMarkers[typeKey]) allMarkers[typeKey] = [];
            allMarkers[typeKey].push(marker);

            if (activeFilters.has(typeKey)) {
                marker.addTo(mapInstance);
                validCount++;
            }
        });

        updateMapStatsDisplay();
    }

    function fitMapBounds() {
        const allLatLngs = [];
        window.locationsData.forEach(loc => {
            const lat = parseFloat(loc.latitude);
            const lng = parseFloat(loc.longitude);
            if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                allLatLngs.push([lat, lng]);
            }
        });

        if (allLatLngs.length > 0) {
            const bounds = L.latLngBounds(allLatLngs);
            mapInstance.fitBounds(bounds, { padding: [40, 40], maxZoom: 13 });
        }
    }

    // ================================================================
    // MAP TYPE FILTER
    // ================================================================
    function filterMapByType(type, el) {
        if (activeFilters.has(type)) {
            // Deactivate — but keep at least one active
            if (activeFilters.size <= 1) return;
            activeFilters.delete(type);
            el.classList.remove('active-filter');

            // Remove markers from map
            if (allMarkers[type]) {
                allMarkers[type].forEach(m => mapInstance.removeLayer(m));
            }
        } else {
            // Activate
            activeFilters.add(type);
            el.classList.add('active-filter');

            // Add markers to map
            if (allMarkers[type]) {
                allMarkers[type].forEach(m => m.addTo(mapInstance));
            }
        }

        updateMapStatsDisplay();
    }

    function updateMapStatsDisplay() {
        let visible = 0;
        activeFilters.forEach(type => {
            visible += (allMarkers[type] || []).length;
        });
        const el = document.getElementById('map-visible-count');
        if (el) el.textContent = visible;
    }
</script>
@endpush
