{{-- ============================================
     MODAL: TABEL DATA LOKASI (menu "Data")
     Menampilkan informasi lokasi dalam bentuk tabel,
     dengan opsi cetak & unduh Excel.
     ============================================ --}}
<div id="dataTableModal" class="report-modal" style="display: none;">
    <div class="report-modal-overlay" onclick="closeDataModal()"></div>

    <div class="report-modal-content" id="dataTableModalContent">
        {{-- Header --}}
        <div class="report-modal-header">
            <div>
                <h2 class="report-modal-title">Data Lokasi</h2>
                <p class="report-modal-subtitle">Tabel informasi lengkap: {{ $location['name'] }}</p>
            </div>
            <button type="button" class="report-modal-close" onclick="closeDataModal()">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="report-modal-body">
            <table class="data-table-info">
                <tbody>
                    @foreach([
                        'Nama Lokasi'         => $location['name'],
                        'Tipe'                => $location['type'],
                        'Tahun Ditetapkan'    => $location['year'],
                        'Luas Area'           => $location['area'],
                        'Kerapatan'           => ucfirst($location['density']),
                        'Kesehatan Mangrove'  => $location['health'],
                        'Koordinat'           => $location['coords'],
                        'Alamat/Lokasi'       => $location['location'],
                        'Pengelola'           => $location['manager'],
                        'Species'             => $location['species'],
                        'Serapan Karbon'      => $location['carbon_data'],
                        'Jumlah Kerusakan'    => $location['damage_count'],
                    ] as $label => $value)
                    <tr>
                        <th>{{ $label }}</th>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $location['description'] }}</td>
                    </tr>
                </tbody>
            </table>

            @if(!empty($location['species_detail']['vegetasi']) || !empty($location['species_detail']['fauna']))
            <h3 class="data-table-section-title">Spesies</h3>
            <table class="data-table-info">
                <tbody>
                    @if(!empty($location['species_detail']['vegetasi']))
                    <tr>
                        <th>Vegetasi</th>
                        <td>{{ implode(', ', $location['species_detail']['vegetasi']) }}</td>
                    </tr>
                    @endif
                    @if(!empty($location['species_detail']['fauna']))
                    <tr>
                        <th>Fauna</th>
                        <td>{{ implode(', ', $location['species_detail']['fauna']) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @endif

            @if(!empty($location['damages']))
            <h3 class="data-table-section-title">Kerusakan Teridentifikasi</h3>
            <table class="data-table-info">
                <tbody>
                    @foreach($location['damages'] as $index => $damage)
                    <tr>
                        <th>{{ $index + 1 }}</th>
                        <td>{{ $damage }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Footer --}}
        <div class="report-modal-footer" data-html2canvas-ignore="true">
            <button type="button" class="btn btn-secondary" onclick="printDataTable()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak</span>
            </button>
            <a href="{{ route('monitoring.detail-lokasi.export-data', $location['slug']) }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Unduh Excel</span>
            </a>
        </div>
    </div>
</div>
