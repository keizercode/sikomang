@extends('layouts.admin.master')

@section('title', 'Laporan Masyarakat')

@section('css')
<style>
    .filter-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .table-actions {
        display: flex;
        gap: 0.25rem;
    }

    .badge-custom {
        padding: 0.375rem 0.75rem;
        font-size: 0.813rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Laporan Masyarakat</h4>
                        <p class="text-muted mb-0">Kelola laporan kondisi mangrove dari masyarakat</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="refreshData()">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="mdi mdi-export"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportData('excel'); return false;">
                                    <i class="mdi mdi-file-excel text-success"></i> Export Excel
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportData('pdf'); return false;">
                                    <i class="mdi mdi-file-pdf text-danger"></i> Export PDF
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportData('csv'); return false;">
                                    <i class="mdi mdi-file-delimited text-primary"></i> Export CSV
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            @foreach([
                ['id' => 'totalReports', 'label' => 'Total Laporan', 'icon' => 'file-document', 'color' => 'primary'],
                ['id' => 'pendingReports', 'label' => 'Menunggu Verifikasi', 'icon' => 'clock-outline', 'color' => 'warning'],
                ['id' => 'inProgressReports', 'label' => 'Sedang Ditangani', 'icon' => 'progress-check', 'color' => 'info'],
                ['id' => 'resolvedReports', 'label' => 'Selesai', 'icon' => 'check-circle', 'color' => 'success']
            ] as $stat)
            <div class="col-md-3">
                <div class="card stat-card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">{{ $stat['label'] }}</p>
                                <h4 class="mb-0" id="{{ $stat['id'] }}">
                                    <div class="spinner-border spinner-border-sm text-{{ $stat['color'] }}" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </h4>
                            </div>
                            <div class="avatar-sm rounded-circle bg-{{ $stat['color'] }} align-self-center mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-{{ $stat['color'] }}">
                                    <i class="mdi mdi-{{ $stat['icon'] }} font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="filter-card">
            <h5 class="mb-3">
                <i class="mdi mdi-filter-variant"></i> Filter & Pencarian
            </h5>
            <div class="filter-row">
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus" onchange="applyFilters()">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="verified">Terverifikasi</option>
                        <option value="in_review">Sedang Ditinjau</option>
                        <option value="in_progress">Sedang Ditangani</option>
                        <option value="resolved">Selesai</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jenis Laporan</label>
                    <select class="form-select" id="filterType" onchange="applyFilters()">
                        <option value="">Semua Jenis</option>
                        <option value="kerusakan">Kerusakan</option>
                        <option value="pencemaran">Pencemaran</option>
                        <option value="penebangan_liar">Penebangan Liar</option>
                        <option value="kondisi_baik">Kondisi Baik</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Urgensi</label>
                    <select class="form-select" id="filterUrgency" onchange="applyFilters()">
                        <option value="">Semua Tingkat</option>
                        <option value="rendah">Rendah</option>
                        <option value="sedang">Sedang</option>
                        <option value="tinggi">Tinggi</option>
                        <option value="darurat">Darurat</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Pencarian</label>
                    <input type="text" class="form-control" id="searchInput"
                           placeholder="Cari nomor laporan, pelapor..." onkeyup="applyFilters()">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                    <i class="mdi mdi-refresh"></i> Reset Filter
                </button>
                <span class="text-muted align-self-center" id="filterInfo">
                    Menampilkan semua data
                </span>
            </div>
        </div>

        {{-- Reports Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-centered align-middle" id="reportsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>No. Laporan</th>
                                <th>Lokasi</th>
                                <th>Pelapor</th>
                                <th>Kontak</th>
                                <th>Jenis</th>
                                <th>Urgensi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
let allReports = [];
let filteredReports = [];

$(document).ready(function() {
    loadGridData();
});

function loadGridData() {
    $.ajax({
        url: '{{ route("admin.public-reports.grid") }}',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            allReports = data;
            filteredReports = data;
            updateStatistics(data);
            renderTable(data);
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            $('#tableBody').html(`
                <tr>
                    <td colspan="10" class="text-center text-danger">
                        <i class="mdi mdi-alert-circle-outline"></i> Gagal memuat data
                    </td>
                </tr>
            `);
        }
    });
}

function renderTable(data) {
    const tbody = $('#tableBody');
    tbody.empty();

    if (data.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="10" class="text-center text-muted">
                    <i class="mdi mdi-inbox"></i><br>
                    Tidak ada data yang sesuai dengan filter
                </td>
            </tr>
        `);
        return;
    }

    data.forEach((item, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td><strong class="text-primary">${item.report_number}</strong></td>
                <td>
                    <i class="mdi mdi-map-marker text-danger"></i>
                    ${item.location}
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-xs me-2">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                ${item.reporter_name.charAt(0).toUpperCase()}
                            </span>
                        </div>
                        <div>
                            <div class="fw-semibold">${item.reporter_name}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <small>
                        <i class="mdi mdi-email"></i> ${item.reporter_email}<br>
                    </small>
                </td>
                <td>${item.report_type}</td>
                <td>${item.urgency}</td>
                <td>${item.status}</td>
                <td>
                    <small class="text-muted">
                        <i class="mdi mdi-clock-outline"></i> ${item.created_at}
                    </small>
                </td>
                <td>
                    <div class="table-actions">
                        ${item.action}
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function updateStatistics(data) {
    const stats = {
        total: data.length,
        pending: data.filter(r => r.status.includes('Menunggu')).length,
        inProgress: data.filter(r => r.status.includes('Ditangani') || r.status.includes('Ditinjau')).length,
        resolved: data.filter(r => r.status.includes('Selesai')).length
    };

    $('#totalReports').html(`<span class="counter-value" data-target="${stats.total}">${stats.total}</span>`);
    $('#pendingReports').html(`<span class="counter-value" data-target="${stats.pending}">${stats.pending}</span>`);
    $('#inProgressReports').html(`<span class="counter-value" data-target="${stats.inProgress}">${stats.inProgress}</span>`);
    $('#resolvedReports').html(`<span class="counter-value" data-target="${stats.resolved}">${stats.resolved}</span>`);
}

function applyFilters() {
    const statusFilter = $('#filterStatus').val().toLowerCase();
    const typeFilter = $('#filterType').val().toLowerCase();
    const urgencyFilter = $('#filterUrgency').val().toLowerCase();
    const searchTerm = $('#searchInput').val().toLowerCase();

    filteredReports = allReports.filter(report => {
        const matchStatus = !statusFilter || report.status.toLowerCase().includes(statusFilter);
        const matchType = !typeFilter || report.report_type.toLowerCase().includes(typeFilter);
        const matchUrgency = !urgencyFilter || report.urgency.toLowerCase().includes(urgencyFilter);
        const matchSearch = !searchTerm ||
            report.report_number.toLowerCase().includes(searchTerm) ||
            report.reporter_name.toLowerCase().includes(searchTerm) ||
            report.reporter_email.toLowerCase().includes(searchTerm) ||
            report.location.toLowerCase().includes(searchTerm);

        return matchStatus && matchType && matchUrgency && matchSearch;
    });

    renderTable(filteredReports);
    updateFilterInfo();
}

function updateFilterInfo() {
    const total = allReports.length;
    const filtered = filteredReports.length;

    if (filtered === total) {
        $('#filterInfo').html(`Menampilkan semua <strong>${total}</strong> laporan`);
    } else {
        $('#filterInfo').html(`Menampilkan <strong>${filtered}</strong> dari <strong>${total}</strong> laporan`);
    }
}

function resetFilters() {
    $('#filterStatus').val('');
    $('#filterType').val('');
    $('#filterUrgency').val('');
    $('#searchInput').val('');
    applyFilters();
}

function refreshData() {
    $('#tableBody').html(`
        <tr>
            <td colspan="10" class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Memuat ulang data...</p>
            </td>
        </tr>
    `);
    loadGridData();
}

// ✅ IMPROVED: Working export with filters
function exportData(format) {
    const statusFilter = $('#filterStatus').val();
    const typeFilter = $('#filterType').val();
    const urgencyFilter = $('#filterUrgency').val();

    let url = `/admin/public-reports/export/${format}?`;
    const params = [];

    if (statusFilter) params.push(`status=${statusFilter}`);
    if (typeFilter) params.push(`report_type=${typeFilter}`);
    if (urgencyFilter) params.push(`urgency_level=${urgencyFilter}`);

    url += params.join('&');

    Swal.fire({
        title: `Export ${format.toUpperCase()}`,
        html: `
            <p>Export <strong>${filteredReports.length}</strong> laporan ke format <strong>${format.toUpperCase()}</strong>?</p>
            ${params.length > 0 ? '<small class="text-muted">Filter aktif akan diterapkan</small>' : ''}
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#009966',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Export',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang membuat file export',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Trigger download
                window.location.href = url;

                // Show success after delay
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Export Berhasil!',
                        text: 'File sedang diunduh',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    resolve();
                }, 1000);
            });
        }
    });
}

// Verify report
$(document).on('click', '.verify-btn', function() {
    const id = $(this).data('id');

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
                url: `/admin/public-reports/${id}/verify`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#28a745',
                            timer: 2000
                        });
                        loadGridData();
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat memverifikasi laporan',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
});

// Delete report
$(document).on('click', '.delete-report', function() {
    const id = $(this).data('id');

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
                url: `/admin/public-reports/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#009966',
                            timer: 2000
                        });
                        loadGridData();
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
});
</script>
@endsection
