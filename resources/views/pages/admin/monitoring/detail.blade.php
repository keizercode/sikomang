@extends('layouts.admin.master')
@section('css')
    @vite(['resources/css/admin/detail.css'])
@endsection
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $location->name }}</h4>
                        <div class="mt-2">
                            <span class="badge badge-outline-blue" style="padding: 0.25rem 0.6rem;">{{ ucfirst($location->type) }}</span>
                            <span class="badge badge-outline-secondary" style="padding: 0.25rem 0.6rem;">{{ ucfirst($location->density) }}</span>
                            @if($location->is_active)
                                <span class="badge badge-outline-primary" style="padding: 0.25rem 0.6rem;">Aktif</span>
                            @else
                                <span class="badge badge-outline-secondary">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.monitoring.edit', $keyId) }}" class="btn btn-primary" style="background: #0d6efd;">
                            <i class="mdi mdi-pencil"></i> Edit Lokasi
                        </a>
                        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-secondary text-white" style="background:grey">
                            <i class="mdi mdi-arrow-left text-white"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#info" role="tab">
                    <i class="mdi mdi-information"></i> Informasi Dasar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#species" role="tab">
                    <i class="mdi mdi-leaf"></i> Spesies
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#activities" role="tab">
                    <i class="mdi mdi-run"></i> Aktivitas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#programs" role="tab">
                    <i class="mdi mdi-format-list-bulleted"></i> Program & Pemanfaatan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#images" role="tab">
                    <i class="mdi mdi-image-multiple"></i> Galeri Foto
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#damages" role="tab">
                    <i class="mdi mdi-alert"></i> Kerusakan
                    @if($location->damages->count() > 0)
                        <span class="badge bg-danger ms-1">{{ $location->damages->count() }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-3">
            <!-- Informasi Dasar -->
            <div class="tab-pane active" id="info" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="180">Nama Lokasi:</th>
                                        <td>{{ $location->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Koordinat:</th>
                                        <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                                    </tr>
                                    <tr>
                                        <th>Luas Area:</th>
                                        <td>{{ $location->area }} ha</td>
                                    </tr>
                                    <tr>
                                        <th>Kerapatan:</th>
                                        <td><span class="badge badge-outline-secondary" style="padding: 0.25rem 0.6rem;">{{ ucfirst($location->density) }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Tipe:</th>
                                        <td><span class="badge badge-outline-blue" style="padding: 0.25rem 0.6rem;">{{ ucfirst($location->type) }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="180">Kesehatan:</th>
                                        <td>
                                            @if($location->health_percentage)
                                                {{ $location->health_percentage }}%
                                                @if($location->health_score)
                                                    <br><small class="text-muted">{{ $location->health_score }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Pengelola:</th>
                                        <td>{{ $location->manager ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Penetapan:</th>
                                        <td>{{ $location->year_established ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Wilayah:</th>
                                        <td>{{ $location->region ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($location->is_active)
                                                <span class="badge badge-outline-primary" style="padding: 0.25rem 0.6rem;">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($location->description)
                        <div class="mt-3">
                            <h6>Deskripsi:</h6>
                            <p class="text-muted">{{ $location->description }}</p>
                        </div>
                        @endif

                        @if($location->location_address)
                        <div class="mt-3">
                            <h6>Alamat:</h6>
                            <p class="text-muted">{{ $location->location_address }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Spesies -->
            <div class="tab-pane" id="species" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Spesies (Vegetasi dan Fauna)</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#speciesModal">
                            <i class="mdi mdi-pencil"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        @if($location->details && $location->details->species_detail)
                            <div class="row">
                                @if(isset($location->details->species_detail['vegetasi']))
                                <div class="col-md-6">
                                    <h6 class="text-primary">Vegetasi:</h6>
                                    <ul class="list-unstyled">
                                        @foreach($location->details->species_detail['vegetasi'] as $item)
                                        <li><i class="mdi mdi-leaf text-success"></i> {{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @if(isset($location->details->species_detail['fauna']))
                                <div class="col-md-6">
                                    <h6 class="text-primary">Fauna:</h6>
                                    <ul class="list-unstyled">
                                        @foreach($location->details->species_detail['fauna'] as $item)
                                        <li><i class="mdi mdi-paw text-warning"></i> {{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        @else
                            <p class="text-muted">Belum ada data spesies. Klik tombol Edit untuk menambahkan.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Aktivitas -->
            <div class="tab-pane" id="activities" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Aktivitas Sekitar</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#activitiesModal">
                            <i class="mdi mdi-pencil"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        @if($location->details && $location->details->activities)
                            @if(isset($location->details->activities['description']))
                            <p class="text-muted">{{ $location->details->activities['description'] }}</p>
                            @endif

                            @if(isset($location->details->activities['items']) && count($location->details->activities['items']) > 0)
                            <ul>
                                @foreach($location->details->activities['items'] as $item)
                                <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            @endif
                        @else
                            <p class="text-muted">Belum ada data aktivitas.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Program & Pemanfaatan -->
            <div class="tab-pane" id="programs" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Program & Pemanfaatan</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#programsModal">
                            <i class="mdi mdi-pencil"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Pemanfaatan Hutan -->
                            <div class="col-md-4">
                                <h6 class="text-primary">Pemanfaatan Hutan:</h6>
                                @if($location->details && $location->details->forest_utilization)
                                <ul class="list-unstyled">
                                    @foreach($location->details->forest_utilization as $item)
                                    <li><i class="mdi mdi-checkbox-marked-circle text-success"></i> {{ $item }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-muted">-</p>
                                @endif
                            </div>

                            <!-- Program -->
                            <div class="col-md-4">
                                <h6 class="text-primary">Program yang Dilaksanakan:</h6>
                                @if($location->details && $location->details->programs)
                                <ul class="list-unstyled">
                                    @foreach($location->details->programs as $item)
                                    <li><i class="mdi mdi-checkbox-marked-circle text-info"></i> {{ $item }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-muted">-</p>
                                @endif
                            </div>

                            <!-- Stakeholders -->
                            <div class="col-md-4">
                                <h6 class="text-primary">Pihak Terkait:</h6>
                                @if($location->details && $location->details->stakeholders)
                                <ul class="list-unstyled">
                                    @foreach($location->details->stakeholders as $item)
                                    <li><i class="mdi mdi-domain text-warning"></i> {{ $item }}</li>
                                    @endforeach
                                </ul>
                                @else
                                <p class="text-muted">-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galeri Foto -->
            <div class="tab-pane" id="images" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Galeri Foto</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="mdi mdi-upload"></i> Upload Foto
                        </button>
                    </div>
                    <div class="card-body">
                        @if($location->images->count() > 0)
                        <div class="row">
                            @foreach($location->images as $image)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <img src="{{ $image->image_url }}" class="card-img-top" alt="{{ $image->caption }}" style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        @if($image->caption)
                                        <p class="card-text small">{{ $image->caption }}</p>
                                        @endif
                                        <button class="btn btn-sm btn-danger w-100 delete-image" data-id="{{ $image->id }}">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted text-center py-4">Belum ada foto. Klik Upload Foto untuk menambahkan.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kerusakan -->
            <div class="tab-pane" id="damages" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Laporan Kerusakan</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#damageModal">
                            <i class="mdi mdi-plus"></i> Tambah Laporan
                        </button>
                    </div>
                    <div class="card-body">
                        @if($location->damages->count() > 0)
                        <div class="accordion" id="damagesAccordion">


@section('js')
@parent

{{-- Inject damage data ke JavaScript --}}
<script>
window.editDamageData = {
    @foreach($location->damages as $damage)
    {{ $damage->id }}: {
        title: @json($damage->title),
        description: @json($damage->description),
        priority: '{{ $damage->priority }}',
        status: '{{ $damage->status }}'
    },
    @endforeach
};
</script>

{{-- Quick Fix JavaScript Functions --}}
<script>
// Reset modal ke mode tambah
function resetDamageModal() {
    $('#damageModalTitle').text('Tambah Laporan Kerusakan');
    $('#submitDamageBtn').text('Simpan');
    $('#damage_id').val('');
    $('#form_method').val('POST');
    $('#damageForm').attr('action', '{{ route("admin.monitoring.add-damage", $keyId) }}');
    $('#damage_title').val('');
    $('#damage_description').val('');
    $('#damage_priority').val('medium');
    $('#damage_status').val('pending');
}

// Edit damage function
function editDamage(damageId) {
    console.log('Editing damage ID:', damageId);

    const data = window.editDamageData[damageId];

    if (!data) {
        alertify.error('Data tidak ditemukan');
        console.error('Available damage IDs:', Object.keys(window.editDamageData));
        return;
    }

    // Set modal
    $('#damageModalTitle').text('Edit Laporan Kerusakan');
    $('#submitDamageBtn').text('Update');

    // Set form action
    const updateUrl = '/admin/monitoring/{{ $keyId }}/damages/' + damageId;
    $('#damageForm').attr('action', updateUrl);
    $('#form_method').val('PUT');
    $('#damage_id').val(damageId);

    // Fill form
    $('#damage_title').val(data.title);
    $('#damage_description').val(data.description);
    $('#damage_priority').val(data.priority);
    $('#damage_status').val(data.status);

    // Show modal
    $('#damageModal').modal('show');
}

// Reset modal saat ditutup
$('#damageModal').on('hidden.bs.modal', function() {
    resetDamageModal();
});

// Reset saat tombol tambah diklik
$('button[data-bs-target="#damageModal"]').on('click', function() {
    resetDamageModal();
});

// Log saat ready
$(document).ready(function() {
    console.log('✓ Damage edit feature loaded');
    console.log('✓ Available damages:', Object.keys(window.editDamageData).length);
});

// Delete damage function (tetap pakai yang ada)
$(document).on('click', '.delete-damage', function() {
    const damageId = $(this).data('id');
    const locationId = '{{ $keyId }}';

    Swal.fire({
        title: 'Hapus Laporan?',
        text: "Laporan kerusakan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/monitoring/${locationId}/damages/${damageId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus laporan');
                }
            });
        }
    });
});

// Delete image function (tetap pakai yang ada)
$(document).on('click', '.delete-image', function() {
    const imageId = $(this).data('id');
    const locationId = '{{ $keyId }}';

    Swal.fire({
        title: 'Hapus Foto?',
        text: "Foto akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/monitoring/${locationId}/images/${imageId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus foto');
                }
            });
        }
    });
});

// Dynamic field adders
function addField(containerId, fieldName, placeholder) {
    const container = $(`#${containerId}`);
    const html = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" name="${fieldName}[]" placeholder="${placeholder}">
            <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                <i class="mdi mdi-minus"></i>
            </button>
        </div>
    `;
    container.append(html);
}
</script>
@endsection

                            @foreach($location->damages as $index => $damage)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#damage{{ $damage->id }}">
                                        <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                            <span>{{ $damage->title }}</span>
                                            <div>
                                                <span class="badge bg-{{ $damage->priority == 'high' ? 'danger' : ($damage->priority == 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($damage->priority) }}
                                                </span>
                                                <span class="badge bg-{{ $damage->status == 'resolved' ? 'success' : ($damage->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $damage->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="damage{{ $damage->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#damagesAccordion">
                                    <div class="accordion-body">
                                        <p><strong>Deskripsi:</strong> {{ $damage->description }}</p>

                                        @if($damage->actions->count() > 0)
                                        <h6 class="mt-3">Aksi Penanganan:</h6>
                                        <ul>
                                            @foreach($damage->actions as $action)
                                            <li>
                                                {{ $action->action_description }}
                                                @if($action->action_date)
                                                <br><small class="text-muted">{{ dateTime($action->action_date, 'd M Y') }}</small>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif

                                        <div class="mt-3">
                                            <button type="button"
                                            class="btn btn-sm btn-primary"
                                            style="background: #0d6efd"
                                            onclick="editDamage({{ $damage->id }})">
                                            <i class="mdi mdi-pencil"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#actionModal{{ $damage->id }}">
                                                <i class="mdi mdi-plus"></i> Tambah Aksi
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-damage" data-id="{{ $damage->id }}">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted text-center py-4">Belum ada laporan kerusakan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('pages.admin.monitoring.modals.species', ['location' => $location, 'keyId' => $keyId])
@include('pages.admin.monitoring.modals.activities', ['location' => $location, 'keyId' => $keyId])
@include('pages.admin.monitoring.modals.programs', ['location' => $location, 'keyId' => $keyId])
@include('pages.admin.monitoring.modals.upload-images', ['location' => $location, 'keyId' => $keyId])
@include('pages.admin.monitoring.modals.damage', ['location' => $location, 'keyId' => $keyId])

<!-- Action Modals for each damage -->
@foreach($location->damages as $damage)
@include('pages.admin.monitoring.modals.action', ['damage' => $damage, 'keyId' => $keyId])
@endforeach

@endsection

@section('js')
<script>
// Delete image
$(document).on('click', '.delete-image', function() {
    const imageId = $(this).data('id');
    const locationId = '{{ $keyId }}';

    Swal.fire({
        title: 'Hapus Foto?',
        text: "Foto akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/monitoring/${locationId}/images/${imageId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus foto');
                }
            });
        }
    });
});

// Delete damage
$(document).on('click', '.delete-damage', function() {
    const damageId = $(this).data('id');
    const locationId = '{{ $keyId }}';

    Swal.fire({
        title: 'Hapus Laporan?',
        text: "Laporan kerusakan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/monitoring/${locationId}/damages/${damageId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus laporan');
                }
            });
        }
    });
});

// Dynamic field adders
function addField(containerId, fieldName, placeholder) {
    const container = $(`#${containerId}`);
    const index = container.find('input').length;
    const html = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" name="${fieldName}[]" placeholder="${placeholder}">
            <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                <i class="mdi mdi-minus"></i>
            </button>
        </div>
    `;
    container.append(html);
}
</script>
@endsection
