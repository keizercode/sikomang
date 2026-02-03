<!-- Modal Edit Program & Pemanfaatan -->
<div class="modal fade" id="programsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('admin.monitoring.update-programs', $keyId) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Program & Pemanfaatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Pemanfaatan Hutan -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Pemanfaatan Hutan</label>
                            <div id="utilizationContainer">
                                @if($location->details && $location->details->forest_utilization)
                                    @foreach($location->details->forest_utilization as $item)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="forest_utilization[]" value="{{ $item }}">
                                        <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addField('utilizationContainer', 'forest_utilization', 'Pemanfaatan')">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>

                        <!-- Program -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Program yang Dilaksanakan</label>
                            <div id="programContainer">
                                @if($location->details && $location->details->programs)
                                    @foreach($location->details->programs as $item)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="programs[]" value="{{ $item }}">
                                        <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addField('programContainer', 'programs', 'Program')">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>

                        <!-- Stakeholders -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Pihak Terkait</label>
                            <div id="stakeholderContainer">
                                @if($location->details && $location->details->stakeholders)
                                    @foreach($location->details->stakeholders as $item)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="stakeholders[]" value="{{ $item }}">
                                        <button type="button" class="btn btn-danger" onclick="$(this).parent().remove()">
                                            <i class="mdi mdi-minus"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addField('stakeholderContainer', 'stakeholders', 'Pihak Terkait')">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>
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
