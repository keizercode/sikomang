@extends('layouts.admin.master')

@section('css')
<!-- TinyMCE Skin -->
<style>
    .tox-tinymce {
        border-radius: 8px !important;
        border: 2px solid #e5e7eb !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .tox-tinymce:focus-within {
        border-color: #009966 !important;
        box-shadow: 0 4px 12px rgba(0, 153, 102, 0.15) !important;
    }

    .preview-section {
        display: none;
        margin-top: 30px;
        padding: 30px;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .preview-section.show {
        display: block;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .preview-content {
        background: white;
        padding: 50px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        max-width: 900px;
        margin: 0 auto;
    }

    #editor-counter {
        padding: 8px 12px;
        background: #f9fafb;
        border-radius: 6px;
        font-size: 0.875rem;
        color: #6b7280;
        display: inline-block;
        margin-top: 10px;
    }

    #editor-counter i {
        color: #009966;
        margin-right: 4px;
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
                    <div>
                        <button type="button" class="btn btn-info btn-sm" onclick="togglePreview()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <span class="badge bg-secondary ms-2">
                            <i class="fas fa-keyboard"></i> Ctrl+S: Save | Ctrl+P: Preview
                        </span>
                    </div>
                </div>
                <form action="{{ route('admin.articles.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      id="articleForm">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <!-- Title -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="articleTitle"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}"
                                       placeholder="Masukkan judul artikel yang menarik..."
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
                                          rows="3"
                                          placeholder="Ringkasan singkat yang menarik pembaca (opsional, maks 500 karakter)">{{ old('excerpt') }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Ringkasan akan muncul di halaman daftar artikel
                                </small>
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
                                          required>{{ old('content') }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-magic"></i>
                                    Gunakan toolbar untuk memformat teks seperti di Microsoft Word. Mendukung gambar, tabel, dan format rich text lainnya.
                                </small>
                                @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="editor-counter" class="mt-2">
                                    <i class="fas fa-file-alt"></i> 0 kata | <i class="fas fa-font"></i> 0 karakter
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Utama</label>
                                <div class="mb-2" id="preview-image-container" style="display: none;">
                                    <img src="" alt="Preview" class="img-thumbnail" id="preview-image" style="max-height: 200px;">
                                </div>
                                <input type="file"
                                       name="featured_image"
                                       class="form-control @error('featured_image') is-invalid @enderror"
                                       accept="image/*"
                                       onchange="previewImage(this)">
                                <small class="text-muted">
                                    <i class="fas fa-image"></i> Format: JPG, PNG, GIF, WebP. Maksimal: 2MB
                                </small>
                                @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="article-status" class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status', 'draft') == $status ? 'selected' : '' }}>
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
                                       value="{{ old('published_at') }}">
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
                                           {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        <i class="fas fa-star text-warning"></i> Jadikan Artikel Unggulan
                                    </label>
                                </div>
                            </div>

                            <!-- SEO Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="text-primary">
                                    <i class="fas fa-search"></i> SEO Meta (Opsional)
                                </h5>
                                <hr>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text"
                                       name="meta_title"
                                       class="form-control @error('meta_title') is-invalid @enderror"
                                       value="{{ old('meta_title') }}"
                                       placeholder="Judul untuk mesin pencari">
                                <small class="text-muted">Kosongkan untuk menggunakan judul artikel</small>
                                @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text"
                                       name="meta_keywords"
                                       class="form-control @error('meta_keywords') is-invalid @enderror"
                                       value="{{ old('meta_keywords') }}"
                                       placeholder="mangrove, jakarta, lingkungan">
                                <small class="text-muted">Pisahkan dengan koma</small>
                                @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description"
                                          class="form-control @error('meta_description') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Deskripsi singkat untuk hasil pencarian Google (maks 160 karakter)">{{ old('meta_description') }}</textarea>
                                <small class="text-muted">Deskripsi untuk hasil pencarian</small>
                                @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="preview-section" id="previewSection">
                            <h5 class="mb-3"><i class="fas fa-eye"></i> Preview Artikel</h5>
                            <div class="preview-content">
                                <h1 id="preview-title" class="mb-3"></h1>
                                <div id="preview-meta" class="text-muted mb-4"></div>
                                <div id="preview-body" class="article-content"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" name="action" value="draft" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Simpan sebagai Draft
                                </button>
                                <button type="submit" name="action" value="publish" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Publish Artikel
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
<!-- TinyMCE Latest Version -->
<script src="https://cdn.tiny.cloud/1/i4v7kghp0a4db8g8ssqhwmv2prjk8x4mfudpnb3en51xhpt2/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Article Frontend CSS for Preview -->
<link rel="stylesheet" href="{{ asset('css/article-frontend.css') }}">

<script src="{{ asset('js/tinymce-config.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize TinyMCE
    TinyMCEConfig.init('#articleContent');

    // Handle form submission
    $('#articleForm').on('submit', function(e) {
        const content = tinymce.get('articleContent').getContent();

        if (!content.trim() || content === '<p></p>' || content === '<p><br></p>') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Konten Kosong',
                text: 'Mohon isi konten artikel terlebih dahulu',
                confirmButtonColor: '#009966'
            });
            return false;
        }

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        return true;
    });

    $('button[name="action"]').on('click', function() {
        let action = $(this).val();
        $('#article-status').val(action === 'draft' ? 'draft' : 'published');
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 2MB',
                confirmButtonColor: '#009966'
            });
            input.value = '';
            return;
        }

        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Valid',
                text: 'Hanya menerima format JPG, PNG, GIF, atau WebP',
                confirmButtonColor: '#009966'
            });
            input.value = '';
            return;
        }

        let reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-image').attr('src', e.target.result);
            $('#preview-image-container').show();
        };
        reader.readAsDataURL(file);
    }
}

function togglePreview() {
    const $previewSection = $('#previewSection');
    const $button = $('button[onclick="togglePreview()"]');

    $previewSection.toggleClass('show');

    if ($previewSection.hasClass('show')) {
        updatePreview();
        $button.html('<i class="fas fa-eye-slash"></i> Sembunyikan Preview');

        $('html, body').animate({
            scrollTop: $previewSection.offset().top - 100
        }, 500);
    } else {
        $button.html('<i class="fas fa-eye"></i> Preview');
    }
}

function updatePreview() {
    const title = $('#articleTitle').val() || 'Judul Artikel';
    const content = tinymce.get('articleContent') ? tinymce.get('articleContent').getContent() : '';
    const excerpt = $('textarea[name="excerpt"]').val();

    $('#preview-title').text(title);
    $('#preview-body').html(content);

    if (excerpt && $('#preview-excerpt').length === 0) {
        $('#preview-body').before(`
            <div id="preview-excerpt" class="lead mb-4" style="font-size: 1.25rem; color: #4b5563; line-height: 1.7;">
                ${excerpt}
            </div>
        `);
    } else if (excerpt) {
        $('#preview-excerpt').text(excerpt);
    } else {
        $('#preview-excerpt').remove();
    }

    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    const timeStr = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });

    $('#preview-meta').html(`
        <i class="far fa-calendar"></i> ${dateStr} &nbsp;
        <i class="far fa-clock"></i> ${timeStr} &nbsp;
        <i class="far fa-user"></i> {{ auth()->user()->name }}
    `);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        $('#article-status').val('draft');
        $('#articleForm').submit();
    }

    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        togglePreview();
    }
});

// Warn before leaving
let formChanged = false;
$('#articleForm input, #articleForm textarea, #articleForm select').on('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged && !$('#articleForm').data('submitting')) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

$('#articleForm').on('submit', function() {
    $(this).data('submitting', true);
    formChanged = false;
});
</script>
@endsection
