<!-- Modal Add/Edit Damage -->
<div class="modal fade" id="damageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="damageForm" method="POST">
                @csrf
                <input type="hidden" id="damage_id" name="damage_id" value="">
                <input type="hidden" id="form_method" name="_method" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="damageModalTitle">Tambah Laporan Kerusakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Kerusakan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="damage_title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="damage_description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                        <select class="form-control" id="damage_priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="damage_status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitDamageBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
