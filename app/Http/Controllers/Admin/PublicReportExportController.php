<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicReport;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicReportExportController extends Controller
{
    /**
     * Export public reports to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = PublicReport::with(['location', 'verifier']);

            // Apply filters if provided
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('report_type') && $request->report_type) {
                $query->where('report_type', $request->report_type);
            }

            if ($request->has('urgency_level') && $request->urgency_level) {
                $query->where('urgency_level', $request->urgency_level);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $reports = $query->orderBy('created_at', 'desc')->get();

            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('SIKOMANG')
                ->setTitle('Laporan Masyarakat')
                ->setSubject('Data Laporan Kondisi Mangrove')
                ->setDescription('Export data laporan dari masyarakat tentang kondisi mangrove');

            // Header styling
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '009966']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            // Headers
            $headers = [
                'No',
                'No. Laporan',
                'Tanggal Laporan',
                'Lokasi Mangrove',
                'Wilayah',
                'Jenis Laporan',
                'Tingkat Urgensi',
                'Status',
                'Nama Pelapor',
                'Email',
                'Telepon',
                'Alamat',
                'Organisasi',
                'Deskripsi',
                'Jumlah Foto',
                'Catatan Admin',
                'Diverifikasi Oleh',
                'Tanggal Verifikasi',
                'Tanggal Selesai',
                'IP Address'
            ];

            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
                $sheet->getColumnDimension($column)->setAutoSize(true);
                $column++;
            }

            // Data rows
            $row = 2;
            foreach ($reports as $index => $report) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $report->report_number);
                $sheet->setCellValue('C' . $row, $report->created_at->format('d/m/Y H:i'));
                $sheet->setCellValue('D' . $row, $report->location->name ?? 'N/A');
                $sheet->setCellValue('E' . $row, $report->location->region ?? 'N/A');
                $sheet->setCellValue('F' . $row, $report->report_type_label);
                $sheet->setCellValue('G' . $row, $report->urgency_label);
                $sheet->setCellValue('H' . $row, $report->status_label);
                $sheet->setCellValue('I' . $row, $report->reporter_name);
                $sheet->setCellValue('J' . $row, $report->reporter_email);
                $sheet->setCellValue('K' . $row, $report->reporter_phone);
                $sheet->setCellValue('L' . $row, $report->reporter_address ?? '-');
                $sheet->setCellValue('M' . $row, $report->reporter_organization ?? '-');
                $sheet->setCellValue('N' . $row, $report->description);
                $sheet->setCellValue('O' . $row, $report->hasPhotos() ? count($report->photo_urls) : 0);
                $sheet->setCellValue('P' . $row, $report->admin_notes ?? '-');
                $sheet->setCellValue('Q' . $row, $report->verifier->name ?? '-');
                $sheet->setCellValue('R' . $row, $report->verified_at ? $report->verified_at->format('d/m/Y H:i') : '-');
                $sheet->setCellValue('S' . $row, $report->resolved_at ? $report->resolved_at->format('d/m/Y H:i') : '-');
                $sheet->setCellValue('T' . $row, $report->ip_address);

                // Row styling
                $sheet->getStyle('A' . $row . ':T' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP
                    ]
                ]);

                // Wrap text for description column
                $sheet->getStyle('N' . $row)->getAlignment()->setWrapText(true);

                $row++;
            }

            // Set row heights
            for ($i = 2; $i < $row; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
            }

            // Freeze first row
            $sheet->freezePane('A2');

            // Create Excel file
            $filename = 'laporan-masyarakat-' . date('Y-m-d-His') . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            \Log::error('Export Excel error: ' . $e->getMessage());
            return redirect()->back()->with([
                'message' => 'Gagal mengexport data: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Export public reports to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $query = PublicReport::with(['location', 'verifier']);

            // Apply filters
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('report_type') && $request->report_type) {
                $query->where('report_type', $request->report_type);
            }

            if ($request->has('urgency_level') && $request->urgency_level) {
                $query->where('urgency_level', $request->urgency_level);
            }

            $reports = $query->orderBy('created_at', 'desc')->get();

            $data = [
                'reports' => $reports,
                'title' => 'Laporan Masyarakat',
                'generated_at' => now()->format('d F Y H:i'),
                'total' => $reports->count()
            ];

            $pdf = Pdf::loadView('admin.reports.export-pdf', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = 'laporan-masyarakat-' . date('Y-m-d-His') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Export PDF error: ' . $e->getMessage());
            return redirect()->back()->with([
                'message' => 'Gagal mengexport PDF: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Export public reports to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $query = PublicReport::with(['location', 'verifier']);

            // Apply filters
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('report_type') && $request->report_type) {
                $query->where('report_type', $request->report_type);
            }

            $reports = $query->orderBy('created_at', 'desc')->get();

            $filename = 'laporan-masyarakat-' . date('Y-m-d-His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($reports) {
                $file = fopen('php://output', 'w');

                // Add BOM for proper UTF-8 encoding in Excel
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Headers
                fputcsv($file, [
                    'No. Laporan',
                    'Tanggal',
                    'Lokasi',
                    'Jenis',
                    'Urgensi',
                    'Status',
                    'Pelapor',
                    'Email',
                    'Telepon',
                    'Deskripsi'
                ]);

                // Data
                foreach ($reports as $report) {
                    fputcsv($file, [
                        $report->report_number,
                        $report->created_at->format('d/m/Y H:i'),
                        $report->location->name ?? 'N/A',
                        $report->report_type_label,
                        $report->urgency_label,
                        $report->status_label,
                        $report->reporter_name,
                        $report->reporter_email,
                        $report->reporter_phone,
                        $report->description
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Export CSV error: ' . $e->getMessage());
            return redirect()->back()->with([
                'message' => 'Gagal mengexport CSV: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}
