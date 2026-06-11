<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LocationExportController extends Controller
{
    // ─── Public endpoints ─────────────────────────────────────────────────────

    /**
     * Unduh laporan PDF lengkap satu lokasi.
     * Route: GET /admin/monitoring/{id}/export-pdf
     */
    public function exportPdf(string $id)
    {
        $location = $this->getLocation($id);

        $pdf = Pdf::loadView('layouts.admin.reports.location-detail-pdf', [
            'location'     => $location,
            'generated_at' => now()->format('d F Y, H:i'),
            'generated_by' => auth()->user()->name ?? 'Administrator',
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'laporan-lokasi-' . $location->slug . '-' . now()->format('Ymd') . '.pdf'
        );
    }

    /**
     * Unduh semua data lokasi sebagai Excel (4 sheet).
     * Route: GET /admin/monitoring/{id}/export-excel
     */
    public function exportExcel(string $id)
    {
        $location = $this->getLocation($id);
        $filename = 'data-lokasi-' . $location->slug . '-' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(
            fn() => (new Xlsx($this->buildSpreadsheet($location)))->save('php://output'),
            $filename,
            [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'max-age=0, no-cache, no-store',
                'Pragma'              => 'no-cache',
            ]
        );
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function getLocation(string $encodedId): MangroveLocation
    {
        return MangroveLocation::with(['details', 'images', 'damages.actions'])
            ->findOrFail(decode_id($encodedId));
    }

    private function buildSpreadsheet(MangroveLocation $loc): Spreadsheet
    {
        $book = new Spreadsheet();
        $book->getProperties()
            ->setCreator('SIKOMANG')
            ->setTitle('Data Lokasi – ' . $loc->name)
            ->setSubject('Detail Lokasi Mangrove DKI Jakarta')
            ->setDescription('Export SIKOMANG ' . now()->format('d/m/Y H:i'));

        $this->sheetInfo($book->getActiveSheet()->setTitle('Informasi Dasar'), $loc);
        $this->sheetSpecies($book->createSheet()->setTitle('Spesies'), $loc);
        $this->sheetPrograms($book->createSheet()->setTitle('Program & Pemanfaatan'), $loc);
        $this->sheetDamages($book->createSheet()->setTitle('Kerusakan'), $loc);

        $book->setActiveSheetIndex(0);
        return $book;
    }

    // ─── Sheet 1: Informasi Dasar ─────────────────────────────────────────────

    private function sheetInfo($ws, MangroveLocation $loc): void
    {
        $G  = '009966';   // primary green
        $DG = '2D5C54';   // dark green
        $W  = 'FFFFFF';
        $LG = 'E8F5F1';   // light green bg
        $GR = 'F3F4F6';   // gray row
        $wh = 'FFFFFF';

        // Title
        $ws->mergeCells('A1:C1');
        $ws->setCellValue('A1', 'DETAIL INFORMASI LOKASI MANGROVE — SIKOMANG');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $G]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(32);

        // Sub-title
        $ws->mergeCells('A2:C2');
        $ws->setCellValue('A2', $loc->name);
        $ws->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $G]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $LG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(2)->setRowHeight(24);

        // Column headers
        foreach (['A3' => 'INFORMASI', 'B3' => 'DATA', 'C3' => 'KETERANGAN'] as $cell => $label) {
            $ws->setCellValue($cell, $label);
        }
        $ws->getStyle('A3:C3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $DG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rows = [
            ['Nama Lokasi',       $loc->name,                                         ''],
            ['Koordinat',         "{$loc->latitude}, {$loc->longitude}",              'Latitude, Longitude (desimal)'],
            ['Luas Area',         $loc->area    ? "{$loc->area} ha"  : '-',           'Hektar (ha)'],
            ['Kerapatan',         ucfirst($loc->density),                              ''],
            ['Tipe Kawasan',      ucfirst(str_replace('_', '/', $loc->type)),         ''],
            ['Tingkat Kesehatan', $loc->health_percentage ? "{$loc->health_percentage}%" : '-', $loc->health_score ?? ''],
            ['Pengelola',         $loc->manager          ?? '-',                      ''],
            ['Tahun Penetapan',   $loc->year_established ?? '-',                      ''],
            ['Wilayah',           $loc->region           ?? '-',                      ''],
            ['Alamat',            $loc->location_address ?? '-',                      ''],
            ['Serapan Karbon',    $loc->carbon_data      ?? 'Data tidak tersedia',    ''],
            ['Spesies Utama',     $loc->species          ?? 'Belum diidentifikasi',   ''],
            ['Status',            $loc->is_active ? 'Aktif' : 'Tidak Aktif',         ''],
        ];

        $r = 4;
        foreach ($rows as $i => [$label, $val, $note]) {
            $bg = ($i % 2 === 0) ? $GR : $wh;
            $ws->setCellValue("A{$r}", $label);
            $ws->setCellValue("B{$r}", $val);
            $ws->setCellValue("C{$r}", $note);
            $ws->getStyle("A{$r}")->getFont()->setBold(true);
            $ws->getStyle("A{$r}:C{$r}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
            $r++;
        }

        if ($loc->description) {
            $ws->setCellValue("A{$r}", 'Deskripsi');
            $ws->mergeCells("B{$r}:C{$r}");
            $ws->setCellValue("B{$r}", $loc->description);
            $ws->getStyle("A{$r}")->getFont()->setBold(true);
            $ws->getStyle("B{$r}")->getAlignment()->setWrapText(true);
            $ws->getRowDimension($r)->setRowHeight(60);
            $r++;
        }

        // Footer note
        $r++;
        $ws->mergeCells("A{$r}:C{$r}");
        $ws->setCellValue("A{$r}", 'Digenerate oleh SIKOMANG pada ' . now()->format('d/m/Y H:i') . ' WIB');
        $ws->getStyle("A{$r}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '9CA3AF']],
        ]);

        $ws->getColumnDimension('A')->setWidth(24);
        $ws->getColumnDimension('B')->setWidth(42);
        $ws->getColumnDimension('C')->setWidth(32);
    }

    // ─── Sheet 2: Spesies ─────────────────────────────────────────────────────

    private function sheetSpecies($ws, MangroveLocation $loc): void
    {
        $DG = '2D5C54';
        $W  = 'FFFFFF';
        $G  = '009966';

        $ws->mergeCells('A1:D1');
        $ws->setCellValue('A1', 'DATA SPESIES — ' . $loc->name);
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $G]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(28);

        foreach (['A2' => 'No', 'B2' => 'Vegetasi / Flora', 'C2' => 'No', 'D2' => 'Fauna'] as $c => $l) {
            $ws->setCellValue($c, $l);
        }
        $ws->getStyle('A2:D2')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $DG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $d   = $loc->details;
        $veg = array_values(array_filter(($d && is_array($d->vegetasi)) ? $d->vegetasi : []));
        $fau = array_values(array_filter(($d && is_array($d->fauna))    ? $d->fauna    : []));
        $max = max(count($veg), count($fau), 1);

        for ($i = 0; $i < $max; $i++) {
            $r  = $i + 3;
            $bg = ($i % 2 === 0) ? 'F9FAFB' : 'FFFFFF';
            $ws->setCellValue("A{$r}", isset($veg[$i]) ? $i + 1 : '');
            $ws->setCellValue("B{$r}", $veg[$i] ?? '');
            $ws->setCellValue("C{$r}", isset($fau[$i]) ? $i + 1 : '');
            $ws->setCellValue("D{$r}", $fau[$i] ?? '');
            $ws->getStyle("A{$r}:D{$r}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
        }

        // Summary row
        $sr = $max + 3;
        $ws->setCellValue("B{$sr}", 'Total: ' . count($veg) . ' spesies vegetasi');
        $ws->setCellValue("D{$sr}", 'Total: ' . count($fau) . ' spesies fauna');
        $ws->getStyle("B{$sr}:D{$sr}")->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6B7280'));

        $ws->getColumnDimension('A')->setWidth(6);
        $ws->getColumnDimension('B')->setWidth(38);
        $ws->getColumnDimension('C')->setWidth(6);
        $ws->getColumnDimension('D')->setWidth(38);
    }

    // ─── Sheet 3: Program & Pemanfaatan ──────────────────────────────────────

    private function sheetPrograms($ws, MangroveLocation $loc): void
    {
        $DG = '2D5C54';
        $W  = 'FFFFFF';
        $G  = '009966';
        $d  = $loc->details;

        $util = array_values(array_filter(($d && is_array($d->forest_utilization)) ? $d->forest_utilization : []));
        $prog = array_values(array_filter(($d && is_array($d->programs))           ? $d->programs           : []));
        $stkh = array_values(array_filter(($d && is_array($d->stakeholders))       ? $d->stakeholders       : []));

        $ws->mergeCells('A1:C1');
        $ws->setCellValue('A1', 'PROGRAM & PEMANFAATAN — ' . $loc->name);
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $G]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(28);

        $heads = [
            'A2' => 'Pemanfaatan Hutan (' . count($util) . ')',
            'B2' => 'Program Dilaksanakan (' . count($prog) . ')',
            'C2' => 'Pihak Terkait (' . count($stkh) . ')',
        ];
        foreach ($heads as $c => $l) {
            $ws->setCellValue($c, $l);
        }
        $ws->getStyle('A2:C2')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $DG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $max = max(count($util), count($prog), count($stkh), 1);
        for ($i = 0; $i < $max; $i++) {
            $r  = $i + 3;
            $bg = ($i % 2 === 0) ? 'F9FAFB' : 'FFFFFF';
            $ws->setCellValue("A{$r}", $util[$i] ?? '');
            $ws->setCellValue("B{$r}", $prog[$i] ?? '');
            $ws->setCellValue("C{$r}", $stkh[$i] ?? '');
            $ws->getStyle("A{$r}:C{$r}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
        }

        foreach (['A' => 32, 'B' => 32, 'C' => 32] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }
    }

    // ─── Sheet 4: Kerusakan ───────────────────────────────────────────────────

    private function sheetDamages($ws, MangroveLocation $loc): void
    {
        $W  = 'FFFFFF';
        $DG = '2D5C54';

        $ws->mergeCells('A1:F1');
        $ws->setCellValue('A1', 'LAPORAN KERUSAKAN — ' . $loc->name . ' (' . $loc->damages->count() . ' laporan)');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(28);

        $headers = ['No', 'Judul', 'Deskripsi', 'Prioritas', 'Status', 'Aksi Penanganan'];
        $cols    = range('A', 'F');
        foreach ($cols as $i => $c) {
            $ws->setCellValue("{$c}2", $headers[$i]);
        }
        $ws->getStyle('A2:F2')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => $W]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $DG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        if ($loc->damages->isEmpty()) {
            $ws->mergeCells('A3:F3');
            $ws->setCellValue('A3', 'Tidak ada laporan kerusakan.');
            $ws->getStyle('A3')->applyFromArray([
                'font'      => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        } else {
            foreach ($loc->damages as $i => $dmg) {
                $r       = $i + 3;
                $bg      = ($i % 2 === 0) ? 'FFF7F7' : 'FFFFFF';
                $actions = $dmg->actions->pluck('action_description')->implode('; ');

                $ws->setCellValue("A{$r}", $i + 1);
                $ws->setCellValue("B{$r}", $dmg->title);
                $ws->setCellValue("C{$r}", $dmg->description);
                $ws->setCellValue("D{$r}", ucfirst($dmg->priority));
                $ws->setCellValue("E{$r}", ucfirst(str_replace('_', ' ', $dmg->status)));
                $ws->setCellValue("F{$r}", $actions ?: '-');

                $ws->getStyle("A{$r}:F{$r}")->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FCA5A5']]],
                ]);
                $ws->getStyle("C{$r}")->getAlignment()->setWrapText(true);
                $ws->getStyle("F{$r}")->getAlignment()->setWrapText(true);
                $ws->getRowDimension($r)->setRowHeight(40);
            }
        }

        $widths = ['A' => 6, 'B' => 30, 'C' => 45, 'D' => 12, 'E' => 18, 'F' => 45];
        foreach ($widths as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }
    }
}
