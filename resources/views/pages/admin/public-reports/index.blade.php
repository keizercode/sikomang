@extends('layouts.admin.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Total Laporan</p>
                                        <h4 class="mb-0" id="totalReports">0</h4>
                                    </div>
                                    <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="mdi mdi-file-document font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Menunggu Verifikasi</p>
                                        <h4 class="mb-0 text-warning" id="pendingReports">0</h4>
                                    </div>
                                    <div class="avatar-sm rounded-circle bg-warning align-self-center mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-warning">
                                            <i class="mdi mdi-clock-outline font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Sedang Ditangani</p>
                                        <h4 class="mb-0 text-info" id="inProgressReports">0</h4>
                                    </div>
                                    <div class="avatar-sm rounded-circle bg-info align-self-center mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-info">
                                            <i class="mdi mdi-progress-check font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Selesai</p>
                                        <h4 class="mb-0 text-success" id="resolvedReports">0</h4>
                                    </div>
                                    <div class="avatar-sm rounded-circle bg-success align-self-center mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-success">
                                            <i class="mdi mdi-check-circle font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Table -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Daftar Laporan Masyarakat</h4>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="grid-data">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>No. Laporan</th>
                                        <th>Lokasi</th>
                                        <th>Pelapor</th>
                                        <th>Email</th>
                                        <th>Jenis</th>
                                        <th>Urgensi</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    loadGridData();
});

function loadGridData() {
    $.ajax({
        url: '{{ route("admin.public-reports.grid") }}',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var tbody = $('#grid-data tbody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>');
                return;
            }

            // Update statistics
            updateStatistics(data);

            $.each(data, function(index, item) {
                var row = '<tr>' +
                    '<td>' + item.no + '</td>' +
                    '<td><strong>' + item.report_number + '</strong></td>' +
                    '<td>' + item.location + '</td>' +
                    '<td>' + item.reporter_name + '</td>' +
                    '<td>' + item.reporter_email + '</td>' +
                    '<td>' + item.report_type + '</td>' +
                    '<td>' + item.urgency + '</td>' +
                    '<td>' + item.status + '</td>' +
                    '<td>' + item.created_at + '</td>' +
                    '<td>' + item.action + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            alertify.error('Gagal memuat data');
        }
    });
}

function updateStatistics(data) {
    const total = data.length;
    const pending = data.filter(r => r.status.includes('Menunggu')).length;
    const inProgress = data.filter(r => r.status.includes('Ditangani')).length;
    const resolved = data.filter(r => r.status.includes('Selesai')).length;

    $('#totalReports').text(total);
    $('#pendingReports').text(pending);
    $('#inProgressReports').text(inProgress);
    $('#resolvedReports').text(resolved);
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
                        alertify.success(response.message);
                        loadGridData();
                    }
                },
                error: function() {
                    alertify.error('Gagal memverifikasi laporan');
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
                        alertify.success(response.message);
                        loadGridData();
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus laporan');
                }
            });
        }
    });
});
</script>
@endsection
