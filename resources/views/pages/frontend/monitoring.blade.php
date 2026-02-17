@extends('layouts.frontend.app')

@section('title', 'Profil Sebaran Mangrove DKI Jakarta 2025 - SIKOMANG')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@vite('resources/css/monitoring.css')

<style>
/* Polygon fill styles by density */
.leaflet-interactive.density-jarang {
    fill: #8dd3c7 !important;
    fill-opacity: 0.4 !important;
    stroke: #5aa89a !important;
    stroke-width: 2px !important;
}

.leaflet-interactive.density-sedang {
    fill: #FFFFB3 !important;
    fill-opacity: 0.4 !important;
    stroke: #d4d466 !important;
    stroke-width: 2px !important;
}

.leaflet-interactive.density-lebat {
    fill: #BEBADA !important;
    fill-opacity: 0.4 !important;
    stroke: #9186ad !important;
    stroke-width: 2px !important;
}

/* Highlight on hover */
.leaflet-interactive:hover {
    fill-opacity: 0.7 !important;
    stroke-width: 3px !important;
}

/* Custom Popup Styles */
.custom-popup {
    min-width: 320px;
    max-width: 380px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.custom-popup .popup-title {
    font-size: 17px;
    font-weight: 700;
    color: #242621;
    margin-bottom: 10px;
    line-height: 1.3;
    padding-bottom: 8px;
    border-bottom: 2px solid #009966;
}

.custom-popup .popup-badges {
    display: flex;
    gap: 6px;
    margin: 10px 0;
    flex-wrap: wrap;
}

.custom-popup .popup-badges span {
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.custom-popup .popup-info {
    font-size: 12px;
    color: #4b5563;
    margin: 5px 0;
    line-height: 1.5;
}

.custom-popup .popup-info strong {
    color: #1f2937;
    font-weight: 600;
}

/* Leaflet popup adjustments */
.leaflet-popup-content-wrapper {
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.leaflet-popup-content {
    margin: 14px 18px;
    max-height: 600px;
    overflow-y: auto;
}

/* Scrollbar for popup content */
.leaflet-popup-content::-webkit-scrollbar {
    width: 6px;
}

.leaflet-popup-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.leaflet-popup-content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.leaflet-popup-content::-webkit-scrollbar-thumb:hover {
    background: #555;
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

/* Loading overlay */
#loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

#loading-overlay .spinner {
    border: 4px solid #f3f4f6;
    border-top: 4px solid #009966;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<!-- Loading Overlay -->
<div id="loading-overlay" style="display: none;">
    <div class="spinner"></div>
</div>

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
        // DIRECT PLOVIS API URLs - NOT internal API
        plovisUrls: {
            jarang: 'https://asset.plovis.id/plovis/public/67f25022-a757-4f90-a114-16e3f3ad671c.geojson',
            sedang: 'https://asset.plovis.id/plovis/public/1c7b760f-7458-4353-bfd9-1ba6084cdce6.geojson',
            lebat: 'https://asset.plovis.id/plovis/public/cb7b89d7-2ac7-4fa4-a16c-02734432838e.geojson'
        }
    };

    console.log('=== GEOJSON MONITORING MAP - PLOVIS DIRECT ===');
    console.log('Plovis URLs:', CONFIG.plovisUrls);

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
    L.control.layers(baseMaps, null, { position: 'topleft' }).addTo(map);

    // ===========================
    // LAYER GROUPS FOR GEOJSON
    // ===========================
    const layers = {
        jarang: L.layerGroup().addTo(map),
        sedang: L.layerGroup().addTo(map),
        lebat: L.layerGroup().addTo(map)
    };

    // ===========================
    // HELPER FUNCTIONS
    // ===========================
    function showLoading() {
        document.getElementById('loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.getElementById('loading-overlay').style.display = 'none';
    }

    function getStyleByDensity(density) {
        return {
            color: CONFIG.colors[density],
            weight: 2,
            opacity: 1,
            fillColor: CONFIG.colors[density],
            fillOpacity: 0.4,
            className: `density-${density}`
        };
    }

    function createPopupContent(feature) {
        const props = feature.properties;

        // Debug: Log ALL properties with their values
        console.log('📊 POPUP PROPERTIES - FULL LIST:');
        console.log('  1. BPDAS:', props.BPDAS);
        console.log('  2. KTTJ:', props.KTTJ);
        console.log('  3. SMBDT:', props.SMBDT);
        console.log('  4. THNBUAT:', props.THNBUAT);
        console.log('  5. INTS:', props.INTS);
        console.log('  6. REMARK:', props.REMARK);
        console.log('  7. STRUKTUR_V:', props.STRUKTUR_V);
        console.log('  8. LSMGR:', props.LSMGR);
        console.log('  9. Shape_Leng:', props.Shape_Leng);
        console.log(' 10. Shape_Area:', props.Shape_Area);
        console.log(' 11. KODE_PROV:', props.KODE_PROV);
        console.log(' 12. FUNGSIKWS:', props.FUNGSIKWS);
        console.log(' 13. NOSKKWS:', props.NOSKKWS);
        console.log(' 14. TGLSKKWS:', props.TGLSKKWS);
        console.log(' 15. LSKKWS:', props.LSKKWS);
        console.log(' 16. Kawasan:', props.Kawasan);
        console.log(' 17. KONSERVASI:', props.KONSERVASI);
        console.log(' 18. WADMKK:', props.WADMKK);
        console.log(' 19. WADMPR:', props.WADMPR);
        console.log(' 20. icon:', props.icon);
        console.log(' 21. colorIndex:', props.colorIndex);
        console.log('─────────────────────────────────');
        console.log('Total keys:', Object.keys(props).length);
        console.log('All keys:', Object.keys(props).join(', '));

        try {
            let html = '<div class="custom-popup">';

            // ===========================
            // HEADER - Wilayah (with fallback for testing)
            // ===========================
            const headerTitle = props.WADMKK || props.NAMA_INSTA || props.REMARK || 'Lokasi Mangrove';
            html += `<div class="popup-title">${headerTitle}</div>`;

            // ===========================
            // BADGES - Dynamic
            // ===========================
            html += '<div class="popup-badges">';

            // KTTJ Badge
            if (props.KTTJ) {
                let badgeColor = '#6b7280';
                const kttjLower = props.KTTJ.toLowerCase();
                if (kttjLower.includes('jarang')) badgeColor = '#8dd3c7';
                else if (kttjLower.includes('sedang')) badgeColor = '#d4d466';
                else if (kttjLower.includes('lebat')) badgeColor = '#9186ad';

                html += `<span style="background: ${badgeColor}; color: white; font-weight: 600;">${props.KTTJ}</span>`;
            }

            // Kawasan Badge
            const kawasan = props.Kawasan || props.KAWASAN;
            if (kawasan) {
                html += `<span style="background: #009966; color: white;">${kawasan}</span>`;
            }

            // Kode Provinsi Badge
            if (props.KODE_PROV) {
                html += `<span style="background: #1f2937; color: white; font-size: 10px;">Prov: ${props.KODE_PROV}</span>`;
            }

            html += '</div>'; // Close badges

            // ===========================
            // SECTION 1: INFORMASI WILAYAH
            // ===========================
            const hasWilayah = props.WADMKK || props.WADMPR || props.KODE_PROV;

            if (hasWilayah) {
                html += `
                    <div style="margin-top: 12px; padding: 10px; background: #f9fafb; border-radius: 6px;">
                        <div style="font-size: 11px; font-weight: 700; color: #1f2937; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                            📍 Informasi Wilayah
                        </div>`;

                if (props.WADMKK) {
                    html += `<div class="popup-info"><strong>Kabupaten/Kota:</strong> ${props.WADMKK}</div>`;
                }
                if (props.WADMPR) {
                    html += `<div class="popup-info"><strong>Provinsi:</strong> ${props.WADMPR}</div>`;
                }
                if (props.KODE_PROV) {
                    html += `<div class="popup-info"><strong>Kode Provinsi:</strong> ${props.KODE_PROV}</div>`;
                }

                html += '</div>';
            }

            // ===========================
            // SECTION 2: DATA MANGROVE
            // ===========================
            const hasMangrove = props.KTTJ || props.STRUKTUR_V || props.LSMGR || props.Shape_Leng || props.Shape_Area;

            if (hasMangrove) {
                html += `
                    <div style="margin-top: 10px; padding: 10px; background: #ecfdf5; border-radius: 6px;">
                        <div style="font-size: 11px; font-weight: 700; color: #065f46; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                            🌳 Data Mangrove
                        </div>`;

                if (props.KTTJ) {
                    html += `<div class="popup-info"><strong>Kategori:</strong> ${props.KTTJ}</div>`;
                }
                if (props.STRUKTUR_V) {
                    html += `<div class="popup-info"><strong>Struktur Vegetasi:</strong> ${props.STRUKTUR_V}</div>`;
                }
                if (props.LSMGR) {
                    const lsmgr = parseFloat(props.LSMGR);
                    let luasDisplay;
                    if (lsmgr > 1000) {
                        luasDisplay = `${(lsmgr / 10000).toFixed(4)} ha (${lsmgr.toFixed(2)} m²)`;
                    } else {
                        luasDisplay = `${lsmgr.toFixed(4)} ha`;
                    }
                    html += `<div class="popup-info"><strong>Luas Mangrove:</strong> ${luasDisplay}</div>`;
                }
                if (props.Shape_Leng) {
                    html += `<div class="popup-info"><strong>Shape Length:</strong> ${parseFloat(props.Shape_Leng).toFixed(8)}</div>`;
                }
                if (props.Shape_Area) {
                    html += `<div class="popup-info"><strong>Shape Area:</strong> ${parseFloat(props.Shape_Area).toFixed(8)}</div>`;
                }

                html += '</div>';
            }

            // ===========================
            // SECTION 3: INFORMASI KAWASAN
            // ===========================
            const hasKawasan = kawasan || props.FUNGSIKWS || props.KONSERVASI || props.LSKKWS;

            if (hasKawasan) {
                html += `
                    <div style="margin-top: 10px; padding: 10px; background: #fef3c7; border-radius: 6px;">
                        <div style="font-size: 11px; font-weight: 700; color: #92400e; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                            🏛️ Informasi Kawasan
                        </div>`;

                if (kawasan) {
                    html += `<div class="popup-info"><strong>Jenis Kawasan:</strong> ${kawasan}</div>`;
                }
                if (props.FUNGSIKWS) {
                    html += `<div class="popup-info"><strong>Fungsi Kawasan:</strong> ${props.FUNGSIKWS}</div>`;
                }
                if (props.KONSERVASI) {
                    const conservationColor = props.KONSERVASI.toLowerCase().includes('kawasan konservasi') ? '#16a34a' : '#f59e0b';
                    html += `<div class="popup-info" style="color: ${conservationColor}; font-weight: 600;">
                        <strong>Status Konservasi:</strong> ${props.KONSERVASI}
                    </div>`;
                }
                if (props.LSKKWS) {
                    const lsKkws = parseFloat(props.LSKKWS);
                    html += `<div class="popup-info"><strong>Luas SK Kawasan:</strong> ${lsKkws.toLocaleString('id-ID')} ha</div>`;
                }

                html += '</div>';
            }

            // ===========================
            // SECTION 4: LEGALITAS & SK
            // ===========================
            const hasLegalitas = props.NOSKKWS || props.TGLSKKWS || props.INTS;

            if (hasLegalitas) {
                html += `
                    <div style="margin-top: 10px; padding: 10px; background: #e0f2fe; border-radius: 6px;">
                        <div style="font-size: 11px; font-weight: 700; color: #075985; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                            📋 Legalitas & SK
                        </div>`;

                if (props.NOSKKWS) {
                    html += `<div class="popup-info"><strong>Nomor SK:</strong> ${props.NOSKKWS}</div>`;
                }
                if (props.TGLSKKWS) {
                    let tglDisplay = props.TGLSKKWS;
                    if (props.TGLSKKWS.includes('-')) {
                        try {
                            const date = new Date(props.TGLSKKWS);
                            tglDisplay = date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                        } catch (e) {
                            console.warn('Date parse error:', e);
                        }
                    }
                    html += `<div class="popup-info"><strong>Tanggal SK:</strong> ${tglDisplay}</div>`;
                }
                if (props.INTS) {
                    html += `<div class="popup-info"><strong>Instansi:</strong> ${props.INTS}</div>`;
                }

                html += '</div>';
            }

            // ===========================
            // SECTION 5: SUMBER DATA
            // ===========================
            const hasSumberData = props.BPDAS || props.THNBUAT || props.TAHUN || props.SMBDT;

            if (hasSumberData) {
                html += `
                    <div style="margin-top: 10px; padding: 10px; background: #f3f4f6; border-radius: 6px;">
                        <div style="font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                            📊 Sumber Data
                        </div>`;

                if (props.BPDAS) {
                    html += `<div class="popup-info"><strong>BPDAS:</strong> ${props.BPDAS}</div>`;
                }

                const tahun = props.THNBUAT || props.TAHUN;
                if (tahun) {
                    html += `<div class="popup-info"><strong>Tahun Data:</strong> ${tahun}</div>`;
                }

                if (props.SMBDT) {
                    html += `<div class="popup-info" style="font-size: 11px; line-height: 1.4;">
                        <strong>Sumber:</strong> ${props.SMBDT}
                    </div>`;
                }

                html += '</div>';
            }

            // ===========================
            // SECTION 6: CATATAN (Conditional)
            // ===========================
            const remark = props.REMARK || props.NAMA_INSTA;
            const hasValidRemark = remark &&
                                   remark !== 'Tidak ada catatan' &&
                                   remark !== 'TIDAK ADA CATATAN' &&
                                   remark.trim() !== '';

            if (hasValidRemark) {
                html += `
                    <div style="margin-top: 10px; padding: 8px; background: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 4px;">
                        <div style="font-size: 11px; font-weight: 700; color: #92400e; margin-bottom: 4px;">
                            📝 CATATAN
                        </div>
                        <div style="font-size: 11px; color: #78350f; line-height: 1.4;">
                            ${remark}
                        </div>
                    </div>`;
            }

            // ===========================
            // DEBUG INFO (removable in production)
            // ===========================
            html += `
                <div style="margin-top: 10px; padding: 6px; background: #f0f0f0; border-radius: 4px; font-size: 10px; color: #666;">
                    <strong>Debug:</strong> ${Object.keys(props).length} attributes loaded
                </div>`;

            html += '</div>'; // Close custom-popup

            console.log('✅ Popup HTML generated successfully');
            return html;

        } catch (error) {
            console.error('❌ Error creating popup content:', error);

            // Fallback error display
            return `
                <div class="custom-popup">
                    <div class="popup-title">Error Loading Data</div>
                    <div style="padding: 10px; color: #dc2626;">
                        <strong>Error:</strong> ${error.message}
                    </div>
                    <div style="padding: 10px; font-size: 11px; background: #f3f4f6;">
                        <strong>Available properties:</strong><br>
                        ${Object.keys(props).join(', ')}
                    </div>
                </div>
            `;
        }
    }

    function onEachFeature(feature, layer, density) {
        // Debug: Log feature properties
        console.log(`🔍 Feature properties for ${density}:`, Object.keys(feature.properties).length, 'keys');

        // Bind popup
        layer.bindPopup(createPopupContent(feature), {
            maxWidth: 380,
            minWidth: 320,
            maxHeight: 600,
            autoPan: true,
            autoPanPadding: [50, 50],
            className: 'leaflet-popup-custom'
        });

        // Bind tooltip - use WADMKK if available, otherwise KTTJ
        const tooltipText = feature.properties.WADMKK || feature.properties.KTTJ || 'Mangrove Area';
        layer.bindTooltip(tooltipText, {
            permanent: false,
            direction: 'top',
            className: 'custom-tooltip'
        });

        // Add hover effects
        layer.on({
            mouseover: function(e) {
                this.setStyle({
                    fillOpacity: 0.7,
                    weight: 3
                });
            },
            mouseout: function(e) {
                this.setStyle(getStyleByDensity(density));
            },
            click: function(e) {
                console.log('🖱️ Polygon clicked! Properties:', feature.properties);
            }
        });
    }

    // ===========================
    // LOAD GEOJSON DATA FROM PLOVIS API DIRECTLY
    // ===========================
    async function loadGeoJsonData() {
        showLoading();

        try {
            // Load each density layer from PLOVIS directly
            for (const density in CONFIG.plovisUrls) {
                const url = CONFIG.plovisUrls[density];
                console.log(`🌐 Fetching ${density} from Plovis:`, url);

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status} for ${density}`);
                }

                let geojsonData = await response.json();

                // IMPORTANT: Plovis wraps GeoJSON in a "geojson" key
                if (geojsonData.geojson && typeof geojsonData.geojson === 'object') {
                    console.log(`📦 Unwrapping Plovis format for ${density}`);
                    geojsonData = geojsonData.geojson;
                }

                console.log(`✓ Loaded ${density}:`, geojsonData.features?.length || 0, 'features');
                console.log(`📊 Sample properties for ${density}:`, geojsonData.features?.[0]?.properties);

                // Validate structure
                if (!geojsonData.features || !Array.isArray(geojsonData.features)) {
                    console.error(`❌ Invalid GeoJSON structure for ${density}:`, geojsonData);
                    throw new Error(`Invalid GeoJSON format for ${density}`);
                }

                // Add GeoJSON to map
                const geoJsonLayer = L.geoJSON(geojsonData, {
                    style: getStyleByDensity(density),
                    onEachFeature: (feature, layer) => onEachFeature(feature, layer, density)
                });

                geoJsonLayer.addTo(layers[density]);

                console.log(`✅ ${density} layer added to map with ${geojsonData.features.length} polygons`);
            }

            console.log('🎉 All GeoJSON layers loaded successfully from Plovis!');
            hideLoading();

        } catch (error) {
            console.error('❌ Error loading GeoJSON from Plovis:', error);
            console.error('Error details:', error.message);
            hideLoading();

            alert('Gagal memuat data peta dari Plovis API.\n\nError: ' + error.message + '\n\nSilakan refresh halaman atau cek koneksi internet.');
        }
    }

    // ===========================
    // UI INTERACTION FUNCTIONS
    // ===========================
    function toggleLegend(type) {
        const body = document.getElementById(`legend-body-${type}`);
        const btn = body.previousElementSibling.querySelector('.legend-toggle-btn');
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

    // ===========================
    // INITIALIZE
    // ===========================
    // Load GeoJSON data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadGeoJsonData();
    });

    // Fix map rendering
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
</script>
@endpush
