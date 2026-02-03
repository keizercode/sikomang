<!-- Modal Edit Aktivitas -->
<div class="modal fade" id="activitiesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.monitoring.update-activities', $keyId) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Aktivitas Sekitar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3">{{ $location->details->activities['description'] ?? '' }}</textarea>
                    </div>

                    <!-- Items -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Aktivitas</label>
                        <div id="activitiesContainer">
                            @if($location->details && isset($location->details->activities['items']))
                                @foreach($location->details->activities['items'] as $item)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="items[]" value="{{ $item }}">
                                    <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                                        <i class="mdi mdi-minus"></i>
                                    </button>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addField('activitiesContainer', 'items', 'Aktivitas')">
                            <i class="mdi mdi-plus"></i> Tambah Aktivitas
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
