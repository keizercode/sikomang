<!-- Modal Edit Spesies -->
<div class="modal fade" id="speciesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.monitoring.update-species', $keyId) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Spesies</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Vegetasi -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Vegetasi</label>
                        <div id="vegetasiContainer">
                            @if($location->details && isset($location->details->species_detail['vegetasi']))
                                @foreach($location->details->species_detail['vegetasi'] as $item)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="vegetasi[]" value="{{ $item }}">
                                    <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                                        <i class="mdi mdi-minus"></i>
                                    </button>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addField('vegetasiContainer', 'vegetasi', 'Nama vegetasi')">
                            <i class="mdi mdi-plus"></i> Tambah Vegetasi
                        </button>
                    </div>

                    <!-- Fauna -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fauna</label>
                        <div id="faunaContainer">
                            @if($location->details && isset($location->details->species_detail['fauna']))
                                @foreach($location->details->species_detail['fauna'] as $item)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="fauna[]" value="{{ $item }}">
                                    <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                                        <i class="mdi mdi-minus"></i>
                                    </button>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addField('faunaContainer', 'fauna', 'Nama fauna')">
                            <i class="mdi mdi-plus"></i> Tambah Fauna
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
