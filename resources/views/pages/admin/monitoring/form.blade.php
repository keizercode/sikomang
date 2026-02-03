@extends('layouts.admin.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="mdi mdi-map-marker"></i> {{ $title }}
                    </div>
                    <form action="{{ route($route.'.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="secure_id" value="{{ @$keyId }}">

                                <!-- Informasi Umum -->
                                <div class="col-12 mb-4">
                                    <h5 class="text-primary">Informasi Umum</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', @$item->name) }}" required>
                                    @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Slug</label>
                                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                           value="{{ old('slug', @$item->slug) }}">
                                    <small class="text-muted">Kosongkan untuk generate otomatis</small>
                                    @error('slug')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Wilayah</label>
                                    <input type="text" name="region" class="form-control @error('region') is-invalid @enderror"
                                           value="{{ old('region', @$item->region) }}"
                                           placeholder="Contoh: Kecamatan Penjaringan, Jakarta Utara">
                                    @error('region')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pengelola</label>
                                    <input type="text" name="manager" class="form-control @error('manager') is-invalid @enderror"
                                           value="{{ old('manager', @$item->manager) }}"
                                           placeholder="Contoh: DPHK">
                                    @error('manager')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Koordinat -->
                                <div class="col-12 mb-4 mt-3">
                                    <h5 class="text-primary">Koordinat & Lokasi</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Latitude <span class="text-danger">*</span></label>
                                    <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
                                           value="{{ old('latitude', @$item->latitude) }}"
                                           placeholder="Contoh: -6.1023"
                                           required>
                                    @error('latitude')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Longitude <span class="text-danger">*</span></label>
                                    <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror"
                                           value="{{ old('longitude', @$item->longitude) }}"
                                           placeholder="Contoh: 106.7655"
                                           required>
                                    @error('longitude')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Klasifikasi -->
                                <div class="col-12 mb-4 mt-3">
                                    <h5 class="text-primary">Klasifikasi</h5>
                                    <hr>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Luas Area (ha)</label>
                                    <input type="number" step="0.01" name="area" class="form-control @error('area') is-invalid @enderror"
                                           value="{{ old('area', @$item->area) }}"
                                           placeholder="Kosongkan jika belum diidentifikasi">
                                    <small class="text-muted">Kosongkan jika luas area belum diidentifikasi</small>
                                    @error('area')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kerapatan <span class="text-danger">*</span></label>
                                    <select name="density" class="form-control @error('density') is-invalid @enderror" required>
                                        <option value="">-Pilih Kerapatan-</option>
                                        <option value="jarang" {{ old('density', @$item->density) == 'jarang' ? 'selected' : '' }}>Jarang</option>
                                        <option value="sedang" {{ old('density', @$item->density) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                        <option value="lebat" {{ old('density', @$item->density) == 'lebat' ? 'selected' : '' }}>Lebat</option>
                                    </select>
                                    @error('density')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipe Lokasi <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">-Pilih Tipe-</option>
                                        <option value="pengkayaan" {{ old('type', @$item->type) == 'pengkayaan' ? 'selected' : '' }}>Pengkayaan</option>
                                        <option value="rehabilitasi" {{ old('type', @$item->type) == 'rehabilitasi' ? 'selected' : '' }}>Rehabilitasi</option>
                                        <option value="dilindungi" {{ old('type', @$item->type) == 'dilindungi' ? 'selected' : '' }}>Dilindungi</option>
                                        <option value="restorasi" {{ old('type', @$item->type) == 'restorasi' ? 'selected' : '' }}>Restorasi</option>
                                    </select>
                                    @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Kesehatan & Pengelola -->
                                <div class="col-12 mb-4 mt-3">
                                    <h5 class="text-primary">Kesehatan & Pengelolaan</h5>
                                    <hr>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kesehatan (%)</label>
                                    <input type="number" step="0.01" name="health_percentage" class="form-control @error('health_percentage') is-invalid @enderror"
                                           value="{{ old('health_percentage', @$item->health_percentage) }}"
                                           min="0" max="100"
                                           placeholder="Contoh: 98">
                                    <small class="text-muted">Persentase kesehatan mangrove (0-100)</small>
                                    @error('health_percentage')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Skor Kesehatan (NAK)</label>
                                    <input type="text" name="health_score" class="form-control @error('health_score') is-invalid @enderror"
                                           value="{{ old('health_score', @$item->health_score) }}"
                                           placeholder="Contoh: NAK: 7.2">
                                    <small class="text-muted">Nilai Akhir Kesehatan (NAK)</small>
                                    @error('health_score')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tahun Penetapan</label>
                                    <input type="number" name="year_established" class="form-control @error('year_established') is-invalid @enderror"
                                           value="{{ old('year_established', @$item->year_established) }}"
                                           min="1900" max="{{ date('Y') }}"
                                           placeholder="{{ date('Y') }}">
                                    @error('year_established')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Informasi Tambahan -->
                                <div class="col-12 mb-4 mt-3">
                                    <h5 class="text-primary">Informasi Tambahan</h5>
                                    <hr>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Spesies</label>
                                    <textarea name="species" class="form-control @error('species') is-invalid @enderror" rows="2"
                                              placeholder="Contoh: Avicennia alba, Avicennia marina, Rhizophora mucronata">{{ old('species', @$item->species) }}</textarea>
                                    <small class="text-muted">Pisahkan dengan koma</small>
                                    @error('species')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                              placeholder="Deskripsi singkat tentang lokasi mangrove">{{ old('description', @$item->description) }}</textarea>
                                    @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Alamat Lokasi</label>
                                    <textarea name="location_address" class="form-control @error('location_address') is-invalid @enderror" rows="2"
                                              placeholder="Alamat lengkap lokasi">{{ old('location_address', @$item->location_address) }}</textarea>
                                    @error('location_address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Data Karbon</label>
                                    <textarea name="carbon_data" class="form-control @error('carbon_data') is-invalid @enderror" rows="2"
                                              placeholder="Informasi tentang penyerapan karbon">{{ old('carbon_data', @$item->carbon_data) }}</textarea>
                                    @error('carbon_data')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    {{-- <div class="form-check">
                                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                                               {{ old('is_active', @$item->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Status Aktif
                                        </label>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <a href="{{ route($route.'.index') }}" class="btn btn-danger">
                                        <i class="mdi mdi-cancel"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="mdi mdi-content-save-outline"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('input[name="name"]').on('keyup', function() {
        var slug = $(this).val()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('input[name="slug"]').val(slug);
    });
});
</script>
@endsection
