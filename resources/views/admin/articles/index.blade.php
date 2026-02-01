@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Artikel
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.articles.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari artikel..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
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
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="card shadow">
        <div class="card-body">
            @if($articles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th width="120">Status</th>
                                <th width="100">Featured</th>
                                <th width="100">Views</th>
                                <th width="150">Tanggal</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($articles as $index => $article)
                                <tr>
                                    <td>{{ $articles->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $article->title }}</strong>
                                        @if($article->is_featured)
                                            <span class="badge bg-warning text-dark ms-1">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ $article->user->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($article->status == 'published')
                                            <span class="badge bg-success">Published</span>
                                        @elseif($article->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @else
                                            <span class="badge bg-dark">Archived</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-featured"
                                                   type="checkbox"
                                                   data-id="{{ $article->id }}"
                                                   {{ $article->is_featured ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>{{ number_format($article->views) }}</td>
                                    <td>{{ $article->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.articles.show', $article) }}"
                                               class="btn btn-sm btn-info" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.articles.edit', $article) }}"
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($article->status == 'draft')
                                                <form action="{{ route('admin.articles.publish', $article) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Publish">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.articles.destroy', $article) }}"
                                                  method="POST"
                                                  class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada artikel. <a href="{{ route('admin.articles.create') }}">Tambah artikel baru</a></p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle featured status
$('.toggle-featured').on('change', function() {
    const articleId = $(this).data('id');
    const isChecked = $(this).is(':checked');

    $.ajax({
        url: `/admin/articles/${articleId}/toggle-featured`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            alertify.success(response.message);
        },
        error: function() {
            alertify.error('Gagal mengubah status featured');
            // Revert checkbox
            $(this).prop('checked', !isChecked);
        }
    });
});

// Delete confirmation
$('.delete-form').on('submit', function(e) {
    e.preventDefault();
    const form = this;

    Swal.fire({
        title: 'Hapus Artikel?',
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
