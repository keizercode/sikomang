@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <div>
            <a href="{{ route('admin.settings.clear-cache') }}" class="btn btn-warning">
                <i class="fas fa-sync"></i> Clear Cache
            </a>
            <a href="{{ route('admin.settings.export') }}" class="btn btn-info">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Grup Pengaturan</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($groups as $group)
                        <a href="{{ route('admin.settings.index', ['group' => $group]) }}"
                           class="list-group-item list-group-item-action {{ $currentGroup == $group ? 'active' : '' }}">
                            <i class="fas fa-{{ $group == 'general' ? 'cog' : ($group == 'contact' ? 'envelope' : ($group == 'social' ? 'share-alt' : ($group == 'seo' ? 'search' : 'wrench'))) }}"></i>
                            {{ ucfirst($group) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Import Settings -->
            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Import Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="file" class="form-control form-control-sm" accept=".json" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-upload"></i> Import
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">{{ ucfirst($currentGroup) }} Settings</h6>
                </div>
                <div class="card-body">
                    @if($settings->count() > 0)
                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @foreach($settings as $setting)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        {{ $setting->label }}
                                        @if($setting->is_public)
                                            <span class="badge bg-success">Public</span>
                                        @endif
                                    </label>

                                    @if($setting->description)
                                        <small class="text-muted d-block mb-2">{{ $setting->description }}</small>
                                    @endif

                                    @if($setting->type == 'text')
                                        <input type="text"
                                               name="settings[{{ $setting->key }}]"
                                               class="form-control"
                                               value="{{ $setting->value }}">

                                    @elseif($setting->type == 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]"
                                                  class="form-control"
                                                  rows="4">{{ $setting->value }}</textarea>

                                    @elseif($setting->type == 'number')
                                        <input type="number"
                                               name="settings[{{ $setting->key }}]"
                                               class="form-control"
                                               value="{{ $setting->value }}">

                                    @elseif($setting->type == 'boolean')
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                            <input type="checkbox"
                                                   name="settings[{{ $setting->key }}]"
                                                   class="form-check-input"
                                                   value="1"
                                                   {{ $setting->value ? 'checked' : '' }}>
                                        </div>

                                    @elseif($setting->type == 'file')
                                        <div class="mb-2">
                                            @if($setting->value)
                                                <img src="{{ asset('storage/' . $setting->value) }}"
                                                     alt="{{ $setting->label }}"
                                                     class="img-thumbnail mb-2"
                                                     style="max-height: 100px;">
                                            @endif
                                        </div>
                                        <input type="file"
                                               name="settings[{{ $setting->key }}]"
                                               class="form-control"
                                               accept="image/*">

                                    @elseif($setting->type == 'json')
                                        <textarea name="settings[{{ $setting->key }}]"
                                                  class="form-control font-monospace"
                                                  rows="6">{{ $setting->value }}</textarea>
                                        <small class="text-muted">Format JSON</small>

                                    @endif
                                </div>
                            @endforeach

                            <hr>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('admin.settings.index', ['group' => $currentGroup]) }}"
                                   class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada pengaturan di grup ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add New Setting -->
            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Tambah Pengaturan Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Key</label>
                                <input type="text" name="key" class="form-control" required>
                                <small class="text-muted">Gunakan snake_case (contoh: site_name)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Label</label>
                                <input type="text" name="label" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="number">Number</option>
                                    <option value="boolean">Boolean</option>
                                    <option value="file">File</option>
                                    <option value="json">JSON</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Group</label>
                                <input type="text" name="group" class="form-control" value="{{ $currentGroup }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Value</label>
                                <input type="text" name="value" class="form-control">
                            </div>
                            <div class="col-md-10 mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Public?</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_public" class="form-check-input" value="1">
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-format JSON
$('textarea[name*="settings"]').each(function() {
    if ($(this).hasClass('font-monospace')) {
        try {
            const value = $(this).val();
            if (value) {
                const formatted = JSON.stringify(JSON.parse(value), null, 2);
                $(this).val(formatted);
            }
        } catch (e) {
            // Not valid JSON, leave as is
        }
    }
});
</script>
@endpush
@endsection
