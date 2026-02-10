<div id="reportModal" class="report-modal" style="display: none;">
    <div class="report-modal-overlay" onclick="closeReportModal()"></div>

    <div class="report-modal-content">
        <!-- Modal Header -->
        <div class="report-modal-header">
            <div>
                <h2 class="report-modal-title">Laporkan Kondisi Mangrove</h2>
                <p class="report-modal-subtitle">Bantu kami menjaga ekosistem mangrove dengan melaporkan kondisi yang Anda temukan</p>
            </div>
            <button type="button" class="report-modal-close" onclick="closeReportModal()">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="reportForm" enctype="multipart/form-data">
            @csrf

            <div class="report-modal-body">
                <!-- Step Indicator -->
                <div class="report-steps">
                    <div class="report-step active" data-step="1">
                        <span class="report-step-number">1</span>
                        <span class="report-step-label">Lokasi</span>
                    </div>
                    <div class="report-step" data-step="2">
                        <span class="report-step-number">2</span>
                        <span class="report-step-label">Detail Laporan</span>
                    </div>
                    <div class="report-step" data-step="3">
                        <span class="report-step-number">3</span>
                        <span class="report-step-label">Identitas</span>
                    </div>
                </div>

                <!-- Step 1: Location Selection -->
                <div class="report-form-step" id="step-1">
                    <div class="form-group">
                        <label class="form-label">
                            Pilih Lokasi Mangrove <span class="text-danger">*</span>
                        </label>
                        <div class="location-search-wrapper">
                            <input type="text"
                                   id="locationSearch"
                                   class="form-control"
                                   placeholder="Ketik untuk mencari lokasi..."
                                   autocomplete="off">
                            <div id="locationResults" class="location-results" style="display: none;"></div>
                        </div>
                        <input type="hidden" name="mangrove_location_id" id="selectedLocationId" required>
                        <div id="selectedLocationDisplay" class="selected-location" style="display: none;">
                            <div class="selected-location-content">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                                <span id="selectedLocationName"></span>
                                <button type="button" class="btn-clear-location" onclick="clearLocationSelection()">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <small class="form-text">Lokasi saat ini: <strong id="currentLocationName">-</strong></small>
                    </div>
                </div>

                <!-- Step 2: Report Details -->
                <div class="report-form-step" id="step-2" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">
                            Jenis Laporan <span class="text-danger">*</span>
                        </label>
                        <select name="report_type" class="form-control" required>
                            <option value="">- Pilih Jenis Laporan -</option>
                            <option value="kerusakan">Kerusakan Mangrove</option>
                            <option value="pencemaran">Pencemaran Lingkungan</option>
                            <option value="penebangan_liar">Penebangan Liar</option>
                            <option value="kondisi_baik">Kondisi Baik (Apresiasi)</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Tingkat Urgensi <span class="text-danger">*</span>
                        </label>
                        <div class="urgency-options">
                            <label class="urgency-option">
                                <input type="radio" name="urgency_level" value="rendah" required>
                                <span class="urgency-badge urgency-rendah">Rendah</span>
                            </label>
                            <label class="urgency-option">
                                <input type="radio" name="urgency_level" value="sedang" required>
                                <span class="urgency-badge urgency-sedang">Sedang</span>
                            </label>
                            <label class="urgency-option">
                                <input type="radio" name="urgency_level" value="tinggi" required>
                                <span class="urgency-badge urgency-tinggi">Tinggi</span>
                            </label>
                            <label class="urgency-option">
                                <input type="radio" name="urgency_level" value="darurat" required>
                                <span class="urgency-badge urgency-darurat">Darurat</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Kondisi/Masalah <span class="text-danger">*</span>
                        </label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="5"
                                  minlength="20"
                                  maxlength="2000"
                                  placeholder="Jelaskan kondisi atau masalah yang Anda temukan secara detail (minimal 20 karakter)"
                                  required></textarea>
                        <small class="form-text">
                            <span id="charCount">0</span>/2000 karakter
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Upload Foto Pendukung
                            <span class="text-muted">(Opsional, maksimal 5 foto)</span>
                        </label>
                        <div class="photo-upload-area" onclick="document.getElementById('photoInput').click()">
                            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p>Klik untuk upload foto</p>
                            <small>JPG, PNG (Max 5MB per foto)</small>
                        </div>
                        <input type="file"
                               id="photoInput"
                               name="photos[]"
                               multiple
                               accept="image/jpeg,image/png,image/jpg"
                               style="display: none;"
                               onchange="previewPhotos(event)">
                        <div id="photoPreview" class="photo-preview"></div>
                    </div>
                </div>

                <!-- Step 3: Reporter Identity -->
                <div class="report-form-step" id="step-3" style="display: none;">
                    <div class="alert alert-info">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                        </svg>
                        <span>Identitas Anda diperlukan untuk tracking dan follow-up laporan</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="reporter_name"
                                   class="form-control"
                                   placeholder="Masukkan nama lengkap Anda"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   name="reporter_email"
                                   class="form-control"
                                   placeholder="contoh@email.com"
                                   required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                No. Telepon/WhatsApp <span class="text-danger">*</span>
                            </label>
                            <input type="tel"
                                   name="reporter_phone"
                                   class="form-control"
                                   placeholder="08xxxxxxxxxx"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Organisasi/Instansi <span class="text-muted">(Opsional)</span>
                            </label>
                            <input type="text"
                                   name="reporter_organization"
                                   class="form-control"
                                   placeholder="Nama organisasi atau instansi">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Alamat <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea name="reporter_address"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Alamat lengkap Anda"></textarea>
                    </div>
                </div>
            </div>

            <!-- Modal Footer with Navigation -->
            <div class="report-modal-footer">
                <button type="button" class="btn btn-secondary" id="btnPrev" onclick="previousStep()" style="display: none;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
                    </svg>
                    <span>Sebelumnya</span>
                </button>

                <button type="button" class="btn btn-primary" id="btnNext" onclick="nextStep()">
                    <span>Selanjutnya</span>
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                    </svg>
                </button>

                <button type="submit" class="btn btn-success" id="btnSubmit" style="display: none;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                    </svg>
                    <span>Kirim Laporan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="report-modal" style="display: none;">
    <div class="report-modal-overlay" onclick="closeSuccessModal()"></div>
    <div class="report-modal-content report-success-modal">
        <div class="success-icon">
            <svg width="64" height="64" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
        </div>
        <h3>Laporan Berhasil Dikirim!</h3>
        <p>Nomor Laporan Anda: <strong id="reportNumber"></strong></p>
        <p class="text-muted">Simpan nomor ini untuk mengecek status laporan Anda</p>
        <button type="button" class="btn btn-primary" onclick="closeSuccessModal()">Tutup</button>
    </div>
</div>
