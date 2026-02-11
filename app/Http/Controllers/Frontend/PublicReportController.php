<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PublicReport;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublicReportController extends Controller
{
    /**
     * Submit new public report
     */
    public function submit(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'mangrove_location_id' => 'required|exists:mangrove_locations,id',
                'report_type' => 'required|in:kerusakan,pencemaran,penebangan_liar,kondisi_baik,lainnya',
                'urgency_level' => 'required|in:rendah,sedang,tinggi,darurat',
                'description' => 'required|string|min:20|max:2000',
                'reporter_name' => 'required|string|max:255',
                'reporter_email' => 'required|email|max:255',
                'reporter_phone' => 'required|string|max:20',
                'reporter_address' => 'nullable|string|max:500',
                'reporter_organization' => 'nullable|string|max:255',
                'photos' => 'nullable|array|max:5',
                'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120' // 5MB max per file
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle photo uploads
            $photoUrls = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('public/reports', $filename);
                    $photoUrls[] = Storage::url($path);
                }
            }

            // Create report
            $report = PublicReport::create([
                'mangrove_location_id' => $request->mangrove_location_id,
                'report_type' => $request->report_type,
                'urgency_level' => $request->urgency_level,
                'description' => $request->description,
                'reporter_name' => $request->reporter_name,
                'reporter_email' => $request->reporter_email,
                'reporter_phone' => $request->reporter_phone,
                'reporter_address' => $request->reporter_address,
                'reporter_organization' => $request->reporter_organization,
                'photo_urls' => $photoUrls,
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'report_number' => $report->report_number,
                'data' => [
                    'id' => $report->id,
                    'report_number' => $report->report_number,
                    'status' => $report->status_label
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Submit report error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search mangrove locations for autocomplete
     */
    public function searchLocations(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $locations = MangroveLocation::where('name', 'like', '%' . $query . '%')
                ->orWhere('region', 'like', '%' . $query . '%')
                ->orWhere('location_address', 'like', '%' . $query . '%')
                ->limit(10)
                ->get()
                ->map(function ($location) {
                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'region' => $location->region,
                        'display_name' => $location->name . ($location->region ? ', ' . $location->region : '')
                    ];
                });

            return response()->json($locations);
        } catch (\Exception $e) {
            \Log::error('Search locations error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Check report status by report number
     */
    public function checkStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'report_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor laporan harus diisi'
                ], 422);
            }

            $report = PublicReport::where('report_number', $request->report_number)
                ->with('location')
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'report_number' => $report->report_number,
                    'status' => $report->status_label,
                    'status_color' => $report->status_color,
                    'location' => $report->location->name,
                    'report_type' => $report->report_type_label,
                    'urgency' => $report->urgency_label,
                    'created_at' => $report->created_at->format('d F Y, H:i'),
                    'verified_at' => $report->verified_at ? $report->verified_at->format('d F Y, H:i') : null,
                    'resolved_at' => $report->resolved_at ? $report->resolved_at->format('d F Y, H:i') : null,
                    'admin_notes' => $report->admin_notes
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Check status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
}
