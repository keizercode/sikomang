<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PublicReport;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PublicReportController extends Controller
{
    /**
     * 🔧 IMPROVED: Submit new public report with better photo handling
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

            // 🔧 IMPROVED: Handle photo uploads with consistent path
            $photoUrls = [];

            if ($request->hasFile('photos')) {
                Log::info('Processing ' . count($request->file('photos')) . ' photo uploads');

                foreach ($request->file('photos') as $index => $photo) {
                    try {
                        // Generate unique filename
                        $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

                        // Store in public disk
                        $path = $photo->storeAs('public_reports', $filename, 'public');

                        // Store as relative path (consistent format)
                        $photoUrls[] = '/storage/' . $path;

                        Log::info("Uploaded photo #{$index}: {$filename}");
                    } catch (\Exception $e) {
                        Log::error("Error uploading photo #{$index}: " . $e->getMessage());
                        // Continue with other photos
                    }
                }

                Log::info('Successfully uploaded ' . count($photoUrls) . ' photos');
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

            Log::info("Report created: {$report->report_number} with " . count($photoUrls) . " photos");

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'report_number' => $report->report_number,
                'data' => [
                    'id' => $report->id,
                    'report_number' => $report->report_number,
                    'status' => $report->status_label,
                    'photos_uploaded' => count($photoUrls)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Submit report error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ FIXED: Field sesuai MangroveLocation model
     */
    public function searchLocations(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Pecah berdasarkan spasi
            $keywords = explode(' ', trim($query));

            $locations = MangroveLocation::where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $word = strtolower($word);

                    $q->where(function ($subQuery) use ($word) {
                        $subQuery->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(region) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(location_address) LIKE ?', ["%{$word}%"])
                            ->orWhereRaw('LOWER(manager) LIKE ?', ["%{$word}%"]);
                    });
                }
            })
                ->where('is_active', true)
                ->orderBy('name', 'asc')
                ->limit(10)
                ->get()
                ->map(function ($location) {
                    $displayParts = [$location->name];

                    if ($location->region) {
                        $displayParts[] = $location->region;
                    }

                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'region' => $location->region ?? '',
                        'location_address' => $location->location_address ?? '',
                        'display_name' => implode(' - ', $displayParts)
                    ];
                });

            Log::info('✅ Found ' . $locations->count() . ' locations');

            return response()->json($locations);
        } catch (\Exception $e) {
            Log::error('❌ Search error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

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
            Log::error('Check status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
}
