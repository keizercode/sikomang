<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PublicReport;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PublicReportController extends Controller
{
    /**
     * Submit new public report - FULLY DYNAMIC
     */
    public function submit(Request $request)
    {
        try {
            // Validation dengan custom messages
            $validated = $request->validate([
                'mangrove_location_id' => 'required|exists:mangrove_locations,id',
                'description' => 'required|string|min:20|max:2000',
                'report_type' => 'required|in:kerusakan,pencemaran,penebangan_liar,kondisi_baik,lainnya',
                'urgency_level' => 'required|in:rendah,sedang,tinggi,darurat',

                // Reporter information - WAJIB untuk tracking
                'reporter_name' => 'required|string|max:255',
                'reporter_email' => 'required|email|max:255',
                'reporter_phone' => 'required|string|max:20',
                'reporter_address' => 'nullable|string|max:500',
                'reporter_organization' => 'nullable|string|max:255',

                // Photos - Optional tapi ada validasi ketat
                'photos' => 'nullable|array|max:5',
                'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // Max 5MB per image
            ], [
                // Custom error messages
                'mangrove_location_id.required' => 'Lokasi mangrove wajib dipilih',
                'mangrove_location_id.exists' => 'Lokasi mangrove tidak valid',
                'description.required' => 'Deskripsi laporan wajib diisi',
                'description.min' => 'Deskripsi minimal 20 karakter',
                'description.max' => 'Deskripsi maksimal 2000 karakter',
                'report_type.required' => 'Jenis laporan wajib dipilih',
                'urgency_level.required' => 'Tingkat urgensi wajib dipilih',
                'reporter_name.required' => 'Nama pelapor wajib diisi',
                'reporter_email.required' => 'Email pelapor wajib diisi',
                'reporter_email.email' => 'Format email tidak valid',
                'reporter_phone.required' => 'Nomor telepon wajib diisi',
                'photos.max' => 'Maksimal 5 foto dapat diupload',
                'photos.*.image' => 'File harus berupa gambar',
                'photos.*.mimes' => 'Format gambar harus JPEG, PNG, atau JPG',
                'photos.*.max' => 'Ukuran foto maksimal 5MB',
            ]);

            // Handle photo uploads - DYNAMIC
            $photoUrls = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    // Generate unique filename
                    $filename = 'report_' . time() . '_' . $index . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();

                    // Store in public/storage/public-reports
                    $path = $photo->storeAs('public-reports', $filename, 'public');

                    // Save URL to array
                    $photoUrls[] = asset('storage/' . $path);
                }

                Log::info('Photos uploaded successfully', [
                    'count' => count($photoUrls),
                    'urls' => $photoUrls
                ]);
            }

            // Create report - DATA DINAMIS DARI FORM
            $report = PublicReport::create([
                'mangrove_location_id' => $validated['mangrove_location_id'],
                'description' => $validated['description'],
                'report_type' => $validated['report_type'],
                'urgency_level' => $validated['urgency_level'],
                'reporter_name' => $validated['reporter_name'],
                'reporter_email' => $validated['reporter_email'],
                'reporter_phone' => $validated['reporter_phone'],
                'reporter_address' => $validated['reporter_address'] ?? null,
                'reporter_organization' => $validated['reporter_organization'] ?? null,
                'photo_urls' => $photoUrls,
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::info('Public report submitted successfully', [
                'report_number' => $report->report_number,
                'location_id' => $report->mangrove_location_id,
                'reporter' => $report->reporter_name
            ]);

            // Return success dengan data report
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim! Nomor laporan Anda: ' . $report->report_number,
                'report_number' => $report->report_number,
                'data' => [
                    'id' => $report->id,
                    'report_number' => $report->report_number,
                    'status' => $report->status_label,
                    'created_at' => $report->formatted_date,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Public Report Submission Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim laporan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get location search results for autocomplete - DYNAMIC FROM DB
     */
    public function searchLocations(Request $request)
    {
        try {
            $query = $request->input('q', '');

            // Minimal 2 karakter untuk search
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Search dari database DINAMIS
            $locations = MangroveLocation::where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('region', 'like', "%{$query}%")
                        ->orWhere('location_address', 'like', "%{$query}%");
                })
                ->select('id', 'name', 'region', 'location_address')
                ->limit(10)
                ->get()
                ->map(function ($location) {
                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'display_name' => $location->name . ($location->region ? ' - ' . $location->region : ''),
                        'address' => $location->location_address
                    ];
                });

            return response()->json($locations);
        } catch (\Exception $e) {
            Log::error('Location search error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari lokasi'
            ], 500);
        }
    }

    /**
     * Check report status by report number - DYNAMIC FROM DB
     */
    public function checkStatus(Request $request)
    {
        try {
            $request->validate([
                'report_number' => 'required|string'
            ]);

            // Cari laporan dari database
            $report = PublicReport::where('report_number', $request->report_number)
                ->with('location')
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor laporan tidak ditemukan'
                ], 404);
            }

            // Return data DINAMIS dari database
            return response()->json([
                'success' => true,
                'data' => [
                    'report_number' => $report->report_number,
                    'location' => [
                        'id' => $report->location->id,
                        'name' => $report->location->name,
                        'region' => $report->location->region,
                    ],
                    'report_type' => $report->report_type_label,
                    'urgency' => $report->urgency_label,
                    'status' => $report->status_label,
                    'status_color' => $report->status_color,
                    'description' => $report->description,
                    'reporter_name' => $report->reporter_name,
                    'has_photos' => $report->hasPhotos(),
                    'photo_count' => $report->hasPhotos() ? count($report->photo_urls) : 0,
                    'created_at' => $report->formatted_date,
                    'admin_notes' => $report->admin_notes,
                    'verified_at' => $report->verified_at ? $report->verified_at->format('d M Y H:i') : null,
                    'resolved_at' => $report->resolved_at ? $report->resolved_at->format('d M Y H:i') : null,
                    'is_resolved' => $report->isResolved(),
                    'is_urgent' => $report->isUrgent(),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor laporan wajib diisi',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Check status error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengecek status'
            ], 500);
        }
    }

    /**
     * Get public statistics - DYNAMIC
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_reports' => PublicReport::count(),
                'pending_reports' => PublicReport::pending()->count(),
                'verified_reports' => PublicReport::verified()->count(),
                'resolved_reports' => PublicReport::resolved()->count(),
                'active_reports' => PublicReport::active()->count(),
                'urgent_reports' => PublicReport::whereIn('urgency_level', ['tinggi', 'darurat'])->count(),
                'reports_this_month' => PublicReport::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'reports_today' => PublicReport::whereDate('created_at', today())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Statistics error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }

    /**
     * Get recent reports for public view - DYNAMIC
     */
    public function recentReports(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $reports = PublicReport::with('location')
                ->whereIn('status', ['verified', 'in_progress', 'resolved'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($report) {
                    return [
                        'report_number' => $report->report_number,
                        'location_name' => $report->location->name,
                        'report_type' => $report->report_type_label,
                        'urgency' => $report->urgency_label,
                        'status' => $report->status_label,
                        'created_at' => $report->formatted_date,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $reports
            ]);
        } catch (\Exception $e) {
            Log::error('Recent reports error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data laporan'
            ], 500);
        }
    }
}
