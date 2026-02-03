<!-- Modal Upload Images -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.monitoring.upload-images', $keyId) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Upload Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto (Multiple)</label>
                        <input type="file" class="form-control" name="images[]" multiple accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 5MB per file. Bisa upload multiple.</small>
                    </div>

                    <div id="captionFields"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview images and add caption fields
$('input[name="images[]"]').on('change', function(e) {
    const files = e.target.files;
    const captionFields = $('#captionFields');
    captionFields.empty();

    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            captionFields.append(`
                <div class="mb-2">
                    <label class="form-label">Caption untuk Foto ${i + 1}</label>
                    <input type="text" class="form-control" name="captions[]" placeholder="Optional">
                </div>
            `);
        }
    }
});
</script>
