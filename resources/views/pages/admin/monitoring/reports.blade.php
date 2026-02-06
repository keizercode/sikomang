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
                        <!-- Summary Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="text-white">Total Lokasi</h6>
                                        <h3 class="text-white mb-0">{{ $locations->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="text-white">Total Luas</h6>
                                        <h3 class="text-white mb-0">{{ number_format($locations->sum('area'), 2) }} ha</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6 class="text-white">Kesehatan Rata-rata</h6>
                                        <h3 class="text-white mb-0">{{ number_format($locations->avg('health_percentage'), 1) }}%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h6 class="text-white">Total Kerusakan</h6>
                                        <h3 class="text-white mb-0">{{ $locations->sum(function($loc) { return $loc->damages->count(); }) }}</h3>
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
                                Laporan digenerate pada: {{ date('d F Y H:i') }}
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
</style>
@endsection
