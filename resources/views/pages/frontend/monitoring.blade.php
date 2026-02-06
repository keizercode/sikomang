@extends('layouts.frontend.app')

@section('title', 'Profil Sebaran Mangrove DKI Jakarta 2025 - SIKOMANG')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@vite('resources/css/monitoring.css')

<style>

/* Custom Popup Styles */
.custom-popup {
    min-width: 280px;
    max-width: 320px;
}

.custom-popup .popup-image {
    width: 100%;
    height: 120px;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
    margin: -12px -16px 10px -16px;
    background: #f3f4f6;
}

.custom-popup .popup-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.custom-popup .popup-title {
    font-size: 16px;
    font-weight: 700;
    color: #242621;
    margin-bottom: 8px;
    line-height: 1.3;
}

.custom-popup .popup-badges {
    display: flex;
    gap: 5px;
    margin: 8px 0;
    flex-wrap: wrap;
}

.custom-popup .popup-badges span {
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    text-transform: capitalize;
}

.custom-popup .popup-info {
    font-size: 13px;
    color: #4b5563;
    margin: 5px 0;
    line-height: 1.5;
}

.custom-popup .popup-link {
    display: inline-block;
    margin-top: 10px;
    padding: 6px 12px;
    background: #009966;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    transition: background 0.2s;
}

.custom-popup .popup-link:hover {
    background: #2d5c54;
}

/* Custom Tooltip Styles */
.custom-tooltip {
    background: rgba(0, 0, 0, 0.85);
    border: none;
    border-radius: 6px;
    color: white;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.custom-tooltip::before {
    border-top-color: rgba(0, 0, 0, 0.85);
}
</style>
@endpush

@section('content')
<div class="monitoring-wrapper">
    {{-- Info Panel Section --}}
    <aside class="info-panel">
        {{-- Page Header --}}
        <header class="page-header">
            <h1 class="page-title">
                Profil Sebaran Mangrove DKI Jakarta 2025
            </h1>

            <p class="page-description">
                SIKOMANG menyediakan data monitoring komprehensif untuk {{ $total_sites }} titik pemantauan di 4 wilayah DKI Jakarta.
                Platform ini memungkinkan pemantauan real-time kondisi ekosistem mangrove, pelaporan kerusakan, dan koordinasi
                upaya konservasi untuk mendukung kelestarian lingkungan pesisir Jakarta.
            </p>

            <a href="{{ route('monitoring.hasil-pemantauan') }}" class="cta-button">
                <span>Hasil Pemantauan</span>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7V17"/>
                </svg>
            </a>
        </header>

        {{-- Data Table Section --}}
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fungsi Kawasan</th>
                        <th>Jarang</th>
                        <th>Sedang</th>
                        <th>Lebat</th>
                        <th>Luas Total</th>
                        <th>Status Konservasi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Luar Kawasan Section --}}
                    <tr class="section-header">
                        <td colspan="6">Luar Kawasan</td>
                    </tr>
                    @foreach($monitoring_data['luar_kawasan'] as $row)
                    <tr>
                        <td>{{ $row['fungsi'] }}</td>
                        <td>{{ $row['jarang'] }}</td>
                        <td>{{ $row['sedang'] }}</td>
                        <td>{{ $row['lebat'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['status'] }}</td>
                    </tr>
                    @endforeach

                    {{-- Dalam Kawasan Section --}}
                    <tr class="section-header">
                        <td colspan="6">Dalam Kawasan</td>
                    </tr>
                    @foreach($monitoring_data['dalam_kawasan'] as $row)
                    <tr>
                        <td>{{ $row['fungsi'] }}</td>
                        <td>{{ $row['jarang'] }}</td>
                        <td>{{ $row['sedang'] }}</td>
                        <td>{{ $row['lebat'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['status'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </aside>

    {{-- Map Container Section --}}
    <main class="map-wrapper">
        {{-- Panel Toggle Button (Desktop Only) --}}
        <button class="panel-toggle" onclick="togglePanel()" aria-label="Toggle Panel">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Leaflet Map --}}
        <div id="mangroveMap"></div>

        {{-- Map Legend --}}
        <div class="map-legend">
            @foreach(['jarang', 'sedang', 'lebat'] as $density)
            <div class="legend-card legend-{{ $density }}">
                <div class="legend-header">
                    <a href="{{ route('monitoring.export', $density) }}"
                       class="legend-download-btn"
                       title="Download data Mangrove {{ ucfirst($density) }}">
                        <x-icons.mapdata-monitoring />
                        <span>Map Data</span>
                    </a>
                    <button class="legend-toggle-btn" onclick="toggleLegend('{{ $density }}')" aria-label="Toggle Legend">
                        <span>Expand</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                <div class="legend-body" id="legend-body-{{ $density }}">
                    <div class="legend-dataset">
                        <span class="legend-dataset-label">Dataset:</span>
                        <span class="legend-dataset-value">Mangrove Jakarta - {{ ucfirst($density) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ===========================
    // CONFIGURATION
    // ===========================
    const CONFIG = {
        map: {
            center: [-6.10, 106.80],
            zoom: 11,
            maxZoom: 19
        },
        colors: {
            jarang: '#8dd3c7',
            sedang: '#FFFFB3',
            lebat: '#BEBADA'
        },
        circleRadius: 500, // 500 meters
        markerSize: 24
    };

    // ===========================
    // MANGROVE DATA FROM CONTROLLER
    // ===========================
    const locationsData = @json($locations);

    console.log('=== MONITORING MAP DEBUG ===');
    console.log('Total locations from controller:', locationsData.length);
    console.log('Locations data:', locationsData);

    // Group locations by density
    const mangroveData = {
        'jarang': [],
        'sedang': [],
        'lebat': []
    };

    locationsData.forEach(location => {
        // Parse coordinates from "lat, lng" string to [lat, lng] array
        const coordsArray = location.coords.split(',').map(c => parseFloat(c.trim()));

        const mapLocation = {
            name: location.name,
            coords: coordsArray,
            area: location.area,
            slug: location.slug,
            density: location.density,
            type: location.type,
            year: location.year,
            images: location.images || [],
            species_detail: location.species_detail || {},
            damage_count: location.damage_count || 0
        };

        // Add to appropriate density group
        const densityKey = location.density.toLowerCase();
        if (mangroveData[densityKey]) {
            mangroveData[densityKey].push(mapLocation);
        }
    });

    console.log('Grouped by density:');
    console.log('- Jarang:', mangroveData.jarang.length, 'locations');
    console.log('- Sedang:', mangroveData.sedang.length, 'locations');
    console.log('- Lebat:', mangroveData.lebat.length, 'locations');

    // ===========================
    // MAP INITIALIZATION
    // ===========================
    const map = L.map('mangroveMap', {
        center: CONFIG.map.center,
        zoom: CONFIG.map.zoom,
        zoomControl: true,
        scrollWheelZoom: true
    });

    // ===========================
    // TILE LAYERS
    // ===========================
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: CONFIG.map.maxZoom
    }).addTo(map);

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri',
        maxZoom: CONFIG.map.maxZoom
    });

    // Layer control
    const baseMaps = {
        "Peta Standar": osmLayer,
        "Satelit": satelliteLayer
    };

    // ===========================
    // LAYER GROUPS
    // ===========================
    const layers = {
        jarang: L.layerGroup().addTo(map),
        sedang: L.layerGroup().addTo(map),
        lebat: L.layerGroup().addTo(map)
    };
    L.control.layers(baseMaps, null, { position: 'topleft' }).addTo(map);

    // ===========================
    // HELPER FUNCTIONS
    // ===========================
    function createCustomIcon(color) {
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: ${color}; width: ${CONFIG.markerSize}px; height: ${CONFIG.markerSize}px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
            iconSize: [CONFIG.markerSize, CONFIG.markerSize],
            iconAnchor: [CONFIG.markerSize / 2, CONFIG.markerSize / 2],
            popupAnchor: [0, -CONFIG.markerSize / 2]
        });
    }

    function createPopupContent(location, category) {
        // Get first image or use placeholder
        const imageUrl = location.images && location.images.length > 0
            ? location.images[0]
            : 'https://via.placeholder.com/400x200?text=No+Image';

        // Get first 2 species from vegetasi
        let speciesText = '-';
        if (location.species_detail && location.species_detail.vegetasi && location.species_detail.vegetasi.length > 0) {
            const species = location.species_detail.vegetasi.slice(0, 2);
            speciesText = species.join(', ');
            if (location.species_detail.vegetasi.length > 2) {
                speciesText += ` +${location.species_detail.vegetasi.length - 2} lainnya`;
            }
        }

        // Format damage count
        const damageText = location.damage_count > 0
            ? `⚠️ ${location.damage_count} Kerusakan teridentifikasi`
            : '✅ Tidak ada kerusakan';

        const damageColor = location.damage_count > 0 ? '#dc2626' : '#16a34a';

        return `
            <div class="custom-popup">
                <div class="popup-image">
                    <img src="${imageUrl}" alt="${location.name}" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'" />
                </div>
                <div class="popup-title">${location.name}</div>
                <div class="popup-badges">
                    <span style="background: #009966; color: white;">${location.type}</span>
                    <span style="background: #6b7280; color: white;">${location.year}</span>
                </div>
                <div class="popup-info">📍 ${location.coords[0].toFixed(4)}, ${location.coords[1].toFixed(4)}</div>
                <div class="popup-info">📏 Luas: ${location.area}</div>
                <div class="popup-info">🌳 Kerapatan: ${category.charAt(0).toUpperCase() + category.slice(1)}</div>
                <div class="popup-info" style="font-size: 12px; color: #4b5563; margin-top: 4px;">🌿 ${speciesText}</div>
                <div class="popup-info" style="font-weight: 600; color: ${damageColor}; margin-top: 4px;">${damageText}</div>
                <a href="/monitoring/lokasi/${location.slug}" class="popup-link">Lihat Detail →</a>
            </div>
        `;
    }

    // ===========================
    // ADD MARKERS TO MAP
    // ===========================
    Object.keys(mangroveData).forEach(category => {
        mangroveData[category].forEach(location => {
            // Create marker
            const marker = L.marker(location.coords, {
                icon: createCustomIcon(CONFIG.colors[category])
            }).addTo(layers[category]);

            // Bind popup with custom options
            marker.bindPopup(createPopupContent(location, category), {
                maxWidth: 320,
                minWidth: 280,
                className: 'leaflet-popup-custom'
            });

            // Add coverage circle
            L.circle(location.coords, {
                color: CONFIG.colors[category],
                fillColor: CONFIG.colors[category],
                fillOpacity: 0.3,
                radius: CONFIG.circleRadius,
                weight: 2
            }).addTo(layers[category]);

            // Add tooltip
            marker.bindTooltip(location.name, {
                permanent: false,
                direction: 'top',
                className: 'custom-tooltip'
            });
        });
    });

    // ===========================
    // UI INTERACTION FUNCTIONS
    // ===========================
    function toggleLegend(type) {
        const body = document.getElementById(`legend-body-${type}`);
        const btn = body.previousElementSibling.querySelector('.legend-toggle-btn');
        const icon = btn.querySelector('svg path');
        const text = btn.querySelector('span');

        body.classList.toggle('show');
        btn.classList.toggle('expanded');

        if (body.classList.contains('show')) {
            text.textContent = 'Collapse';
        } else {
            text.textContent = 'Expand';
        }
    }

    function togglePanel() {
        const panel = document.querySelector('.info-panel');
        const container = document.querySelector('.monitoring-wrapper');
        const toggleBtn = document.querySelector('.panel-toggle');

        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            container.style.gridTemplateColumns = '500px 1fr';
            toggleBtn.querySelector('svg path').setAttribute('d', 'M13 5l7 7-7 7M5 5l7 7-7 7');
        } else {
            panel.style.display = 'none';
            container.style.gridTemplateColumns = '0 1fr';
            toggleBtn.querySelector('svg path').setAttribute('d', 'M11 19l-7-7 7-7M18 19l-7-7 7-7');
        }

        setTimeout(() => map.invalidateSize(), 300);
    }

    // ===========================
    // EVENT LISTENERS
    // ===========================
    window.addEventListener('resize', () => {
        map.invalidateSize();
    });

    // Show toggle button on desktop when panel is hidden
    if (window.innerWidth > 1024) {
        const panel = document.querySelector('.info-panel');
        const toggleBtn = document.querySelector('.panel-toggle');

        if (panel.style.display === 'none') {
            toggleBtn.style.display = 'block';
        }
    }

    // Fix map rendering on page load
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
</script>
@endpush
