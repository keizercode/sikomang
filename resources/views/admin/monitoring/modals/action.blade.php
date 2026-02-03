<!-- Modal Add Action -->
<div class="modal fade" id="actionModal{{ $damage->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.monitoring.add-action', [$keyId, $damage->id]) }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aksi Penanganan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted mb-3">Untuk: <strong>{{ $damage->title }}</strong></p>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Aksi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="action_description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Aksi</label>
                        <input type="date" class="form-control" name="action_date" value="{{ date('Y-m-d') }}">
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
