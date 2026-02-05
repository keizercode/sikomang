let map;
let marker;
let currentLatLng = null;

// ===========================
// CONFIGURATION
// ===========================
const CONFIG = {
    defaultCenter: [-6.1751, 106.865], // Jakarta center
    defaultZoom: 12,
    markerColor: "#009966", // Green for mangrove theme
    maxZoom: 19,
    coordinatePrecision: 6,
};

// ===========================
// MAP INITIALIZATION
// ===========================
function initializeLocationMap() {
    console.log("Initializing location map...");

    // Get initial coordinates from form inputs (if editing existing location)
    const latInput = document.getElementById("latitude");
    const lngInput = document.getElementById("longitude");

    let initialLat = latInput.value
        ? parseFloat(latInput.value)
        : CONFIG.defaultCenter[0];
    let initialLng = lngInput.value
        ? parseFloat(lngInput.value)
        : CONFIG.defaultCenter[1];

    // Validate coordinates
    if (isNaN(initialLat) || isNaN(initialLng)) {
        initialLat = CONFIG.defaultCenter[0];
        initialLng = CONFIG.defaultCenter[1];
    }

    currentLatLng = [initialLat, initialLng];

    // Initialize map
    map = L.map("locationMap", {
        center: currentLatLng,
        zoom: CONFIG.defaultZoom,
        zoomControl: true,
    });

    // Add tile layers
    const osmLayer = L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: CONFIG.maxZoom,
        },
    ).addTo(map);

    const satelliteLayer = L.tileLayer(
        "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        {
            attribution: "Tiles &copy; Esri",
            maxZoom: CONFIG.maxZoom,
        },
    );

    // Layer control
    const baseMaps = {
        Standard: osmLayer,
        Satelit: satelliteLayer,
    };
    L.control.layers(baseMaps, null, { position: "topright" }).addTo(map);

    // Add initial marker if coordinates exist
    if (latInput.value && lngInput.value) {
        updateMarker(initialLat, initialLng);
    }

    // Setup event listeners
    setupMapEvents();
    setupFormIntegration();
    setupMapControls();

    // Fix map rendering
    setTimeout(() => {
        map.invalidateSize();
    }, 100);

    console.log("Map initialized successfully at:", currentLatLng);
}

// ===========================
// MARKER MANAGEMENT
// ===========================
function updateMarker(lat, lng) {
    // Validate coordinates
    if (isNaN(lat) || isNaN(lng)) {
        console.error("Invalid coordinates:", lat, lng);
        return;
    }

    currentLatLng = [lat, lng];

    // Remove existing marker
    if (marker) {
        map.removeLayer(marker);
    }

    // Create custom green marker icon
    const customIcon = L.divIcon({
        className: "custom-marker",
        html: `
            <div style="
                position: relative;
                width: 32px;
                height: 32px;
            ">
                <div style="
                    background: ${CONFIG.markerColor};
                    width: 100%;
                    height: 100%;
                    border-radius: 50% 50% 50% 0;
                    transform: rotate(-45deg);
                    border: 3px solid white;
                    box-shadow: 0 3px 8px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <div style="
                        width: 12px;
                        height: 12px;
                        background: white;
                        border-radius: 50%;
                        transform: rotate(45deg);
                    "></div>
                </div>
            </div>
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
    });

    // Add new marker
    marker = L.marker(currentLatLng, {
        icon: customIcon,
        draggable: true,
    }).addTo(map);

    // Update form inputs
    updateCoordinates(lat, lng);

    // Add popup
    const locationName =
        document.querySelector('input[name="name"]').value || "Lokasi Mangrove";
    marker
        .bindPopup(
            `
        <strong>${locationName}</strong><br>
        Lat: ${lat.toFixed(CONFIG.coordinatePrecision)}<br>
        Lng: ${lng.toFixed(CONFIG.coordinatePrecision)}
    `,
        )
        .openPopup();

    // Handle marker drag
    marker.on("dragend", function (e) {
        const position = e.target.getLatLng();
        updateMarker(position.lat, position.lng);
        console.log("Marker dragged to:", position);
    });

    // Center map on marker
    map.setView(currentLatLng, map.getZoom());
}

function updateCoordinates(lat, lng) {
    const latInput = document.getElementById("latitude");
    const lngInput = document.getElementById("longitude");

    if (latInput && lngInput) {
        latInput.value = lat.toFixed(CONFIG.coordinatePrecision);
        lngInput.value = lng.toFixed(CONFIG.coordinatePrecision);
    }
}

// ===========================
// EVENT LISTENERS
// ===========================
function setupMapEvents() {
    // Click on map to place marker
    map.on("click", function (e) {
        updateMarker(e.latlng.lat, e.latlng.lng);
        console.log("Map clicked at:", e.latlng);
    });
}

function setupFormIntegration() {
    const latInput = document.getElementById("latitude");
    const lngInput = document.getElementById("longitude");

    if (!latInput || !lngInput) {
        console.warn("Coordinate inputs not found");
        return;
    }

    // Debounce function to prevent excessive updates
    let debounceTimer;
    const debounceDelay = 500; // ms

    function handleCoordinateInput() {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);

            if (!isNaN(lat) && !isNaN(lng)) {
                // Validate coordinate ranges
                if (lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    updateMarker(lat, lng);
                } else {
                    console.warn("Coordinates out of valid range");
                }
            }
        }, debounceDelay);
    }

    // Listen to manual input changes
    latInput.addEventListener("input", handleCoordinateInput);
    lngInput.addEventListener("input", handleCoordinateInput);
}

function setupMapControls() {
    // Use Current Location button
    const useLocationBtn = document.getElementById("useCurrentLocation");
    if (useLocationBtn) {
        useLocationBtn.addEventListener("click", function () {
            if ("geolocation" in navigator) {
                useLocationBtn.disabled = true;
                useLocationBtn.innerHTML =
                    '<i class="mdi mdi-loading mdi-spin"></i> Mengambil lokasi...';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        updateMarker(lat, lng);
                        map.setView([lat, lng], 16);

                        useLocationBtn.disabled = false;
                        useLocationBtn.innerHTML =
                            '<i class="mdi mdi-crosshairs-gps"></i> Gunakan Lokasi Saya';

                        // Show success notification
                        if (typeof alertify !== "undefined") {
                            alertify.success("Lokasi GPS berhasil diambil");
                        }

                        console.log("GPS location acquired:", lat, lng);
                    },
                    function (error) {
                        useLocationBtn.disabled = false;
                        useLocationBtn.innerHTML =
                            '<i class="mdi mdi-crosshairs-gps"></i> Gunakan Lokasi Saya';

                        let errorMsg = "Gagal mengambil lokasi GPS";
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg =
                                    "Akses lokasi ditolak. Mohon izinkan akses lokasi.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg = "Informasi lokasi tidak tersedia.";
                                break;
                            case error.TIMEOUT:
                                errorMsg = "Waktu permintaan lokasi habis.";
                                break;
                        }

                        if (typeof alertify !== "undefined") {
                            alertify.error(errorMsg);
                        } else {
                            alert(errorMsg);
                        }

                        console.error("GPS error:", error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0,
                    },
                );
            } else {
                alert("Geolocation tidak didukung oleh browser Anda");
            }
        });
    }

    // Reset Map button
    const resetBtn = document.getElementById("resetMap");
    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }

            document.getElementById("latitude").value = "";
            document.getElementById("longitude").value = "";

            map.setView(CONFIG.defaultCenter, CONFIG.defaultZoom);

            if (typeof alertify !== "undefined") {
                alertify.message("Marker dihapus");
            }

            console.log("Map reset");
        });
    }

    // Center Jakarta button
    const centerJakartaBtn = document.getElementById("centerJakarta");
    if (centerJakartaBtn) {
        centerJakartaBtn.addEventListener("click", function () {
            map.setView(CONFIG.defaultCenter, CONFIG.defaultZoom);

            if (typeof alertify !== "undefined") {
                alertify.message("Peta dipusatkan ke Jakarta");
            }

            console.log("Map centered to Jakarta");
        });
    }
}

// ===========================
// INITIALIZE ON DOM READY
// ===========================
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM loaded, initializing location map...");

    // Check if map container exists
    const mapContainer = document.getElementById("locationMap");
    if (mapContainer) {
        initializeLocationMap();
    } else {
        console.warn("Map container #locationMap not found");
    }
});

// Export functions for external use if needed
window.adminLocationMap = {
    updateMarker,
    getCoordinates: () => currentLatLng,
};
