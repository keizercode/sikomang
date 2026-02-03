@extends('layouts.admin.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $title }}</h5>
                </div>
                <form action="{{ isset($gallery) ? route('admin.galleries.update', $gallery) : route('admin.galleries.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($gallery))
                        @method('PUT')
                    @endif

                    <div class="card-body">
                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul Foto <span class="text-danger">*</span></label>
                                <input type="text" name="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $gallery->title ?? '') }}"
                                       required>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category }}"
                                            {{ old('category', $gallery->category ?? '') == $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Image Upload -->
                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    Foto
                                    @if(!isset($gallery))
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                @if(isset($gallery) && $gallery->image_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $gallery->image_path) }}"
                                         alt="Current image"
                                         class="img-thumbnail"
                                         style="max-height: 250px;">
                                    <p class="text-muted mt-1 mb-0">Upload foto baru untuk mengganti</p>
                                </div>
                                @endif
                                <input type="file"
                                       name="image"
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*"
                                       {{ isset($gallery) ? '' : 'required' }}>
                                <small class="text-muted">Format: JPG, PNG, GIF. Maks: 5MB</small>
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="4">{{ old('description', $gallery->description ?? '') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi</label>
                                <select name="location_id" class="form-control @error('location_id') is-invalid @enderror">
                                    <option value="">-- Pilih Lokasi (Opsional) --</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}"
                                            {{ old('location_id', $gallery->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date Taken -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pengambilan</label>
                                <input type="date"
                                       name="date_taken"
                                       class="form-control @error('date_taken') is-invalid @enderror"
                                       value="{{ old('date_taken', isset($gallery->date_taken) ? $gallery->date_taken->format('Y-m-d') : '') }}">
                                @error('date_taken')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Photographer -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fotografer</label>
                                <input type="text"
                                       name="photographer"
                                       class="form-control @error('photographer') is-invalid @enderror"
                                       value="{{ old('photographer', $gallery->photographer ?? '') }}">
                                @error('photographer')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Order -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Urutan Tampilan</label>
                                <input type="number"
                                       name="order"
                                       class="form-control @error('order') is-invalid @enderror"
                                       value="{{ old('order', $gallery->order ?? 0) }}"
                                       min="0">
                                <small class="text-muted">Semakin kecil akan ditampilkan lebih dulu</small>
                                @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Checkboxes -->
                            <div class="col-12 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                   name="is_featured"
                                                   class="form-check-input"
                                                   id="is_featured"
                                                   value="1"
                                                   {{ old('is_featured', $gallery->is_featured ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                Jadikan Foto Unggulan
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                   name="is_active"
                                                   class="form-check-input"
                                                   id="is_active"
                                                   value="1"
                                                   {{ old('is_active', $gallery->is_active ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Aktif/Tampilkan
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview image before upload
    $('input[name="image"]').on('change', function(e) {
        let file = e.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let preview = `
                    <div class="mb-2">
                        <img src="${e.target.result}" class="img-thumbnail" style="max-height: 250px;">
                        <p class="text-muted mt-1 mb-0">Preview foto baru</p>
                    </div>
                `;
                $('input[name="image"]').before(preview);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
