@extends('layouts.admin.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Data Kerusakan Mangrove</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Lokasi</th>
                                        <th>Judul Kerusakan</th>
                                        <th>Deskripsi</th>
                                        <th>Prioritas</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($damages as $index => $damage)
                                    <tr>
                                        <td>{{ $damages->firstItem() + $index }}</td>
                                        <td>{{ $damage->location->name ?? 'N/A' }}</td>
                                        <td>{{ $damage->title }}</td>
                                        <td>{{ Str::limit($damage->description, 50) }}</td>
                                        <td>
                                            <span class="badge badge-soft-{{ $damage->priority == 'high' ? 'danger' : ($damage->priority == 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($damage->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-{{ $damage->status == 'resolved' ? 'success' : ($damage->status == 'in_progress' ? 'warning' : 'danger') }}">
                                                {{ ucfirst(str_replace('_', ' ', $damage->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ dateTime($damage->created_at, 'd M Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.monitoring.edit', encode_id($damage->mangrove_location_id)) }}"
                                                   class="btn btn-sm btn-primary" title="Lihat Lokasi">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-success" title="Update Status">
                                                    <i class="mdi mdi-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data kerusakan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $damages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
