@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $title }}</h5>
                </div>
                <form action="{{ route('admin.articles.update', $article) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <!-- Title -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                                <input type="text" name="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $article->title) }}"
                                       required>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Excerpt -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Ringkasan/Excerpt</label>
                                <textarea name="excerpt"
                                          class="form-control @error('excerpt') is-invalid @enderror"
                                          rows="3">{{ old('excerpt', $article->excerpt) }}</textarea>
                                <small class="text-muted">Opsional. Ringkasan singkat artikel (maks 200 karakter)</small>
                                @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Content -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Konten Artikel <span class="text-danger">*</span></label>
                                <textarea name="content"
                                          id="articleContent"
                                          class="form-control @error('content') is-invalid @enderror"
                                          rows="15"
                                          required>{{ old('content', $article->content) }}</textarea>
                                @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Featured Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Utama</label>
                                @if($article->featured_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $article->featured_image) }}"
                                         alt="Current featured image"
                                         class="img-thumbnail"
                                         style="max-height: 200px;">
                                    <p class="text-muted small mt-1">Gambar saat ini</p>
                                </div>
                                @endif
                                <input type="file"
                                       name="featured_image"
                                       class="form-control @error('featured_image') is-invalid @enderror"
                                       accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maks: 2MB. Kosongkan jika tidak ingin mengganti.</small>
                                @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach(['draft', 'published', 'archived'] as $status)
                                    <option value="{{ $status }}"
                                            {{ old('status', $article->status) == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Published At -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tanggal Publish</label>
                                <input type="datetime-local"
                                       name="published_at"
                                       class="form-control @error('published_at') is-invalid @enderror"
                                       value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
                                <small class="text-muted">Kosongkan untuk otomatis</small>
                                @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Featured Checkbox -->
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="is_featured"
                                           class="form-check-input"
                                           id="is_featured"
                                           value="1"
                                           {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Jadikan Artikel Unggulan
                                    </label>
                                </div>
                            </div>

                            <!-- SEO Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="text-primary">SEO Meta</h5>
                                <hr>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text"
                                       name="meta_title"
                                       class="form-control @error('meta_title') is-invalid @enderror"
                                       value="{{ old('meta_title', $article->meta_title) }}">
                                <small class="text-muted">Opsional. Akan menggunakan judul artikel jika kosong</small>
                                @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text"
                                       name="meta_keywords"
                                       class="form-control @error('meta_keywords') is-invalid @enderror"
                                       value="{{ old('meta_keywords', $article->meta_keywords) }}">
                                <small class="text-muted">Pisahkan dengan koma</small>
                                @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description"
                                          class="form-control @error('meta_description') is-invalid @enderror"
                                          rows="3">{{ old('meta_description', $article->meta_description) }}</textarea>
                                <small class="text-muted">Opsional. Deskripsi untuk mesin pencari (maks 160 karakter)</small>
                                @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" name="action" value="draft" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Simpan sebagai Draft
                                </button>
                                <button type="submit" name="action" value="publish" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Update & Publish
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor
    CKEDITOR.replace('articleContent', {
        height: 400,
        filebrowserUploadUrl: "{{ route('admin.articles.update', $article) }}",
        filebrowserUploadMethod: 'form'
    });

    // Handle form submit with action button
    $('button[name="action"]').on('click', function() {
        let action = $(this).val();
        if (action === 'draft') {
            $('select[name="status"]').val('draft');
        } else if (action === 'publish') {
            $('select[name="status"]').val('published');
        }
    });

    // Preview image on change
    $('input[name="featured_image"]').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.img-thumbnail').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
