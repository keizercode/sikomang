@extends('layouts.admin.master')

@section('css')
<!-- TinyMCE CSS -->
<style>
    .tox-tinymce {
        border-radius: 8px !important;
        border: 2px solid #e5e7eb !important;
    }

    .preview-section {
        display: none;
        margin-top: 20px;
        padding: 20px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .preview-section.show {
        display: block;
    }

    .preview-content {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 0 auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $title }}</h5>
                    <button type="button" class="btn btn-info btn-sm" onclick="togglePreview()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                </div>
                <form action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      id="articleForm">
                    @csrf
                    @if(isset($article))
                        @method('PUT')
                    @endif

                    <div class="card-body">
                        <div class="row">
                            <!-- Title -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="articleTitle"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $article->title ?? '') }}"
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
                                          rows="3">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                                <small class="text-muted">Opsional. Ringkasan singkat artikel (maks 500 karakter)</small>
                                @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Content Editor -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Konten Artikel <span class="text-danger">*</span></label>
                                <textarea name="content"
                                          id="articleContent"
                                          class="form-control @error('content') is-invalid @enderror"
                                          required>{{ old('content', $article->content ?? '') }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Gunakan toolbar untuk memformat teks seperti di Microsoft Word
                                </small>
                                @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Featured Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Utama</label>
                                @if(isset($article) && $article->featured_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $article->featured_image) }}"
                                         alt="Current featured image"
                                         class="img-thumbnail"
                                         id="preview-image"
                                         style="max-height: 200px;">
                                </div>
                                @else
                                <div class="mb-2" id="preview-image-container" style="display: none;">
                                    <img src="" alt="Preview" class="img-thumbnail" id="preview-image" style="max-height: 200px;">
                                </div>
                                @endif
                                <input type="file"
                                       name="featured_image"
                                       class="form-control @error('featured_image') is-invalid @enderror"
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maks: 2MB</small>
                                @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="article-status" class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status }}"
                                            {{ old('status', $article->status ?? 'draft') == $status ? 'selected' : '' }}>
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
                                       value="{{ old('published_at', isset($article->published_at) ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
                                <small class="text-muted">Kosongkan untuk otomatis saat publish</small>
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
                                           {{ old('is_featured', $article->is_featured ?? false) ? 'checked' : '' }}>
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
                                       value="{{ old('meta_title', $article->meta_title ?? '') }}">
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
                                       value="{{ old('meta_keywords', $article->meta_keywords ?? '') }}">
                                <small class="text-muted">Pisahkan dengan koma</small>
                                @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description"
                                          class="form-control @error('meta_description') is-invalid @enderror"
                                          rows="3">{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
                                <small class="text-muted">Opsional. Deskripsi untuk mesin pencari (maks 160 karakter)</small>
                                @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="preview-section" id="previewSection">
                            <h5 class="mb-3">Preview Artikel</h5>
                            <div class="preview-content">
                                <h1 id="preview-title" class="mb-3"></h1>
                                <div id="preview-meta" class="text-muted mb-4"></div>
                                <div id="preview-body" class="article-content"></div>
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
                                    <i class="fas fa-paper-plane"></i> {{ isset($article) ? 'Update' : 'Publish' }}
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

@section('js')
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
$(document).ready(function() {
    // Initialize TinyMCE with Word-like features
    tinymce.init({
        selector: '#articleContent',
        height: 600,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'paste',
            'autosave', 'emoticons', 'codesample', 'quickbars', 'pagebreak'
        ],
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                 'forecolor backcolor | alignleft aligncenter alignright alignjustify | ' +
                 'bullist numlist outdent indent | removeformat | link image media table | ' +
                 'code fullscreen preview | help',

        // Style untuk content
        content_style: `
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                font-size: 16px;
                line-height: 1.8;
                color: #374151;
                padding: 20px;
            }
            p {
                margin-bottom: 1.5rem;
                text-align: justify;
            }
            h1, h2, h3, h4, h5, h6 {
                margin-top: 2rem;
                margin-bottom: 1rem;
                font-weight: 700;
                color: #1f2937;
                line-height: 1.3;
            }
            h1 { font-size: 2.25rem; }
            h2 { font-size: 1.875rem; }
            h3 { font-size: 1.5rem; }
            h4 { font-size: 1.25rem; }
            ul, ol {
                margin-bottom: 1.5rem;
                padding-left: 2rem;
            }
            li {
                margin-bottom: 0.5rem;
            }
            blockquote {
                border-left: 4px solid #009966;
                padding-left: 1.5rem;
                margin: 1.5rem 0;
                font-style: italic;
                color: #4b5563;
            }
            img {
                max-width: 100%;
                height: auto;
                border-radius: 0.75rem;
                margin: 2rem 0;
            }
            a {
                color: #009966;
                text-decoration: underline;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 1.5rem 0;
            }
            table th, table td {
                border: 1px solid #e5e7eb;
                padding: 0.75rem;
                text-align: left;
            }
            table th {
                background-color: #f3f4f6;
                font-weight: 600;
            }
        `,

        // Format blocks
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Preformatted=pre',

        // Paste options
        paste_as_text: false,
        paste_data_images: true,
        paste_retain_style_properties: 'all',

        // Image upload (if you want to enable it)
        images_upload_handler: function (blobInfo, success, failure) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', '{{ csrf_token() }}');

            // You can implement image upload endpoint
            // For now, we'll use base64
            var reader = new FileReader();
            reader.onload = function() {
                success(reader.result);
            };
            reader.readAsDataURL(blobInfo.blob());
        },

        // Auto-save
        autosave_interval: '30s',
        autosave_retention: '30m',

        // Mobile responsive
        mobile: {
            menubar: true,
            toolbar_mode: 'sliding'
        },

        // Setup callback
        setup: function(editor) {
            editor.on('change keyup', function() {
                updatePreview();
            });
        }
    });

    // Handle form submit with action button
    $('button[name="action"]').on('click', function() {
        let action = $(this).val();
        if (action === 'draft') {
            $('#article-status').val('draft');
        } else if (action === 'publish') {
            $('#article-status').val('published');
        }
    });
});

// Preview image before upload
function previewImage(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-image').attr('src', e.target.result);
            $('#preview-image-container').show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle preview
function togglePreview() {
    $('#previewSection').toggleClass('show');
    if ($('#previewSection').hasClass('show')) {
        updatePreview();
    }
}

// Update preview content
function updatePreview() {
    let title = $('#articleTitle').val();
    let content = tinymce.get('articleContent').getContent();

    $('#preview-title').text(title || 'Judul Artikel');
    $('#preview-body').html(content);

    let now = new Date();
    let dateStr = now.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    $('#preview-meta').text('Dipublikasikan pada ' + dateStr);
}
</script>

<style>
/* Article content preview styling */
.article-content {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #374151;
}

.article-content p {
    margin-bottom: 1.5rem;
    text-align: justify;
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4,
.article-content h5,
.article-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.3;
}

.article-content h1 { font-size: 2.25rem; }
.article-content h2 { font-size: 1.875rem; }
.article-content h3 { font-size: 1.5rem; }
.article-content h4 { font-size: 1.25rem; }

.article-content ul,
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content blockquote {
    border-left: 4px solid #009966;
    padding-left: 1.5rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #4b5563;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.75rem;
    margin: 2rem 0;
}

.article-content a {
    color: #009966;
    text-decoration: underline;
}
</style>
@endsection
