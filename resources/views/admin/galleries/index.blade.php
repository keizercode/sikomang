@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                <i class="fas fa-upload"></i> Upload Bulk
            </button>
            <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Foto
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.galleries.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari foto..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="location_id" class="form-control">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="row">
        @if($galleries->count() > 0)
            @foreach($galleries as $gallery)
                <div class="col-md-3 mb-4">
                    <div class="card shadow h-100">
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $gallery->image_path) }}"
                                 class="card-img-top"
                                 alt="{{ $gallery->title }}"
                                 style="height: 200px; object-fit: cover;">

                            <!-- Badges -->
                            <div class="position-absolute top-0 end-0 m-2">
                                @if($gallery->is_featured)
                                    <span class="badge bg-warning">Featured</span>
                                @endif
                                @if(!$gallery->is_active)
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($gallery->title, 40) }}</h6>
                            <p class="card-text text-muted small">
                                <i class="fas fa-tag"></i> {{ ucfirst($gallery->category) }}<br>
                                @if($gallery->location)
                                    <i class="fas fa-map-marker-alt"></i> {{ Str::limit($gallery->location->name, 30) }}<br>
                                @endif
                                <i class="fas fa-calendar"></i> {{ $gallery->created_at->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('admin.galleries.show', $gallery) }}"
                                   class="btn btn-sm btn-info" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.galleries.edit', $gallery) }}"
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-{{ $gallery->is_featured ? 'success' : 'secondary' }} toggle-featured"
                                        data-id="{{ $gallery->id }}"
                                        title="Toggle Featured">
                                    <i class="fas fa-star"></i>
                                </button>
                                <form action="{{ route('admin.galleries.destroy', $gallery) }}"
                                      method="POST"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $galleries->links() }}
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada foto di galeri. <a href="{{ route('admin.galleries.create') }}">Tambah foto baru</a></p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.galleries.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Multiple Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto (Multiple)</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 5MB per file.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-control" required>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi (Opsional)</label>
                        <select name="location_id" class="form-control">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle featured status
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
            } else {
                button.removeClass('btn-success').addClass('btn-secondary');
            }
        },
        error: function() {
            alertify.error('Gagal mengubah status featured');
        }
    });
});

// Delete confirmation
$('.delete-form').on('submit', function(e) {
    e.preventDefault();
    const form = this;

    Swal.fire({
        title: 'Hapus Foto?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
@endpush
@endsection
