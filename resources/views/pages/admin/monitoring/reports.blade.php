@extends('layouts.admin.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Laporan Monitoring Mangrove</h5>
                        <button class="btn btn-outline-success" onclick="window.print()">
                            <i class="mdi mdi-printer"></i> Print Laporan
                        </button>
                    </div>
                    <div class="card-body">
                       <!-- Summary Statistics Modern -->
<div class="row mb-4 g-3">

    <!-- Total Lokasi -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="mdi mdi-map-marker-outline fs-3"></i>
                    </div>
                </div>
                <div class="ms-3">
                    <p class="text-muted mb-1">Total Lokasi</p>
                    <h4 class="mb-0 fw-bold">{{ $locations->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Luas -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-success bg-opacity-10 text-success">
                        <i class="mdi mdi-vector-square fs-3"></i>
                    </div>
                </div>
                <div class="ms-3">
                    <p class="text-muted mb-1">Total Luas</p>
                    <h4 class="mb-0 fw-bold">{{ number_format($locations->sum('area'), 2) }} ha</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Kesehatan -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="mdi mdi-heart-pulse fs-3"></i>
                    </div>
                </div>
                <div class="ms-3">
                    <p class="text-muted mb-1">Kesehatan Rata-rata</p>
                    <h4 class="mb-0 fw-bold">
                        {{ number_format($locations->avg('health_percentage'), 1) }}%
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Kerusakan -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                        <i class="mdi mdi-alert-circle-outline fs-3"></i>
                    </div>
                </div>
                <div class="ms-3">
                    <p class="text-muted mb-1">Total Kerusakan</p>
                    <h4 class="mb-0 fw-bold">
                        {{ $locations->sum(function($loc) { return $loc->damages->count(); }) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

</div>


                        <!-- Detailed Report Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lokasi</th>
                                        <th>Wilayah</th>
                                        <th>Luas (ha)</th>
                                        <th>Kerapatan</th>
                                        <th>Kesehatan</th>
                                        <th>Tipe</th>
                                        <th>Kerusakan</th>
                                        <th>Gambar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $index => $location)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $location->name }}</td>
                                        <td>{{ $location->region ?? '-' }}</td>
                                        <td>{{ number_format($location->area, 2) }}</td>
                                        <td>{{ ucfirst($location->density) }}</td>
                                        <td>{{ $location->health_percentage ? $location->health_percentage . '%' : 'N/A' }}</td>
                                        <td>{{ ucfirst($location->type) }}</td>
                                        <td>{{ $location->damages->count() }}</td>
                                        <td>{{ $location->images->count() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Report Footer -->
                        <div class="mt-4 text-end">

                            <p class="text-muted mb-0">
                            Laporan digenerate pada: {{ now()->format('d F Y H:i') }}
                            </p>
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
    @media print {
        .navbar-header,
        .vertical-menu,
        .btn,
        .page-title-box {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            margin-top: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
    .icon-circle {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08) !important;
}

</style>
@endsection
