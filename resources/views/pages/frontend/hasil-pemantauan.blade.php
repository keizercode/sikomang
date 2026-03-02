@extends('layouts.frontend.app')

@section('title', 'Pemanfaatan Mangrove - SIKOMANG')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
{{-- @vite([
    'resources/css/hasil-pemantauan.css',
    'resources/css/hasil-pemantauan-popup.css',
    'resources/js/hasil-pemantauan.js'
]) --}}

<style>
    /* Sidebar sticky dengan scroll sendiri */
.hasil-pemantauan-page .sidebar {
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
}
    /* ============================================================
       NAVBAR INJECTED ACTIONS — search + toggle
       ============================================================ */
    .nav-actions-wrap {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        min-width: 0;
    }

    /* ── Search ── */
    .nav-search-form {
        position: relative;
        display: flex;
        align-items: center;
        background: #f6f8f7;
        border: 1.5px solid #e3ede9;
        border-radius: 10px;
        height: 50px;
    padding: 0 12px 0 38px;
    min-width: 425px;   /* ← lebar minimum */
    max-width: 520px;   /* ← lebar maksimum */
    width: 100%;
    }

    .nav-search-form:focus-within {
        border-color: #009966;
        box-shadow: 0 0 0 3px rgba(0,153,102,.10);
        background: #fff;
    }

    .nav-search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
        display: flex;
    }

    .nav-search-form input {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 1rem;
    color: #1f2937;
    outline: none;
    min-width: 0;
}

.nav-search-form input::placeholder {
    color: #b0b9b5;
    font-size: 1rem;
}

    .nav-search-clear {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: #9ca3af;
        display: none;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        transition: background .15s, color .15s;
        flex-shrink: 0;
    }

    .nav-search-clear:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .nav-search-form input:not(:placeholder-shown) ~ .nav-search-clear {
        display: flex;
    }

    /* ── Separator ── */
    .nav-actions-sep {
        width: 1px;
        height: 26px;
        background: #e3ede9;
        flex-shrink: 0;
    }

    /* ── View Toggle ── */
    .nav-view-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
        user-select: none;
    }

    .nav-toggle-label {
        font-size: 0.875rem; /*custom toggle*/
        font-weight: 600;
        color: #9ca3af;
        letter-spacing: .03em;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color .2s;
        white-space: nowrap;
    }

    .nav-toggle-label.is-active {
        color: #009966;
    }

    .nav-toggle-label svg {
        width: 13px;
        height: 13px;
        flex-shrink: 0;
    }

    .nav-toggle-track {
        position: relative;
        width: 62px;  /*custom toggle*/
        height: 32px; /*custom toggle*/
        background: #e5e7eb;
        border-radius: 99px;
        cursor: pointer;
        transition: background .3s ease;
        flex-shrink: 0;
        box-shadow: inset 0 1px 3px rgba(0,0,0,.10);
    }

    .nav-toggle-track.map-active {
        background: #009966;
    }

    .nav-toggle-thumb {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 26px; /*custom toggle*/
        height: 26px; /*custom toggle*/
        background: #fff;
        border-radius: 50%;
        transition: transform .3s cubic-bezier(.34, 1.56, .64, 1);
        box-shadow: 0 1px 5px rgba(0,0,0,.18);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-toggle-track.map-active .nav-toggle-thumb {
        transform: translateX(30px); /* sesuaikan: track width - thumb width - (2 * top offset) = 62 - 26 - 6 */
    }

    /* ── Map modal button ── */
    .nav-map-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1.5px solid #e3ede9;
        background: #f6f8f7;
        cursor: pointer;
        flex-shrink: 0;
        transition: border-color .2s, background .2s;
    }

    .nav-map-btn:hover {
        border-color: #009966;
        background: #edfdf7;
    }

    .nav-map-btn svg {
        width: 17px;
        height: 17px;
        color: #4c5250;
        transition: color .2s;
    }

    .nav-map-btn:hover svg {
        color: #009966;
    }

    /* Mobile: collapse search on small screens */
    @media (max-width: 639px) {
    .nav-search-form {
        min-width: 0;
        max-width: 200px;
    }
}

@media (max-width: 400px) {
    .nav-search-form {
        min-width: 0;
        max-width: 150px;
    }
}

    /* ============================================================
       VIEW CONTAINERS
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

    .map-legend-item:hover { background: #f3f4f6; }
    .map-legend-item.active-filter { border-color: currentColor; background: rgba(0,0,0,.04); }

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
        height: 560px;
        border-radius: 0 0 12px 12px;
        border: 1px solid #e5e7eb;
        background: #f0f4f0;
    }

    /* Custom marker */
    .custom-marker-pin { display: flex; align-items: center; justify-content: center; }

    /* Density badges in popup */
    .custom-leaflet-popup .density-badge.jarang  { background:#e6f4f1; color:#00724c; }
    .custom-leaflet-popup .density-badge.sedang  { background:#fef9e7; color:#7d6200; }
    .custom-leaflet-popup .density-badge.lebat   { background:#eae6f4; color:#4a3d8f; }

    /* ── Rich popup (mpp-*) ── */
    .mpp-popup { width: 300px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; overflow: hidden; }
    .mpp-img-wrap, .mpp-img-placeholder { position: relative; width: 100%; height: 150px; overflow: hidden; background: #f0f4f0; }
    .mpp-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .35s ease; }
    .mpp-popup:hover .mpp-img { transform: scale(1.04); }
    .mpp-img-placeholder { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; color: #9ca3af; font-size: 0.72rem; }
    .mpp-img-placeholder svg { width: 38px; height: 38px; opacity: .45; }
    .mpp-type-ribbon { position: absolute; top: 10px; right: 0; padding: 3px 10px 3px 8px; color: #fff; font-size: 0.68rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; border-radius: 3px 0 0 3px; box-shadow: -2px 2px 6px rgba(0,0,0,.18); }
    .mpp-body { padding: 12px 14px 14px; }
    .mpp-title { font-size: 0.9rem; font-weight: 700; color: #1f2937; line-height: 1.3; margin-bottom: 7px; }
    .mpp-damage-badge, .mpp-ok-badge { display: inline-block; font-size: 0.68rem; font-weight: 600; padding: 2px 8px; border-radius: 99px; margin-bottom: 10px; }
    .mpp-damage-badge { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .mpp-ok-badge { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .mpp-grid { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
    .mpp-row { display: flex; align-items: flex-start; gap: 6px; font-size: 0.76rem; }
    .mpp-row-full { flex-direction: column; gap: 3px; }
    .mpp-lbl { color: #6b7280; white-space: nowrap; min-width: 90px; flex-shrink: 0; }
    .mpp-val { color: #1f2937; font-weight: 500; flex: 1; }
    .mpp-coords { font-size: 0.7rem; font-family: monospace; color: #4b5563; }
    .mpp-empty { color: #9ca3af; font-style: italic; font-size: 0.72rem; }
    .mpp-species { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 2px; }
    .mpp-species-pill { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-size: 0.67rem; font-weight: 600; padding: 1px 7px; border-radius: 99px; }
    .mpp-species-pill.mpp-more { background: #f3f4f6; color: #6b7280; border-color: #e5e7eb; }
    .mpp-damage-list { list-style: none; padding: 0; margin: 2px 0 0; }
    .mpp-damage-list li { font-size: 0.72rem; color: #b91c1c; padding: 1px 0 1px 10px; position: relative; }
    .mpp-damage-list li::before { content: '•'; position: absolute; left: 0; color: #fca5a5; }
    .mpp-more-item { color: #9ca3af !important; }
    .mpp-link { display: flex; align-items: center; justify-content: center; gap: 5px; padding: 7px 0; background: #009966; color: #fff !important; border-radius: 8px; font-size: 0.78rem; font-weight: 600; text-decoration: none !important; transition: background .2s; }
    .mpp-link:hover { background: #007a52; }
    #leaflet-main-map .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.18); padding: 0; overflow: hidden; min-width: 300px; }
    #leaflet-main-map .leaflet-popup-content { margin: 0; width: auto !important; }

    /* body class toggling */
    body.map-view-active #grid-filter-tabs { display: none; }
    body.map-view-active .cards-grid { display: none; }
    body.map-view-active #map-view-container { display: flex; }
    body:not(.map-view-active) #map-view-container { display: none; }
</style>
@endpush

{{-- ──────────────────────────────────────────────────────────
     INJECT SEARCH + TOGGLE INTO NAVBAR
     ────────────────────────────────────────────────────────── --}}
@push('navbar-left')
 {{-- Divider --}}
            <div class="h-24 w-px bg-gray-300 mx-7.5"></div>
<div class="nav-search-form ml-3">
    <span class="nav-search-icon">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
        </svg>
    </span>

    <input
        type="text"
        id="searchInput"
        placeholder="Cari lokasi mangrove…"
        autocomplete="off"
    >

    <button class="nav-search-clear" id="searchClearBtn" onclick="clearSearch()">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
    </button>
</div>
@endpush

@push('navbar-right')
    <div class="nav-actions-sep"></div>
    {{-- View Toggle: Grid ↔ Peta --}}
    <div class="nav-view-toggle">
        <span class="nav-toggle-label is-active" id="label-grid">
            <svg viewBox="0 0 16 16" fill="currentColor">
                <rect x="1" y="1" width="6" height="6" rx="1.5"/>
                <rect x="9" y="1" width="6" height="6" rx="1.5"/>
                <rect x="1" y="9" width="6" height="6" rx="1.5"/>
                <rect x="9" y="9" width="6" height="6" rx="1.5"/>
            </svg>
            Grid
        </span>

        <div class="nav-toggle-track" id="viewToggleTrack" onclick="toggleView()" title="Beralih tampilan">
            <div class="nav-toggle-thumb">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="#009966">
                    <path d="M8 0C5.2 0 2.9 2.3 2.9 5.1c0 3.8 5.1 10.9 5.1 10.9s5.1-7.1 5.1-10.9C13.1 2.3 10.8 0 8 0zm0 7c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                </svg>
            </div>
        </div>

        <span class="nav-toggle-label" id="label-peta">
            <svg viewBox="0 0 16 16" fill="currentColor">
                <path d="M8 0C5.2 0 2.9 2.3 2.9 5.1c0 3.8 5.1 10.9 5.1 10.9s5.1-7.1 5.1-10.9C13.1 2.3 10.8 0 8 0zm0 7c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
            </svg>
            Peta
        </span>
    </div>
</div>
@endpush

@section('content')

<div class="hasil-pemantauan-page">
    <div class="mangrove-container">

        {{-- ── Sidebar (unchanged, still dynamic) ─────────────── --}}
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Pemantauan Mangrove</h2>
            </div>

            <div class="stats-section">
    {{-- <h3>Total Pemanfaatan Kawasan Mangrove</h3> --}}
    <div class="stat-box">
        <div class="stat-box-content">
            <div class="stat-label">Titik Pemantauan Kawasan Mangrove</div>
            <div class="stat-number primary">{{ $totalSites }}</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-box-content">
            <div class="stat-label">Total Luas Area (ha)</div>
            <div class="stat-number secondary">{{ $totalArea }}</div>
        </div>
    </div>
</div>

            <div class="info-section">
                <div class="info-header">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <div style="width: 18px; height: 18px; flex-shrink: 0;">
                        <x-icons.rekomendasi_pengelolaan-hasi-pemantauan />
                        </div>
                        <h3>Rekomendasi Pengelolaan</h3>
                    </div>
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
                        <span class="tag tag-dilindungi">Dilindungi: {{ $typeStats['dilindungi'] }}</span>
                    @endif
                    @if($typeStats['pengkayaan'] > 0)
                        <span class="tag tag-pengkayaan">Pengkayaan: {{ $typeStats['pengkayaan'] }}</span>
                    @endif
                    @if($typeStats['pengkayaan_rehabilitasi'] > 0)
                    <span class="tag tag-pengkayaan_rehabilitasi" style="display:flex;gap:3px;align-items:center;min-width:0;">
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;">Pengkayaan/Rehabil.</span>
                    <span style="flex-shrink:0;font-weight:700;">{{ $typeStats['pengkayaan_rehabilitasi'] }}</span>
                    </span>
                    @endif
                    @if($typeStats['rehabilitasi'] > 0)
                        <span class="tag tag-rehabilitasi">Rehabilitasi: {{ $typeStats['rehabilitasi'] }}</span>
                    @endif
                </div>
            </div>

            <div class="geography-section">
                <div style="display:flex; align-items:center; gap:6px; margin-bottom: 1rem;">
                    <div style="width: 18px; height: 18px; flex-shrink: 0;">
                        <x-icons.sebaran_geografis-hasil-pemantauan />
                    </div>
                    <h3 style="margin-bottom: 0;">Sebaran Geografis</h3>
                </div>
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
<h2>© 2025 Dinas Lingkungan Hidup DKI Jakarta</h2>

        </aside>

        {{-- ── Main Content ────────────────────────────────────── --}}
        <main class="main-content">
            {{-- Filter Tabs (grid view only) --}}
           <div class="filter-tabs" id="grid-filter-tabs">
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
                    <div class="empty-state" style="grid-column:1/-1;text-align:center;padding:3rem;color:#9ca3af;">
                        <p>Belum ada data lokasi monitoring.</p>
                    </div>
                @endforelse
            </div>

            {{-- Map View --}}
<div id="map-view-container">

    {{-- Region Tabs (map version) --}}
    <div class="filter-tabs" id="map-region-tabs">
    <button class="tab active" onclick="setMapRegion('all', this)">
        Semua <span class="tab-count">{{ $totalSites }}</span>
    </button>
    <button class="tab" onclick="setMapRegion('penjaringan', this)">
        Penjaringan, Jakarta Utara <span class="tab-count">{{ $regionStats['penjaringan'] }}</span>
    </button>
    <button class="tab" onclick="setMapRegion('cilincing', this)">
        Cilincing, Jakarta Utara <span class="tab-count">{{ $regionStats['cilincing'] }}</span>
    </button>
    <button class="tab" onclick="setMapRegion('kep-seribu-utara', this)">
        Kep. Seribu Utara <span class="tab-count">{{ $regionStats['kep_seribu_utara'] }}</span>
    </button>
    <button class="tab" onclick="setMapRegion('kep-seribu-selatan', this)">
        Kep. Seribu Selatan <span class="tab-count">{{ $regionStats['kep_seribu_selatan'] }}</span>
    </button>
</div>

    <div class="map-toolbar">
        {{-- ... isi map-toolbar tidak berubah ... --}}
    </div>

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

    // ================================================================
    // TYPE COLORS
    // ================================================================
    const TYPE_COLORS = {
        'pengkayaan':   '#F0B100',
        'rehabilitasi': '#FF6900',
        'dilindungi':   '#016630',
        'pengkayaan_rehabilitasi':    '#9F0712',
    };
    const TYPE_LABELS = {
        'pengkayaan':   'Pengkayaan',
        'rehabilitasi': 'Rehabilitasi',
        'dilindungi':   'Dilindungi',
        'pengkayaan_rehabilitasi':    'Pengkayaan/Rehabilitasi',
    };

    // ================================================================
    // SEARCH (navbar input → filter cards)
    // ================================================================
    function clearSearch() {
        const inp = document.getElementById('searchInput');
        if (inp) { inp.value = ''; filterCards(''); inp.focus(); }
    }

    // Wire search input (runs after DOM ready)
    document.addEventListener('DOMContentLoaded', function () {
        const inp = document.getElementById('searchInput');
        if (inp) {
            inp.addEventListener('input', () => filterCards(inp.value.toLowerCase().trim()));
            inp.addEventListener('keydown', e => { if (e.key === 'Escape') clearSearch(); });
        }
    });

    function filterCards(term) {
    document.querySelectorAll('.location-card').forEach(card => {
        const title = (card.querySelector('.card-title')?.textContent || '').toLowerCase();
        const desc  = (card.querySelector('.description')?.textContent || '').toLowerCase();
        card.style.display = (!term || title.includes(term) || desc.includes(term)) ? '' : 'none';
    });
    filterMarkers(term);
}

// Tambah setelah deklarasi activeFilters:
let activeRegionFilter = 'all';

// Fungsi baru:
function setMapRegion(region, el) {
    activeRegionFilter = region;

    // Update active tab — query ke dalam #map-region-tabs saja
    document.querySelectorAll('#map-region-tabs .tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    const term = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    filterMarkers(term);

    const visibleLatLngs = [];
    window.locationsData.forEach(loc => {
        const matchRegion = activeRegionFilter === 'all' || (loc.group || '') === activeRegionFilter;
        const matchType   = activeFilters.has(getTypeKey(loc));
        const name        = (loc.name || '').toLowerCase();
        const matchSearch = !term || name.includes(term);
        if (matchRegion && matchType && matchSearch) {
            visibleLatLngs.push([parseFloat(loc.latitude), parseFloat(loc.longitude)]);
        }
    });
    if (visibleLatLngs.length && mapInstance) {
        mapInstance.fitBounds(L.latLngBounds(visibleLatLngs), { padding: [40, 40], maxZoom: 14 });
    }
}

function filterMarkers(term) {
    if (!mapInstance) return;

    let visibleCount = 0;

    window.locationsData.forEach(loc => {
        const typeKey = getTypeKey(loc);
        const markers = allMarkers[typeKey] || [];
        const lat = parseFloat(loc.latitude);
        const lng = parseFloat(loc.longitude);

        markers.forEach(marker => {
            const mll = marker.getLatLng();
            if (Math.abs(mll.lat - lat) > 0.00001 || Math.abs(mll.lng - lng) > 0.00001) return;

            const name        = (loc.name || '').toLowerCase();
            const desc        = (loc.description || '').toLowerCase();
            const matchSearch = !term || name.includes(term) || desc.includes(term);
            const matchType   = activeFilters.has(typeKey);
            const matchRegion = activeRegionFilter === 'all' || (loc.group || '') === activeRegionFilter;

            if (matchSearch && matchType && matchRegion) {
                marker.addTo(mapInstance);
                visibleCount++;
            } else {
                mapInstance.removeLayer(marker);
            }
        });
    });

    const el = document.getElementById('map-visible-count');
    if (el) el.textContent = visibleCount;
}

    // ================================================================
    // VIEW TOGGLE
    // ================================================================
    let isMapView = false;
    let mapInstance = null;
    let allMarkers  = {};
    let activeFilters = new Set(['dilindungi', 'pengkayaan', 'rehabilitasi', 'pengkayaan_rehabilitasi']);

    function toggleView() {
        isMapView = !isMapView;

        const track     = document.getElementById('viewToggleTrack');
        const labelGrid = document.getElementById('label-grid');
        const labelPeta = document.getElementById('label-peta');

        if (isMapView) {
            track.classList.add('map-active');
            document.body.classList.add('map-view-active');
            labelGrid?.classList.remove('is-active');
            labelPeta?.classList.add('is-active');
            requestAnimationFrame(initMainMap);
        } else {
            track.classList.remove('map-active');
            document.body.classList.remove('map-view-active');
            labelGrid?.classList.add('is-active');
            labelPeta?.classList.remove('is-active');
        }
    }

    // ================================================================
    // LEAFLET MAP
    // ================================================================
    function initMainMap() {
        if (mapInstance) { mapInstance.invalidateSize(); return; }

        mapInstance = L.map('leaflet-main-map', { center: [-6.12, 106.82], zoom: 11 });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(mapInstance);

        plotMarkers();
        fitMapBounds();
    }

    function createMarkerIcon(color) {
        const s = 20;
        return L.divIcon({
            html: `<svg xmlns="http://www.w3.org/2000/svg" width="${s}" height="${Math.round(s*1.4)}" viewBox="0 0 20 28">
                       <path d="M10 0C4.5 0 0 4.5 0 10c0 7.5 10 18 10 18S20 17.5 20 10C20 4.5 15.5 0 10 0z"
                             fill="${color}" stroke="rgba(255,255,255,0.55)" stroke-width="1.5"/>
                       <circle cx="10" cy="10" r="4" fill="rgba(255,255,255,0.85)"/>
                   </svg>`,
            className: 'custom-marker-pin',
            iconSize: [s, Math.round(s*1.4)],
            iconAnchor: [s/2, Math.round(s*1.4)],
            popupAnchor: [0, -Math.round(s*1.4)],
        });
    }

    function getTypeKey(loc) {
    const raw = (loc.type || '').toLowerCase().trim();
    // 1. Exact match dulu (paling aman, handle 'pengkayaan_rehabilitasi' langsung)
    if (TYPE_COLORS[raw]) return raw;
    // 2. Fallback partial match, key terpanjang dulu
    const sortedKeys = Object.keys(TYPE_COLORS).sort((a, b) => b.length - a.length);
    return sortedKeys.find(k => raw.includes(k)) || 'pengkayaan';
}

    function buildPopupContent(loc) {
        const typeKey    = getTypeKey(loc);
        const color      = TYPE_COLORS[typeKey];
        const typeLabel  = TYPE_LABELS[typeKey] || loc.type;
        const area       = loc.area || 'Belum diidentifikasi';
        const densityRaw = (loc.density || '').toLowerCase();
        const densityLbl = densityRaw.charAt(0).toUpperCase() + densityRaw.slice(1);
        const year       = loc.year || '-';
        const coords     = loc.coords || `${loc.latitude}, ${loc.longitude}`;
        const species    = loc.species || null;
        const dmgCount   = parseInt(loc.damage_count) || 0;
        const damages    = Array.isArray(loc.damages) ? loc.damages : [];
        const detailUrl  = `/monitoring/lokasi/${loc.slug}`;
        const images     = Array.isArray(loc.images) ? loc.images : [];
        const imgSrc     = images[0] || null;

        const imgHtml = imgSrc
            ? `<div class="mpp-img-wrap">
                   <img src="${imgSrc}" alt="${loc.name}" class="mpp-img" onerror="this.parentElement.style.display='none'">
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

        const damageBadge = dmgCount > 0
            ? `<span class="mpp-damage-badge">⚠ ${dmgCount} kerusakan aktif</span>`
            : `<span class="mpp-ok-badge">✓ Kondisi baik</span>`;

        const speciesHtml = species && species !== 'Belum diidentifikasi'
            ? `<div class="mpp-species">${
                  species.split(',').slice(0,3).map(s => `<span class="mpp-species-pill">${s.trim()}</span>`).join('')
              }${species.split(',').length > 3 ? `<span class="mpp-species-pill mpp-more">+${species.split(',').length-3}</span>` : ''}</div>`
            : `<span class="mpp-empty">Belum diidentifikasi</span>`;

        return `<div class="mpp-popup">
            ${imgHtml}
            <div class="mpp-body">
                <div class="mpp-title">${loc.name || 'Lokasi Mangrove'}</div>
                ${damageBadge}
                <div class="mpp-grid">
                    <div class="mpp-row"><span class="mpp-lbl">📅 Tahun</span><span class="mpp-val">${year}</span></div>
                    <div class="mpp-row"><span class="mpp-lbl">📐 Luas</span><span class="mpp-val">${area}</span></div>
                    <div class="mpp-row"><span class="mpp-lbl">🌿 Kerapatan</span><span class="mpp-val"><span class="density-badge ${densityRaw}">${densityLbl}</span></span></div>
                    <div class="mpp-row"><span class="mpp-lbl">📍 Koordinat</span><span class="mpp-val mpp-coords">${coords}</span></div>
                    <div class="mpp-row mpp-row-full"><span class="mpp-lbl">🌳 Spesies</span><span class="mpp-val">${speciesHtml}</span></div>
                    ${dmgCount > 0 && damages.length > 0 ? `<div class="mpp-row mpp-row-full"><span class="mpp-lbl">⚠ Kerusakan</span><span class="mpp-val"><ul class="mpp-damage-list">${damages.slice(0,2).map(d=>`<li>${d}</li>`).join('')}${damages.length>2?`<li class="mpp-more-item">+${damages.length-2} lainnya...</li>`:''}</ul></span></div>` : ''}
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

    function plotMarkers() {
        Object.values(allMarkers).forEach(g => g.forEach(m => mapInstance.removeLayer(m)));
        allMarkers = {};

        window.locationsData.forEach(loc => {
            const lat = parseFloat(loc.latitude);
            const lng = parseFloat(loc.longitude);
            if (!lat || !lng || isNaN(lat) || isNaN(lng)) return;

            const typeKey = getTypeKey(loc);
            const marker  = L.marker([lat, lng], { icon: createMarkerIcon(TYPE_COLORS[typeKey]) })
                .bindPopup(buildPopupContent(loc), { maxWidth: 340, className: 'custom-leaflet-popup' });

            if (!allMarkers[typeKey]) allMarkers[typeKey] = [];
            allMarkers[typeKey].push(marker);
            if (activeFilters.has(typeKey)) marker.addTo(mapInstance);
        });

        updateMapStatsDisplay();
    }

    function fitMapBounds() {
        const pts = window.locationsData
            .map(l => [parseFloat(l.latitude), parseFloat(l.longitude)])
            .filter(([a,b]) => !isNaN(a) && !isNaN(b) && a && b);
        if (pts.length) mapInstance.fitBounds(L.latLngBounds(pts), { padding: [40,40], maxZoom: 13 });
    }

  function filterMapByType(type, el) {
    if (activeFilters.has(type)) {
        if (activeFilters.size <= 1) return;
        activeFilters.delete(type);
        el.classList.remove('active-filter');
        (allMarkers[type] || []).forEach(m => mapInstance.removeLayer(m));
    } else {
        activeFilters.add(type);
        el.classList.add('active-filter');
        // Tambahkan hanya marker yang lolos search term juga
        (allMarkers[type] || []).forEach(m => m.addTo(mapInstance));
    }
    // Re-apply search filter supaya konsisten
    const term = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    filterMarkers(term);
}

    function updateMapStatsDisplay() {
        let count = 0;
        activeFilters.forEach(t => { count += (allMarkers[t] || []).length; });
        const el = document.getElementById('map-visible-count');
        if (el) el.textContent = count;
    }
</script>
@endpush
