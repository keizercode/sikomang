<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExcelExportController extends FrontendController
{
    /**
     * URL sumber GeoJSON Plovis — sama persis dengan GeoJsonApiController
     */
    private const PLOVIS_URLS = [
        'jarang' => 'https://asset.plovis.id/plovis/public/67f25022-a757-4f90-a114-16e3f3ad671c.geojson',
        'sedang' => 'https://asset.plovis.id/plovis/public/1c7b760f-7458-4353-bfd9-1ba6084cdce6.geojson',
        'lebat'  => 'https://asset.plovis.id/plovis/public/cb7b89d7-2ac7-4fa4-a16c-02734432838e.geojson',
    ];

    private const KTTJ_LABELS = [
        'jarang' => 'MANGROVE JARANG',
        'sedang' => 'MANGROVE SEDANG',
        'lebat'  => 'MANGROVE LEBAT',
    ];

    /**
     * 21 kolom persis sesuai template GIS Plovis.
     * Urutan & nama TIDAK boleh diubah.
     */
    private const COLUMNS = [
        'A' => 'BPDAS',
        'B' => 'KTTJ',
        'C' => 'SMBDT',
        'D' => 'THNBUAT',
        'E' => 'INTS',
        'F' => 'REMARK',
        'G' => 'STRUKTUR_V',
        'H' => 'LSMGR',
        'I' => 'Shape_Leng',
        'J' => 'Shape_Area',
        'K' => 'KODE_PROV',
        'L' => 'FUNGSIKWS',
        'M' => 'NOSKKWS',
        'N' => 'TGLSKKWS',
        'O' => 'LSKKWS',
        'P' => 'Kawasan',
        'Q' => 'KONSERVASI',
        'R' => 'WADMKK',
        'S' => 'WADMPR',
        'T' => 'icon',
        'U' => 'colorIndex',
    ];

    /**
     * Export data mangrove ke Excel.
     * Data diambil LANGSUNG dari Plovis API — sama dengan sumber peta.
     * Menggunakan PhpSpreadsheet (fitur bawaan Laravel ecosystem).
     *
     * Route: GET /monitoring/export/{category}
     */
    public function exportMangroveData(string $category)
    {
        $category = strtolower(trim($category));

        abort_if(
            !array_key_exists($category, self::PLOVIS_URLS),
            400,
            'Kategori tidak valid. Gunakan: jarang, sedang, atau lebat.'
        );

        $features  = $this->fetchPlovisFeatures($category);
        $spreadsheet = $this->buildSpreadsheet($features, $category);

        $filename = sprintf(
            'export-map-Mangrove_Jakarta_-%s-%s.xlsx',
            ucfirst($category),
            now()->format('Ymd')
        );

        return response()->streamDownload(
            function () use ($spreadsheet) {
                (new Xlsx($spreadsheet))->save('php://output');
            },
            $filename,
            [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'max-age=0, no-cache, no-store',
                'Pragma'              => 'no-cache',
            ]
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PRIVATE — Fetch dari Plovis API
    // ─────────────────────────────────────────────────────────────────────────

    private function fetchPlovisFeatures(string $category): array
    {
        $response = Http::timeout(60)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(self::PLOVIS_URLS[$category]);

        abort_if(
            !$response->successful(),
            502,
            "Gagal mengambil data dari Plovis (HTTP {$response->status()})."
        );

        $json    = $response->json();
        $geojson = $json['geojson'] ?? $json;   // Plovis membungkus di key "geojson"

        abort_if(
            !isset($geojson['features']) || !is_array($geojson['features']),
            502,
            'Format GeoJSON Plovis tidak valid.'
        );

        return collect($geojson['features'])
            ->filter(fn($f) => !empty($f['properties']))
            ->map(fn($f) => $f['properties'])
            ->values()
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PRIVATE — Bangun Spreadsheet
    // ─────────────────────────────────────────────────────────────────────────

    private function buildSpreadsheet(array $features, string $category): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('SIKOMANG')
            ->setTitle('Export Map Mangrove Jakarta - ' . ucfirst($category))
            ->setSubject('Data Sebaran Mangrove ' . self::KTTJ_LABELS[$category])
            ->setDescription('Sumber: Plovis KLHK. Export: ' . now()->format('d/m/Y H:i'));

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');

        $this->writeHeaderRow($sheet);

        $row = 2;
        foreach ($features as $props) {
            $this->writeDataRow($sheet, $row, $props, $category);
            $row++;
        }

        foreach (array_keys(self::COLUMNS) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:U1');

        return $spreadsheet;
    }

    private function writeHeaderRow(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $style = [
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
                'name'  => 'Arial',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F7942'],   // Hijau GIS standar
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];

        foreach (self::COLUMNS as $col => $label) {
            $sheet->setCellValue("{$col}1", $label);
            $sheet->getStyle("{$col}1")->applyFromArray($style);
        }

        $sheet->getRowDimension(1)->setRowHeight(20);
    }

    /**
     * Tulis satu baris data dari properties GeoJSON Plovis.
     * Key mapping 1:1 dengan nama field asli Plovis — akurat tanpa transformasi.
     */
    private function writeDataRow(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int $row,
        array $props,
        string $category
    ): void {
        $sheet->setCellValue("A{$row}", $props['BPDAS']      ?? 'CITARUM CILIWUNG');
        $sheet->setCellValue("B{$row}", $props['KTTJ']       ?? self::KTTJ_LABELS[$category]);
        $sheet->setCellValue("C{$row}", $props['SMBDT']      ?? null);
        $sheet->setCellValue("D{$row}", $props['THNBUAT']    ?? ($props['TAHUN'] ?? null));
        $sheet->setCellValue("E{$row}", $props['INTS']       ?? 'KLHK');
        $sheet->setCellValue("F{$row}", $props['REMARK']     ?? 'TIDAK ADA CATATAN');
        $sheet->setCellValue("G{$row}", $props['STRUKTUR_V'] ?? null);

        // LSMGR — luas mangrove (ha)
        $lsmgr = $props['LSMGR'] ?? null;
        if (is_numeric($lsmgr)) {
            $sheet->setCellValue("H{$row}", (float) $lsmgr);
            $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00000000');
        } else {
            $sheet->setCellValue("H{$row}", 0);
        }

        // Shape_Leng
        $shapeLeng = $props['Shape_Leng'] ?? null;
        if (is_numeric($shapeLeng)) {
            $sheet->setCellValue("I{$row}", (float) $shapeLeng);
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('0.00000000000');
        }

        // Shape_Area
        $shapeArea = $props['Shape_Area'] ?? null;
        if (is_numeric($shapeArea)) {
            $sheet->setCellValue("J{$row}", (float) $shapeArea);
            $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('0.00000E+00');
        }

        $kodeProv = $props['KODE_PROV'] ?? 31;
        $sheet->setCellValue("K{$row}", is_numeric($kodeProv) ? (int) $kodeProv : $kodeProv);

        $fungsikws = $props['FUNGSIKWS'] ?? null;
        $sheet->setCellValue("L{$row}", is_numeric($fungsikws) ? (int) $fungsikws : $fungsikws);

        $sheet->setCellValue("M{$row}", $props['NOSKKWS']  ?? null);
        $sheet->setCellValue("N{$row}", $props['TGLSKKWS'] ?? null);

        $lskkws = $props['LSKKWS'] ?? null;
        if (is_numeric($lskkws)) {
            $sheet->setCellValue("O{$row}", (float) $lskkws);
            $sheet->getStyle("O{$row}")->getNumberFormat()->setFormatCode('#,##0');
        } else {
            $sheet->setCellValue("O{$row}", $lskkws);
        }

        $sheet->setCellValue("P{$row}", $props['Kawasan']    ?? null);
        $sheet->setCellValue("Q{$row}", $props['KONSERVASI'] ?? null);
        $sheet->setCellValue("R{$row}", $props['WADMKK']     ?? null);
        $sheet->setCellValue("S{$row}", $props['WADMPR']     ?? null);
        $sheet->setCellValue("T{$row}", $props['icon']       ?? null);
        $sheet->setCellValue("U{$row}", $props['colorIndex'] ?? 0);

        // Zebra striping ringan
        if ($row % 2 === 0) {
            $sheet->getStyle("A{$row}:U{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F5F5F5'],
                ],
            ]);
        }
    }
}
