@extends('layouts.admin.master')

@section('title', 'Detail Laporan: ' . $report->report_number)

@section('css')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Custom styles for report detail */
    .report-detail-header {
        background: linear-gradient(135deg, #009966 0%, #00724c 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
    }

    .report-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Reporter Info Card - Horizontal Layout */
    .reporter-info-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .reporter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .reporter-item {
        display: flex;
        align-items: start;
        gap: 0.75rem;
    }

    .reporter-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .reporter-icon i {
        font-size: 1.25rem;
    }

    .reporter-content {
        flex: 1;
        min-width: 0;
    }

    .reporter-label {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .reporter-value {
        color: #1f2937;
        font-weight: 500;
        word-break: break-word;
    }

    .reporter-value a {
        color: #009966;
        text-decoration: none;
    }

    .reporter-value a:hover {
        text-decoration: underline;
    }

    /* Map Container */
    .map-container {
        height: 400px;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        background: #f3f4f6;
    }

    #locationMap {
        height: 100%;
        width: 100%;
    }

    .location-info-overlay {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 1000;
        background: white;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        max-width: 300px;
    }

    .location-info-overlay strong {
        color: #009966;
        display: block;
        margin-bottom: 0.25rem;
    }

    .location-info-overlay small {
        color: #6b7280;
    }

    /* Photo Gallery */
    .photo-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .photo-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 0.75rem;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        background: #f3f4f6;
    }

    .photo-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .photo-item:hover img {
        transform: scale(1.1);
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .photo-item:hover .photo-overlay {
        opacity: 1;
    }

    .photo-overlay svg {
        width: 32px;
        height: 32px;
        color: white;
    }

    .photo-number {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .no-photos {
        text-align: center;
        padding: 3rem 1rem;
        background: #f9fafb;
        border-radius: 0.75rem;
        border: 2px dashed #d1d5db;
    }

    .no-photos svg {
        width: 64px;
        height: 64px;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #009966;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #e5e7eb;
    }

    .info-row {
        display: flex;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6b7280;
        min-width: 180px;
    }

    .info-value {
        color: #1f2937;
        flex: 1;
    }

    .action-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .action-card h5 {
        margin-bottom: 1rem;
        color: #1f2937;
    }

    /* Modal improvements */
    #photoModal .modal-dialog {
        max-width: 900px;
    }

    #photoModal img {
        max-height: 80vh;
        width: auto;
        margin: 0 auto;
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .reporter-grid {
            grid-template-columns: 1fr;
        }

        .map-container {
            height: 300px;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Header dengan status --}}
        <div class="report-detail-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-2">{{ $report->report_number }}</h2>
                    <p class="mb-0 opacity-75">
                        <i class="mdi mdi-clock-outline me-1"></i>
                        Diterima: {{ $report->created_at->format('d F Y, H:i') }} WIB
                    </p>
                </div>
                <div>
                    <span class="report-status-badge bg-{{ $report->status_color }}">
                        <i class="mdi mdi-circle-small"></i>
                        {{ $report->status_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Data Pelapor - Horizontal Layout --}}
        <div class="reporter-info-card">
            <h5 class="mb-3">
                <i class="mdi mdi-account-circle text-primary"></i>
                Data Pelapor
            </h5>
            <div class="reporter-grid">
                <div class="reporter-item">
                    <div class="reporter-icon bg-primary bg-opacity-10">
                        <i class="mdi mdi-account text-primary"></i>
                    </div>
                    <div class="reporter-content">
                        <div class="reporter-label">Nama Lengkap</div>
                        <div class="reporter-value">{{ $report->reporter_name }}</div>
                    </div>
                </div>

                <div class="reporter-item">
                    <div class="reporter-icon bg-success bg-opacity-10">
                        <i class="mdi mdi-email text-success"></i>
                    </div>
                    <div class="reporter-content">
                        <div class="reporter-label">Email</div>
                        <div class="reporter-value">
                            <a>{{ $report->reporter_email }}</a>
                        </div>
                    </div>
                </div>

                <div class="reporter-item">
                    <div class="reporter-icon bg-info bg-opacity-10">
                        <i class="mdi mdi-phone text-info"></i>
                    </div>
                    <div class="reporter-content">
                        <div class="reporter-label">Telepon</div>
                        <div class="reporter-value">
                            <a>{{ $report->reporter_phone }}</a>
                        </div>
                    </div>
                </div>

                @if($report->reporter_address)
                <div class="reporter-item">
                    <div class="reporter-icon bg-warning bg-opacity-10">
                        <i class="mdi mdi-map-marker text-warning"></i>
                    </div>
                    <div class="reporter-content">
                        <div class="reporter-label">Alamat</div>
                        <div class="reporter-value">{{ $report->reporter_address }}</div>
                    </div>
                </div>
                @endif

                @if($report->reporter_organization)
                <div class="reporter-item">
                    <div class="reporter-icon bg-danger bg-opacity-10">
                        <i class="mdi mdi-office-building text-danger"></i>
                    </div>
                    <div class="reporter-content">
                        <div class="reporter-label">Organisasi</div>
                        <div class="reporter-value">{{ $report->reporter_organization }}</div>
                    </div>
                </div>
                @endif

                <div class="reporter-item">
                    <div class="reporter-icon bg-secondary bg-opacity-10">
                        <i class="mdi mdi-ip text-secondary"></i>
                    </div>
                    <div class="reporter-content">
                        <div class="reporter-label">IP Address</div>
                        <div class="reporter-value"><code>{{ $report->ip_address }}</code></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Main Content - Left Side --}}
            <div class="col-lg-6">

                {{-- Informasi Laporan --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-file-document-outline text-primary"></i>
                            Informasi Laporan
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Nomor Laporan</span>
                            <span class="info-value">
                                <strong>{{ $report->report_number }}</strong>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Lokasi Mangrove</span>
                            <span class="info-value">
                                <i class="mdi mdi-map-marker text-danger me-1"></i>
                                <strong>{{ $report->location->name }}</strong>
                                @if($report->location->region)
                                    <br><small class="text-muted">{{ $report->location->region }}</small>
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jenis Laporan</span>
                            <span class="info-value">
                                <span class="badge bg-{{ ['kerusakan' => 'danger', 'pencemaran' => 'warning', 'penebangan_liar' => 'dark', 'kondisi_baik' => 'success', 'lainnya' => 'secondary'][$report->report_type] ?? 'secondary' }}">
                                    {{ $report->report_type_label }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tingkat Urgensi</span>
                            <span class="info-value">
                                <span class="badge bg-{{ $report->urgency_color }}">
                                    @if($report->urgency_level === 'darurat')
                                        <i class="mdi mdi-alert me-1"></i>
                                    @endif
                                    {{ $report->urgency_label }}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Deskripsi</span>
                            <span class="info-value">{{ $report->description }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Preview Lokasi - Right Side --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-map text-primary"></i>
                            Preview Lokasi
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="map-container position-relative">

                            <div id="locationMap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">

                {{-- Foto Pendukung --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-image-multiple text-primary"></i>
                            Foto Pendukung
                        </h4>
                        @if(!empty($report->photo_urls) && is_array($report->photo_urls) && count($report->photo_urls) > 0)
                        <span class="badge bg-primary">{{ count($report->photo_urls) }} Foto</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if(!empty($report->photo_urls) && is_array($report->photo_urls) && count($report->photo_urls) > 0)
                            <div class="photo-gallery">
                                @foreach($report->photo_urls as $index => $photoUrl)
                                    @php
                                        // Clean and validate photo URL
                                        $photoPath = str_starts_with($photoUrl, '/') ? $photoUrl : '/' . $photoUrl;
                                        $fullUrl = asset($photoPath);
                                    @endphp
                                    <div class="photo-item"
                                         data-bs-toggle="modal"
                                         data-bs-target="#photoModal"
                                         onclick="showPhoto('{{ $fullUrl }}', {{ $index + 1 }})">
                                        <img src="{{ $fullUrl }}"
                                             alt="Foto {{ $index + 1 }}"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22 viewBox=%220 0 200 200%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22200%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2245%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2248%22 fill=%22%23ef4444%22%3E✕%3C/text%3E%3Ctext x=%2250%25%22 y=%2260%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2212%22 fill=%22%239ca3af%22%3EFoto Error%3C/text%3E%3C/svg%3E';">
                                        <div class="photo-number">{{ $index + 1 }}</div>
                                        <div class="photo-overlay">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                            </svg>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-photos">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-muted mb-0">Tidak ada foto pendukung yang dilampirkan</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Catatan Admin --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-note-text text-primary"></i>
                            Catatan Admin
                        </h4>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#noteModal">
                            <i class="mdi mdi-pencil"></i> Tambah/Edit Catatan
                        </button>
                    </div>
                    <div class="card-body">
                        @if($report->admin_notes)
                            <div class="alert alert-info mb-0">
                                <i class="mdi mdi-note-text me-2"></i>
                                {{ $report->admin_notes }}
                            </div>
                        @else
                            <p class="text-muted mb-0">Belum ada catatan admin.</p>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Quick Actions --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-lightning-bolt text-warning"></i>
                            Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">

                        {{-- Update Status --}}
                        <div class="action-card">
                            <h5>
                                <i class="mdi mdi-update text-primary"></i>
                                Update Status
                            </h5>
                            <select class="form-select" id="statusSelect">
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="verified" {{ $report->status === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                <option value="in_review" {{ $report->status === 'in_review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                                <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang Ditangani</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                                <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            <button class="btn btn-primary btn-sm mt-2 w-100" onclick="updateStatus()">
                                <i class="mdi mdi-check"></i> Update Status
                            </button>
                        </div>

                        {{-- Quick Verify --}}
                        @if($report->status === 'pending')
                        <button class="btn btn-success w-100 mb-2" onclick="verifyReport()">
                            <i class="mdi mdi-check-circle"></i> Verifikasi Laporan
                        </button>
                        @endif

                        {{-- Delete --}}
                        <button class="btn btn-danger w-100" onclick="deleteReport()">
                            <i class="mdi mdi-delete"></i> Hapus Laporan
                        </button>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-timeline-clock text-primary"></i>
                            Riwayat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <strong>Laporan Dibuat</strong>
                                <p class="text-muted small mb-0">{{ $report->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            @if($report->verified_at)
                            <div class="timeline-item">
                                <strong>Diverifikasi</strong>
                                <p class="text-muted small mb-0">
                                    {{ $report->verified_at->format('d M Y, H:i') }}
                                    @if($report->verifier)
                                        <br>oleh {{ $report->verifier->name }}
                                    @endif
                                </p>
                            </div>
                            @endif
                            @if($report->resolved_at)
                            <div class="timeline-item">
                                <strong>Diselesaikan</strong>
                                <p class="text-muted small mb-0">{{ $report->resolved_at->format('d M Y, H:i') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Photo Modal --}}
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Foto Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center bg-dark">
                <img src="" id="modalPhoto" class="img-fluid">
            </div>
        </div>
    </div>
</div>

{{-- Note Modal --}}
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catatan Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="adminNotes" rows="5"
                          placeholder="Masukkan catatan tentang laporan ini...">{{ $report->admin_notes }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveNote()">Simpan Catatan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const reportId = '{{ $keyId }}';

// Initialize Map
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $report->location->latitude ?? -6.1751 }};
    const lng = {{ $report->location->longitude ?? 106.8650 }};

    // Create map
    const map = L.map('locationMap').setView([lat, lng], 14);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Custom marker icon
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `
            <div style="
                background: #009966;
                width: 40px;
                height: 40px;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                border: 3px solid white;
                box-shadow: 0 3px 10px rgba(0,0,0,0.3);
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <i class="mdi mdi-alert" style="
                    color: white;
                    font-size: 20px;
                    transform: rotate(45deg);
                "></i>
            </div>
        `,
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    // Add marker
    const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);

    // Add popup
    marker.bindPopup(`
        <div style="min-width: 200px;">
            <strong style="color: #009966;">{{ $report->location->name }}</strong><br>
            @if($report->location->region)
            <small style="color: #6b7280;">{{ $report->location->region }}</small><br>
            @endif
            <small style="color: #6b7280;">Koordinat: ${lat.toFixed(5)}, ${lng.toFixed(5)}</small>
        </div>
    `).openPopup();

    // Add circle to show area
    L.circle([lat, lng], {
        color: '#009966',
        fillColor: '#009966',
        fillOpacity: 0.1,
        radius: 500
    }).addTo(map);
});

function showPhoto(url, index) {
    document.getElementById('modalPhoto').src = url;
    document.getElementById('photoModalLabel').textContent = `Foto Laporan #${index}`;
}

function updateStatus() {
    const status = document.getElementById('statusSelect').value;

    Swal.fire({
        title: 'Update Status?',
        text: `Status akan diubah menjadi: ${document.getElementById('statusSelect').options[document.getElementById('statusSelect').selectedIndex].text}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#009966',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Update',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/public-reports/${reportId}/update-status`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#009966'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mengupdate status',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function verifyReport() {
    Swal.fire({
        title: 'Verifikasi Laporan?',
        text: "Laporan akan ditandai sebagai terverifikasi",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Verifikasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/public-reports/${reportId}/verify`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat verifikasi',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function saveNote() {
    const notes = document.getElementById('adminNotes').value;

    if (!notes.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Catatan tidak boleh kosong',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    $.ajax({
        url: `/admin/public-reports/${reportId}/add-note`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            admin_notes: notes
        },
        success: function(response) {
            if (response.success) {
                $('#noteModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    confirmButtonColor: '#009966'
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan catatan',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

function deleteReport() {
    Swal.fire({
        title: 'Hapus Laporan?',
        html: '<p class="mb-2">Data laporan dan <strong>semua foto</strong> akan dihapus permanen!</p><p class="text-danger small">⚠️ Tindakan ini tidak dapat dibatalkan</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/public-reports/${reportId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#009966'
                        }).then(() => {
                            window.location.href = '{{ route("admin.public-reports.index") }}';
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus laporan',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}
</script>
@endsection
