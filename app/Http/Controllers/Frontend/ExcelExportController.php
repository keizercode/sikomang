<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ExcelExportController extends FrontendController
{
    /**
     * Export mangrove data to Excel based on density category
     */
    public function exportMangroveData($category)
    {
        // Validate category
        $validCategories = ['jarang', 'sedang', 'lebat'];
        if (!in_array(strtolower($category), $validCategories)) {
            abort(400, 'Invalid category');
        }

        // Get data based on category
        $data = $this->getMangroveDataByCategory($category);

        // Generate Excel file
        $excelPath = $this->generateExcelFile($category, $data);

        // Return file for download
        return response()->download($excelPath, "Mangrove_Jakarta_{$category}_" . date('Y-m-d') . ".xlsx")->deleteFileAfterSend(true);
    }

    /**
     * Get mangrove data based on category
     */
    private function getMangroveDataByCategory($category)
    {
        // Data mapping untuk setiap kategori
        $allData = [
            'jarang' => [
                ['location' => 'Rawa Hutan Lindung', 'area' => 44.7, 'coords' => '-6.1023, 106.7655', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'HL', 'konservasi' => 'Kawasan Konservasi'],
                ['location' => 'Pos 5 Hutan Lindung', 'area' => 4.7, 'coords' => '-6.0895, 106.7820', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'HL', 'konservasi' => 'Kawasan Konservasi'],
                ['location' => 'Rusun TNI AL', 'area' => 6.0, 'coords' => '-6.0912, 106.9105', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'APL', 'konservasi' => 'Bukan Kawasan Konservasi'],
            ],
            'sedang' => [
                ['location' => 'Tanah Timbul (Bird Feeding)', 'area' => 2.89, 'coords' => '-6.1012, 106.7645', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'APL', 'konservasi' => 'Bukan Kawasan Konservasi'],
                ['location' => 'Pos 2 Hutan Lindung', 'area' => null, 'coords' => '-6.1025, 106.7680', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'HL', 'konservasi' => 'Kawasan Konservasi'],
                ['location' => 'TWA Angke Kapuk', 'area' => 99.82, 'coords' => '-6.0921, 106.7590', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'TWA', 'konservasi' => 'Kawasan Konservasi'],
            ],
            'lebat' => [
                ['location' => 'Titik 2 Elang Laut', 'area' => null, 'coords' => '-6.1015, 106.7670', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'HL', 'konservasi' => 'Kawasan Konservasi'],
                ['location' => 'Mangrove STIP', 'area' => 4.6, 'coords' => '-6.1223, 106.9512', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'APL', 'konservasi' => 'Bukan Kawasan Konservasi'],
                ['location' => 'Mangrove Si Pitung', 'area' => 5.5, 'coords' => '-6.1198, 106.8645', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'APL', 'konservasi' => 'Bukan Kawasan Konservasi'],
                ['location' => 'Pasmar 1 TNI AL', 'area' => 5.5, 'coords' => '-6.1156, 106.8598', 'wadmkk' => 'Kota Adm. Jakarta Utara', 'kawasan' => 'APL', 'konservasi' => 'Bukan Kawasan Konservasi'],
            ],
        ];

        return $allData[strtolower($category)] ?? [];
    }

    /**
     * Get Python command based on operating system
     */
    private function getPythonCommand()
    {
        // Check if running on Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'python'; // Windows uses 'python'
        }

        return 'python3'; // Linux/Unix uses 'python3'
    }

    /**
     * Generate Excel file using Python script
     */
    private function generateExcelFile($category, $data)
    {
        // Adjust template path based on OS
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            // On Windows, use Laravel storage path to access uploaded file
            // You need to copy the template file to storage/app/templates directory
            $templatePath = storage_path('app/templates/export-map-Mangrove_Jakarta_-_Jarang__2_.xlsx');
        } else {
            // On Linux (production), use the original path
            $templatePath = '/mnt/user-data/uploads/export-map-Mangrove_Jakarta_-_Jarang__2_.xlsx';
        }

        $outputPath = storage_path("app/exports/Mangrove_Jakarta_{$category}_" . time() . ".xlsx");

        // Create exports directory if not exists
        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        // Create templates directory if not exists (for Windows)
        if ($isWindows && !is_dir(storage_path('app/templates'))) {
            mkdir(storage_path('app/templates'), 0755, true);
        }

        // Create Python script content
        $pythonScript = $this->generatePythonScript($templatePath, $outputPath, $category, $data);

        // Save Python script to temp file
        $scriptPath = storage_path('app/exports/generate_excel_' . time() . '.py');
        file_put_contents($scriptPath, $pythonScript);

        // Get correct Python command
        $pythonCommand = $this->getPythonCommand();

        // Execute Python script
        $process = new Process([$pythonCommand, $scriptPath]);
        $process->setTimeout(60);
        $process->run();

        // Clean up script file
        @unlink($scriptPath);

        // Check if process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $outputPath;
    }

    /**
     * Generate Python script for creating Excel file
     */
    private function generatePythonScript($templatePath, $outputPath, $category, $data)
    {
        $dataJson = json_encode($data, JSON_PRETTY_PRINT);

        // Escape backslashes for Windows paths
        $templatePath = str_replace('\\', '\\\\', $templatePath);
        $outputPath = str_replace('\\', '\\\\', $outputPath);

        return <<<PYTHON
import openpyxl
from openpyxl import load_workbook
import json
from datetime import datetime

# Load template
wb = load_workbook('{$templatePath}')
sheet = wb['Data']

# Clear existing data (keep header)
for row in range(sheet.max_row, 1, -1):
    sheet.delete_rows(row)

# Data to insert
data = {$dataJson}

# Category mapping
category_map = {
    'jarang': 'MANGROVE JARANG',
    'sedang': 'MANGROVE SEDANG',
    'lebat': 'MANGROVE LEBAT'
}

# Insert data
for i, item in enumerate(data, start=2):
    coords = item['coords'].split(',')
    lat = float(coords[0].strip()) if len(coords) > 0 else 0
    lon = float(coords[1].strip()) if len(coords) > 1 else 0

    sheet[f'A{i}'] = 'CITARUM CILIWUNG'
    sheet[f'B{i}'] = category_map['{$category}']
    sheet[f'C{i}'] = 'CITRA PLANETSCOPE DAN SENTINEL 2 TAHUN 2024'
    sheet[f'D{i}'] = '2024'
    sheet[f'E{i}'] = 'KLHK'
    sheet[f'F{i}'] = item['location']
    sheet[f'G{i}'] = 'DOMINASI POHON'
    sheet[f'H{i}'] = item['area'] if item['area'] is not None else 0
    sheet[f'I{i}'] = abs(lat) * 0.001  # Shape_Leng (dummy calculation)
    sheet[f'J{i}'] = abs(lat * lon) * 0.0001  # Shape_Area (dummy calculation)
    sheet[f'K{i}'] = 31
    sheet[f'L{i}'] = item.get('fungsikws', 100100)
    sheet[f'M{i}'] = '220/Kpts-II/2000'
    sheet[f'N{i}'] = '2000-08-02 00:00:00'
    sheet[f'O{i}'] = 66401
    sheet[f'P{i}'] = item['kawasan']
    sheet[f'Q{i}'] = item['konservasi']
    sheet[f'R{i}'] = item['wadmkk']
    sheet[f'S{i}'] = 'DKI Jakarta'
    sheet[f'T{i}'] = None
    sheet[f'U{i}'] = 0

# Save file
wb.save('{$outputPath}')
print("Excel file generated successfully")
PYTHON;
    }
}
