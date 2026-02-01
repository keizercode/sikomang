@extends('layouts.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        @if(isset($error))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-outline me-2"></i>{{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-title rounded bg-soft-primary">
                                    <i class="bx bx-map-pin font-size-24 mb-0 text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 font-size-15">Total Lokasi</h6>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h4 class="mb-0 font-size-22">{{ $total_sites }} <span class="text-muted font-size-14">Titik</span></h4>
                            <p class="text-muted mb-0">Kawasan Pemantauan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-title rounded bg-soft-success">
                                    <i class="bx bx-area font-size-24 mb-0 text-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 font-size-15">Total Luas</h6>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h4 class="mb-0 font-size-22">{{ $total_area }} <span class="text-muted font-size-14">ha</span></h4>
                            <p class="text-muted mb-0">Area Mangrove</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-title rounded bg-soft-danger">
                                    <i class="bx bx-error font-size-24 mb-0 text-danger"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 font-size-15">Kerusakan Aktif</h6>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h4 class="mb-0 font-size-22">{{ $active_damages }} <span class="text-muted font-size-14">Laporan</span></h4>
                            <p class="text-muted mb-0">Memerlukan Tindakan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0">
                                <div class="avatar-title rounded bg-soft-info">
                                    <i class="bx bx-calendar-check font-size-24 mb-0 text-info"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 font-size-15">Kegiatan Bulan Ini</h6>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h4 class="mb-0 font-size-22">{{ $this_month_activities }} <span class="text-muted font-size-14">Aktivitas</span></h4>
                            <p class="text-muted mb-0">Konservasi & Monitoring</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Density Distribution Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Distribusi Kerapatan Mangrove</h5>
                            </div>
                        </div>
                        <div>
                            <canvas id="densityChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Aktivitas Terbaru</h5>
                        <div class="mx-n3" style="max-height: 350px; overflow-y: auto;">
                            @forelse($recent_activities as $activity)
                            <div class="border-bottom px-3 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 font-size-14">{{ $activity['name'] }}</h6>
                                        <p class="text-muted mb-0 font-size-12">
                                            <i class="bx bx-map-pin"></i> {{ $activity['site'] }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <p class="text-muted mb-0 font-size-11">{{ $activity['date'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="bx bx-info-circle font-size-24 text-muted"></i>
                                <p class="text-muted mb-0">Belum ada aktivitas</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pending Reports -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Laporan Kerusakan Pending</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.monitoring.damages') }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-list-ul"></i> Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul</th>
                                        <th>Lokasi</th>
                                        <th>Prioritas</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pending_reports as $report)
                                    <tr>
                                        <td>{{ $report['title'] }}</td>
                                        <td>{{ $report['site'] }}</td>
                                        <td>
                                            <span class="badge badge-soft-{{ $report['priority'] == 'high' ? 'danger' : ($report['priority'] == 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($report['priority']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-{{ $report['status'] == 'pending' ? 'danger' : 'warning' }}">
                                                {{ ucfirst(str_replace('_', ' ', $report['status'])) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.monitoring.damages') }}" class="btn btn-sm btn-success">
                                                <i class="bx bx-show"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bx bx-check-circle font-size-24 text-success"></i>
                                            <p class="text-muted mb-0 mt-2">Tidak ada laporan kerusakan pending</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('css')
<style>
.bg-soft-primary { background-color: rgba(0, 153, 102, 0.1); }
.bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
.bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }

.text-primary { color: #009966 !important; }
.text-success { color: #198754 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #0dcaf0 !important; }
.text-warning { color: #ffc107 !important; }

.badge-soft-primary { background-color: rgba(0, 153, 102, 0.1); color: #009966; }
.badge-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
.badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
.badge-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
.badge-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #92400e; }

.avatar {
    width: 48px;
    height: 48px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.375rem;
    font-weight: 600;
}
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Density Distribution Chart
    var ctx = document.getElementById('densityChart');
    if (ctx) {
        ctx = ctx.getContext('2d');
        var densityChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jarang', 'Sedang', 'Lebat'],
                datasets: [{
                    label: 'Jumlah Lokasi',
                    data: [{{ $density_distribution['jarang'] }}, {{ $density_distribution['sedang'] }}, {{ $density_distribution['lebat'] }}],
                    backgroundColor: [
                        'rgba(141, 211, 199, 0.7)',
                        'rgba(255, 255, 179, 0.7)',
                        'rgba(190, 186, 218, 0.7)'
                    ],
                    borderColor: [
                        'rgba(141, 211, 199, 1)',
                        'rgba(255, 255, 179, 1)',
                        'rgba(190, 186, 218, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 13,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
