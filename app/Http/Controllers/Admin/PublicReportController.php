<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicReport;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PublicReportController extends Controller
{
    /**
     * Display listing of public reports
     */
    public function index()
    {
        $data['title'] = 'Laporan Masyarakat';
        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Laporan Masyarakat', 'active' => true],
        ];

        return view('pages.admin.public-reports.index', $data);
    }

    /**
     * Get grid data for DataTables
     */
    public function grid(Request $request)
    {
        $reports = PublicReport::with(['location', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $reports->map(function ($report, $index) {
            return [
                'no' => $index + 1,
                'report_number' => $report->report_number,
                'location' => $report->location->name ?? 'N/A',
                'reporter_name' => $report->reporter_name,
                'reporter_email' => $report->reporter_email,
                'report_type' => '<span class="badge bg-' . $this->getTypeColor($report->report_type) . '">' . $report->report_type_label . '</span>',
                'urgency' => '<span class="badge bg-' . $report->urgency_color . '">' . $report->urgency_label . '</span>',
                'status' => '<span class="badge bg-' . $report->status_color . '">' . $report->status_label . '</span>',
                'created_at' => $report->created_at->format('d M Y H:i'),
                'action' => $this->generateActionButtons($report)
            ];
        })->toArray();

        return response()->json($data);
    }

    /**
     * Show report detail
     */
    public function show($id)
    {
        $keyId = decode_id($id);
        $report = PublicReport::with(['location', 'verifier'])->findOrFail($keyId);

        // 🔧 FIX: Normalize photo URLs for display
        if ($report->photo_urls && is_array($report->photo_urls)) {
            $report->photo_urls = array_map(function ($url) {
                // If it's a full URL, extract the path
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    return parse_url($url, PHP_URL_PATH);
                }

                // If it doesn't start with /, add it
                if (!str_starts_with($url, '/')) {
                    return '/' . $url;
                }

                return $url;
            }, $report->photo_urls);
        }

        $data['title'] = 'Detail Laporan: ' . $report->report_number;
        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Laporan Masyarakat', 'url' => route('admin.public-reports.index')],
            ['name' => 'Detail', 'active' => true],
        ];
        $data['report'] = $report;
        $data['keyId'] = $id;

        return view('pages.admin.public-reports.detail', $data);
    }

    /**
     * Verify report
     */
    public function verify(Request $request, $id)
    {
        try {
            $keyId = decode_id($id);
            $report = PublicReport::findOrFail($keyId);

            $report->update([
                'status' => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            Log::info("Report verified: {$report->report_number} by " . Auth::user()->name);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diverifikasi',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Verify report error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi laporan',
                'type' => 'error'
            ], 500);
        }
    }

    /**
     * Update report status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $keyId = decode_id($id);
            $report = PublicReport::findOrFail($keyId);

            $validated = $request->validate([
                'status' => 'required|in:pending,verified,in_review,in_progress,resolved,rejected'
            ]);

            $updateData = ['status' => $validated['status']];

            // Auto-set resolved_at if status is resolved
            if ($validated['status'] === 'resolved' && !$report->resolved_at) {
                $updateData['resolved_at'] = now();
            }

            $report->update($updateData);

            Log::info("Report status updated: {$report->report_number} to {$validated['status']}");

            return response()->json([
                'success' => true,
                'message' => 'Status laporan berhasil diperbarui',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Update status error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status',
                'type' => 'error'
            ], 500);
        }
    }

    /**
     * Add admin note to report
     */
    public function addNote(Request $request, $id)
    {
        try {
            $keyId = decode_id($id);
            $report = PublicReport::findOrFail($keyId);

            $validated = $request->validate([
                'admin_notes' => 'required|string|max:2000'
            ]);

            $report->update([
                'admin_notes' => $validated['admin_notes']
            ]);

            Log::info("Note added to report: {$report->report_number}");

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil ditambahkan',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Add note error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan catatan',
                'type' => 'error'
            ], 500);
        }
    }

    /**
     * 🔧 IMPROVED: Delete report with proper file cleanup
     */
    public function destroy($id)
    {
        try {
            $keyId = decode_id($id);

            // Use withTrashed to get even soft-deleted records
            $report = PublicReport::withTrashed()->findOrFail($keyId);

            // Delete associated photos if any
            if ($report->photo_urls && is_array($report->photo_urls)) {
                foreach ($report->photo_urls as $photoUrl) {
                    // Extract filename from various URL formats
                    $filename = basename(parse_url($photoUrl, PHP_URL_PATH));
                    $path = "public_reports/{$filename}";

                    // Delete from storage
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                        Log::info("Deleted photo: {$path}");
                    }
                }
            }

            // Force delete (permanent deletion, bypass soft delete)
            $reportNumber = $report->report_number;
            $report->forceDelete();

            Log::info("Report permanently deleted: {$reportNumber}");

            return response()->json([
                'success' => true,
                'message' => 'Laporan dan semua file terkait berhasil dihapus',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete report error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus laporan: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }

    /**
     * Get report type color for badge
     */
    private function getTypeColor($type)
    {
        return [
            'kerusakan' => 'danger',
            'pencemaran' => 'warning',
            'penebangan_liar' => 'dark',
            'kondisi_baik' => 'success',
            'lainnya' => 'secondary'
        ][$type] ?? 'secondary';
    }

    /**
     * Generate action buttons for grid
     */
    private function generateActionButtons($report)
    {
        $encodedId = encode_id($report->id);

        $html = '<div class="d-flex gap-1">';

        // Detail button
        $html .= '<a href="' . route('admin.public-reports.detail', $encodedId) . '"
                    class="btn btn-sm btn-outline-primary" title="Detail">
                    <i class="mdi mdi-eye"></i>
                  </a>';

        // Verify button (if pending)
        if ($report->status === 'pending') {
            $html .= '<button class="btn btn-sm btn-outline-success verify-btn"
                        data-id="' . $encodedId . '" title="Verifikasi">
                        <i class="mdi mdi-check-circle"></i>
                      </button>';
        }

        // Delete button
        $html .= '<button class="btn btn-sm btn-outline-danger delete-report"
                    data-id="' . $encodedId . '" title="Hapus">
                    <i class="mdi mdi-delete"></i>
                  </button>';

        $html .= '</div>';

        return $html;
    }
}
