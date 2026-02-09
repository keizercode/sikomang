<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicReport;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diverifikasi',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Verify report error: ' . $e->getMessage());

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

            return response()->json([
                'success' => true,
                'message' => 'Status laporan berhasil diperbarui',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Update status error: ' . $e->getMessage());

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

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil ditambahkan',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Add note error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan catatan',
                'type' => 'error'
            ], 500);
        }
    }

    /**
     * Delete report
     */
    public function destroy($id)
    {
        try {
            $keyId = decode_id($id);
            $report = PublicReport::findOrFail($keyId);

            // Delete associated photos if any
            if ($report->photo_urls && is_array($report->photo_urls)) {
                foreach ($report->photo_urls as $photoUrl) {
                    if (str_contains($photoUrl, 'storage/')) {
                        $path = str_replace(asset('storage/'), '', $photoUrl);
                        \Storage::disk('public')->delete($path);
                    }
                }
            }

            $report->delete();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dihapus',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete report error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus laporan',
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
