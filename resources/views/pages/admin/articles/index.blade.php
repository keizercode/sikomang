@extends('layouts.admin.master')

@section('css')
<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .toggle-featured {
        cursor: pointer;
    }

    .toggle-featured input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $title }}</h4>
                    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Artikel
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.articles.index') }}" class="row g-3" id="filterForm">
                    <div class="col-md-4">
                        <label class="form-label">Pencarian</label>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Cari judul atau konten..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
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
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-info w-100" onclick="toggleAdvancedFilter()">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                    </div>
                </form>

                <!-- Advanced Filter (Hidden by default) -->
                <div id="advancedFilter" style="display: none;" class="mt-3 pt-3 border-top">
                    <form method="GET" action="{{ route('admin.articles.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Featured</label>
                            <select name="is_featured" class="form-control">
                                <option value="">Semua</option>
                                <option value="1" {{ request('is_featured') == '1' ? 'selected' : '' }}>Featured</option>
                                <option value="0" {{ request('is_featured') == '0' ? 'selected' : '' }}>Non-Featured</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Articles Table Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if($articles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Judul</th>
                                    <th width="150">Penulis</th>
                                    <th width="120">Status</th>
                                    <th width="100" class="text-center">Featured</th>
                                    <th width="80" class="text-center">Views</th>
                                    <th width="150">Tanggal</th>
                                    <th width="180" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($articles as $index => $article)
                                    <tr>
                                        <td>{{ $articles->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ Str::limit($article->title, 50) }}</strong>
                                            @if($article->is_featured)
                                                <span class="badge bg-warning text-dark ms-1">
                                                    <i class="fas fa-star"></i> Featured
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $article->user->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($article->status == 'published')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Published
                                                </span>
                                            @elseif($article->status == 'draft')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-file"></i> Draft
                                                </span>
                                            @else
                                                <span class="badge bg-dark">
                                                    <i class="fas fa-archive"></i> Archived
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center toggle-featured">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       data-id="{{ $article->id }}"
                                                       {{ $article->is_featured ? 'checked' : '' }}
                                                       title="Toggle Featured">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">
                                                <i class="fas fa-eye"></i> {{ number_format($article->views) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="far fa-calendar"></i> {{ $article->created_at->format('d/m/Y') }}
                                                <br>
                                                <i class="far fa-clock"></i> {{ $article->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="action-buttons d-flex gap-1 justify-content-center">
                                                <a href="{{ route('admin.articles.show', $article) }}"
                                                   class="btn btn-sm btn-info"
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.articles.edit', $article) }}"
                                                   class="btn btn-sm btn-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if($article->status == 'draft')
                                                    <form action="{{ route('admin.articles.publish', $article) }}"
                                                          method="POST"
                                                          class="d-inline publish-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-success"
                                                                title="Publish">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.articles.destroy', $article) }}"
                                                      method="POST"
                                                      class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            title="Hapus">
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
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">
                                Menampilkan {{ $articles->firstItem() }} - {{ $articles->lastItem() }}
                                dari {{ $articles->total() }} artikel
                            </p>
                        </div>
                        <div>
                            {{ $articles->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">
                            @if(request()->has('search') || request()->has('status'))
                                Tidak ada artikel yang sesuai dengan filter
                            @else
                                Belum ada artikel
                            @endif
                        </h5>
                        <p class="text-muted mb-3">
                            @if(request()->has('search') || request()->has('status'))
                                Coba ubah filter atau pencarian Anda
                            @else
                                Mulai dengan membuat artikel baru
                            @endif
                        </p>
                        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Artikel Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Toggle Featured Status
    $('.toggle-featured input[type="checkbox"]').on('change', function() {
        const articleId = $(this).data('id');
        const checkbox = $(this);
        const isChecked = checkbox.is(':checked');

        $.ajax({
            url: `/admin/articles/${articleId}/toggle-featured`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alertify.success(response.message || 'Status featured berhasil diubah');

                    // Update badge in title column
                    const row = checkbox.closest('tr');
                    const titleCell = row.find('td:eq(1)');
                    const existingBadge = titleCell.find('.badge');

                    if (isChecked) {
                        if (existingBadge.length === 0) {
                            titleCell.find('strong').after(' <span class="badge bg-warning text-dark ms-1"><i class="fas fa-star"></i> Featured</span>');
                        }
                    } else {
                        existingBadge.remove();
                    }
                } else {
                    alertify.error('Gagal mengubah status featured');
                    checkbox.prop('checked', !isChecked);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alertify.error('Terjadi kesalahan saat mengubah status');
                checkbox.prop('checked', !isChecked);
            }
        });
    });

    // Delete Confirmation
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Hapus Artikel?',
            text: "Artikel yang dihapus tidak dapat dikembalikan!",
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

    // Publish Confirmation
    $('.publish-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Publish Artikel?',
            text: "Artikel akan dipublikasikan dan dapat dilihat publik",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Publish!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Auto-submit on status change
    $('select[name="status"]').on('change', function() {
        $('#filterForm').submit();
    });
});

// Toggle Advanced Filter
function toggleAdvancedFilter() {
    const advFilter = $('#advancedFilter');
    const icon = event.target.tagName === 'I' ? $(event.target) : $(event.target).find('i');

    if (advFilter.is(':visible')) {
        advFilter.slideUp();
        icon.removeClass('fa-chevron-up').addClass('fa-sliders-h');
    } else {
        advFilter.slideDown();
        icon.removeClass('fa-sliders-h').addClass('fa-chevron-up');
    }
}
</script>
@endsection
