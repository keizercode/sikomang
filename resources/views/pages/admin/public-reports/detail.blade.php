@extends('layouts.admin.master')

@section('title', 'Detail Laporan: ' . $report->report_number)

@section('css')
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

    .photo-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .photo-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .photo-item:hover {
        transform: scale(1.05);
    }

    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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

    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
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

        <div class="row">
            {{-- Main Content --}}
            <div class="col-lg-8">

                {{-- Informasi Laporan --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Informasi Laporan</h4>
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

                {{-- Foto Pendukung --}}
                @if($report->hasPhotos())
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Foto Pendukung
                            <span class="badge bg-primary ms-2">{{ count($report->photo_urls) }} Foto</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="photo-gallery">
                            @foreach($report->photo_urls as $index => $photo)
                            <div class="photo-item" data-bs-toggle="modal" data-bs-target="#photoModal"
                                 onclick="showPhoto('{{ $photo }}', {{ $index + 1 }})">
                                <img src="{{ $photo }}" alt="Foto {{ $index + 1 }}" loading="lazy">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Catatan Admin --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Catatan Admin</h4>
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

                {{-- Data Pelapor --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Data Pelapor</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Nama</label>
                            <p class="mb-0 fw-semibold">{{ $report->reporter_name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0">
                                <a href="mailto:{{ $report->reporter_email }}">{{ $report->reporter_email }}</a>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Telepon</label>
                            <p class="mb-0">
                                <a href="tel:{{ $report->reporter_phone }}">{{ $report->reporter_phone }}</a>
                            </p>
                        </div>
                        @if($report->reporter_address)
                        <div class="mb-3">
                            <label class="text-muted small">Alamat</label>
                            <p class="mb-0">{{ $report->reporter_address }}</p>
                        </div>
                        @endif
                        @if($report->reporter_organization)
                        <div class="mb-3">
                            <label class="text-muted small">Organisasi</label>
                            <p class="mb-0">{{ $report->reporter_organization }}</p>
                        </div>
                        @endif
                        <div class="mb-0">
                            <label class="text-muted small">IP Address</label>
                            <p class="mb-0"><code>{{ $report->ip_address }}</code></p>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aksi Cepat</h5>
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
                        <h5 class="card-title mb-0">Riwayat</h5>
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

{{-- Modal for Photo --}}
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Foto Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalPhoto" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

{{-- Modal for Note --}}
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
<script>
const reportId = '{{ $keyId }}';

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
        text: "Data laporan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
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
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menghapus laporan',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}
</script>
@endsection
