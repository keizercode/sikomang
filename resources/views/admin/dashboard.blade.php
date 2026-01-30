@extends('layouts.master')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar">
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
                    <div class="avatar">
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
                    <div class="avatar">
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
                    <div class="avatar">
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
                        <h5 class="card-title">Distribusi Kerapatan Mangrove</h5>
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
                <div class="mx-n3" data-simplebar style="max-height: 350px;">
                    @foreach($recent_activities as $activity)
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
                    @endforeach
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
                        <h5 class="card-title">Laporan Kerusakan Pending</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.monitoring.damages') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul</th>
                                <th>Lokasi</th>
                                <th>Prioritas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending_reports as $report)
                            <tr>
                                <td>{{ $report['title'] }}</td>
                                <td>{{ $report['site'] }}</td>
                                <td>
                                    <span class="badge badge-soft-{{ $report['priority'] == 'high' ? 'danger' : 'warning' }}">
                                        {{ ucfirst($report['priority']) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-success">Tindak Lanjut</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Density Distribution Chart
    var ctx = document.getElementById('densityChart').getContext('2d');
    var densityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jarang', 'Sedang', 'Lebat'],
            datasets: [{
                label: 'Jumlah Lokasi',
                data: [{{ $density_distribution['jarang'] }}, {{ $density_distribution['sedang'] }}, {{ $density_distribution['lebat'] }}],
                backgroundColor: [
                    'rgba(141, 211, 199, 0.6)',
                    'rgba(255, 255, 179, 0.6)',
                    'rgba(190, 186, 218, 0.6)'
                ],
                borderColor: [
                    'rgba(141, 211, 199, 1)',
                    'rgba(255, 255, 179, 1)',
                    'rgba(190, 186, 218, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endsection
