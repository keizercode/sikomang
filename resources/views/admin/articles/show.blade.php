@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $title }}</h5>
                    <div>
                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Metadata -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Penulis:</th>
                                    <td>{{ $article->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($article->status == 'published')
                                            <span class="badge bg-success">Published</span>
                                        @elseif($article->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @else
                                            <span class="badge bg-dark">Archived</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Featured:</th>
                                    <td>
                                        @if($article->is_featured)
                                            <span class="badge bg-warning">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Views:</th>
                                    <td>{{ number_format($article->views) }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat:</th>
                                    <td>{{ $article->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Dipublish:</th>
                                    <td>{{ $article->published_at ? $article->published_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Featured Image -->
                    @if($article->featured_image)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Gambar Utama:</h6>
                        <img src="{{ asset('storage/' . $article->featured_image) }}"
                             alt="{{ $article->title }}"
                             class="img-fluid rounded"
                             style="max-height: 400px;">
                    </div>
                    @endif

                    <!-- Title -->
                    <h2 class="mb-3">{{ $article->title }}</h2>

                    <!-- Excerpt -->
                    @if($article->excerpt)
                    <div class="alert alert-light mb-4">
                        <strong>Ringkasan:</strong> {{ $article->excerpt }}
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="article-content mb-4">
                        {!! $article->content !!}
                    </div>

                    <hr>

                    <!-- SEO Meta -->
                    <div class="mt-4">
                        <h5 class="mb-3">SEO Meta Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Meta Title:</th>
                                <td>{{ $article->meta_title ?? $article->title }}</td>
                            </tr>
                            <tr>
                                <th>Meta Description:</th>
                                <td>{{ $article->meta_description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Meta Keywords:</th>
                                <td>{{ $article->meta_keywords ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td>
                                    <code>{{ $article->slug }}</code>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.article-content {
    line-height: 1.8;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    margin: 1rem 0;
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4,
.article-content h5,
.article-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.article-content p {
    margin-bottom: 1rem;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}
</style>
@endpush
