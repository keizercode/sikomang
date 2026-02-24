{{-- Modal Matriks Rekomendasi --}}
<div class="matrix-backdrop" id="matrixBackdrop" onclick="closeMatrix()"></div>
<div class="matrix-modal" id="matrixModal">
    <button class="modal-close" onclick="closeMatrix()">&times;</button>
    <div class="matrix-header">
        <h3>Matrik Rekomendasi Pengelolaan Kawasan Hutan Mangrove DKI Jakarta</h3>
    </div>

    <div class="matrix-container">
        {{-- Y-Axis Label --}}
        <div class="y-axis-label">
            <span>Nilai Akhir Kesehatan (NAK)</span>
        </div>

        {{-- Matrix Grid --}}
        <div class="matrix-grid">
            {{-- Row 10 --}}
            <div class="nak-label">10</div>
            <div class="matrix-cell pengkayaan-prioritas">
                <div class="cell-title">Pengkayaan Prioritas</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>
            <div class="matrix-cell dilindungi">
                <div class="cell-title">Dilindungi</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>
            <div class="matrix-cell dipertahankan">
                <div class="cell-title">Dipertahankan dan/atau Pemanfaatan Lestari</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>

            {{-- Row 8 --}}
            <div class="nak-label">8</div>
            <div class="matrix-cell pengkayaan">
                <div class="cell-title">Pengkayaan</div>
                <ul>
                    <li>&#8226; Pantai Marunda</li>
                    <li>&#8226; Mangrove STIP</li>
                    <li>&#8226; Mangrove Si Pitung</li>
                </ul>
            </div>
            <div class="matrix-cell pengkayaan">
                <div class="cell-title">Pengkayaan</div>
                <ul>
                    <li>&#8226; Rawa Hutan Lindung</li>
                    <li>&#8226; Pos 2 Hutan Lindung</li>
                    <li>&#8226; TWA Angke Kapuk</li>
                </ul>
            </div>
            <div class="matrix-cell dilindungi-2">
                <div class="cell-title">Dilindungi</div>
                <ul>
                    <li>&#8226; Pos 5 Hutan Lindung</li>
                    <li>&#8226; Pos Elang Laut</li>
                    <li>&#8226; Pasmar 1 TNI AL</li>
                    <li>&#8226; Pulau Lancang Besar</li>
                    <li>&#8226; Ekowisata Mangrove PIK</li>
                </ul>
            </div>

            {{-- Row 6 --}}
            <div class="nak-label">6</div>
            <div class="matrix-cell rehabilitasi">
                <div class="cell-title">Rehabilitasi</div>
                <ul>
                    <li>&#8226; Tanah Timbul (Bird Feeding)</li>
                    <li>&#8226; Pulau Kelapa Dua</li>
                    <li>&#8226; Pulau Tidung Besar dan Tidung Kecil</li>
                    <li>&#8226; Pulau Pramuka</li>
                </ul>
            </div>
            <div class="matrix-cell pengkayaan-rehabilitasi">
                <div class="cell-title">Pengkayaan / Rehabilitasi</div>
                <ul>
                    <li>&#8226; Suaka Margasatwa Muara Angke</li>
                </ul>
            </div>
            <div class="matrix-cell pengkayaan-2">
                <div class="cell-title">Pengkayaan</div>
                <ul>
                    <li>&#8226; Pulau Kelapa</li>
                    <li>&#8226; Komunitas Mangrove Muara Angke</li>
                </ul>
            </div>

            {{-- Row 4 --}}
            <div class="nak-label">4</div>
            <div class="matrix-cell restorasi">
                <div class="cell-title">Restorasi</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>
            <div class="matrix-cell rehabilitasi-2">
                <div class="cell-title">Rehabilitasi</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>
            <div class="matrix-cell rehabilitasi-3">
                <div class="cell-title">Rehabilitasi</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>

            {{-- Row 2 --}}
            <div class="nak-label">2</div>
            <div class="matrix-cell restorasi-prioritas">
                <div class="cell-title">Restorasi Prioritas</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>
            <div class="matrix-cell restorasi-2">
                <div class="cell-title">Restorasi</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>
            <div class="matrix-cell rehabilitasi-prioritas">
                <div class="cell-title">Rehabilitasi Prioritas</div>
                <ul><li>&#8226; N/A</li></ul>
            </div>
        </div>

        {{-- X-Axis Labels --}}
        <div class="x-axis-labels">
            <div class="x-label">Jarang</div>
            <div class="x-label">Sedang</div>
            <div class="x-label">Lebat</div>
        </div>

        {{-- X-Axis Title --}}
        <div class="x-axis-title">Kelas Kerapatan</div>
    </div>
</div>

