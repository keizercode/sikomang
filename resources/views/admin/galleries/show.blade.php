@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $title }}</h5>
                    <div>
                        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Image -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $gallery->image_path) }}"
                                     alt="{{ $gallery->title }}"
                                     class="img-fluid rounded shadow-sm">
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="col-md-4">
                            <h4 class="mb-3">{{ $gallery->title }}</h4>

                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="120">Kategori:</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $gallery->category_label }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($gallery->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Featured:</th>
                                    <td>
                                        @if($gallery->is_featured)
                                            <span class="badge bg-warning">Ya</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lokasi:</th>
                                    <td>{{ $gallery->location ? $gallery->location->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Fotografer:</th>
                                    <td>{{ $gallery->photographer ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal:</th>
                                    <td>{{ $gallery->date_taken ? $gallery->date_taken->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Urutan:</th>
                                    <td>{{ $gallery->order }}</td>
                                </tr>
                                <tr>
                                    <th>Diupload:</th>
                                    <td>{{ $gallery->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diupload oleh:</th>
                                    <td>{{ $gallery->user->name ?? 'N/A' }}</td>
                                </tr>
                            </table>

                            @if($gallery->description)
                            <div class="mt-3">
                                <h6>Deskripsi:</h6>
                                <p class="text-muted">{{ $gallery->description }}</p>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="mt-4">
                                <form action="{{ route('admin.galleries.destroy', $gallery) }}"
                                      method="POST"
                                      class="delete-form"
                                      onsubmit="return confirm('Yakin ingin menghapus foto ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-{{ $gallery->is_featured ? 'success' : 'secondary' }} btn-sm w-100 mb-2 toggle-featured"
                                            data-id="{{ $gallery->id }}">
                                        <i class="fas fa-star"></i>
                                        {{ $gallery->is_featured ? 'Unggulan' : 'Jadikan Unggulan' }}
                                    </button>
                                    <button type="button"
                                            class="btn btn-{{ $gallery->is_active ? 'info' : 'warning' }} btn-sm w-100 mb-2 toggle-active"
                                            data-id="{{ $gallery->id }}">
                                        <i class="fas fa-eye"></i>
                                        {{ $gallery->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </button>
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-trash"></i> Hapus Foto
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle featured
$('.toggle-featured').on('click', function() {
    const galleryId = $(this).data('id');
    const button = $(this);

    $.ajax({
        url: `/admin/galleries/${galleryId}/toggle-featured`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            alertify.success(response.message);
            if (response.is_featured) {
                button.removeClass('btn-secondary').addClass('btn-success');
                button.html('<i class="fas fa-star"></i> Unggulan');
            } else {
                button.removeClass('btn-success').addClass('btn-secondary');
                button.html('<i class="fas fa-star"></i> Jadikan Unggulan');
            }
        },
        error: function() {
            alertify.error('Gagal mengubah status featured');
        }
    });
});

// Toggle active
$('.toggle-active').on('click', function() {
    const galleryId = $(this).data('id');
    const button = $(this);

    $.ajax({
        url: `/admin/galleries/${galleryId}/toggle-active`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            alertify.success(response.message);
            if (response.is_active) {
                button.removeClass('btn-warning').addClass('btn-info');
                button.html('<i class="fas fa-eye"></i> Aktif');
            } else {
                button.removeClass('btn-info').addClass('btn-warning');
                button.html('<i class="fas fa-eye"></i> Tidak Aktif');
            }
        },
        error: function() {
            alertify.error('Gagal mengubah status aktif');
        }
    });
});
</script>
@endpush
