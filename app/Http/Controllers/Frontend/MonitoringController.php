<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    /**
     * Halaman profil sebaran mangrove.
     */
    public function index(Request $request)
    {
        try {
            $locations = MangroveLocation::where('is_active', true)
                ->with(['details', 'images', 'damages.actions'])
                ->orderBy('id', 'asc')
                ->get();

            $monitoring_data = $this->calculateMonitoringData();

            $formattedLocations = $locations->map(
                fn($loc) => $this->formatLocationForFrontend($loc)
            )->toArray();

            return view('pages.frontend.monitoring', [
                'title'           => 'Profil Sebaran Mangrove DKI Jakarta 2025',
                'total_sites'     => $locations->count(),
                'total_area'      => number_format($locations->sum('area'), 2),
                'monitoring_data' => $monitoring_data,
                'locations'       => $formattedLocations,
            ]);
        } catch (\Exception $e) {
            \Log::error('MonitoringController@index: ' . $e->getMessage());

            return view('pages.frontend.monitoring', [
                'title'           => 'Profil Sebaran Mangrove DKI Jakarta 2025',
                'total_sites'     => 0,
                'total_area'      => '0.00',
                'monitoring_data' => ['luar_kawasan' => [], 'dalam_kawasan' => [], 'totals' => null],
                'locations'       => [],
            ]);
        }
    }

    /**
     * Halaman hasil pemantauan.
     */
    public function hasilPemantauan()
    {
        try {
            $locations = MangroveLocation::where('is_active', true)
                ->with(['details', 'images', 'damages.actions'])
                ->orderBy('id', 'asc')
                ->get();

            $regionStats = [
                'penjaringan'        => $locations->filter(fn($l) => stripos($l->region ?? '', 'penjaringan') !== false)->count(),
                'cilincing'          => $locations->filter(fn($l) => stripos($l->region ?? '', 'cilincing') !== false)->count(),
                'kep_seribu_utara'   => $locations->filter(fn($l) => stripos($l->region ?? '', 'seribu utara') !== false)->count(),
                'kep_seribu_selatan' => $locations->filter(fn($l) => stripos($l->region ?? '', 'seribu selatan') !== false)->count(),
            ];

            $typeStats = [
                'dilindungi'              => $locations->where('type', 'dilindungi')->count(),
                'pengkayaan'              => $locations->where('type', 'pengkayaan')->count(),
                'pengkayaan_rehabilitasi' => $locations->where('type', 'pengkayaan_rehabilitasi')->count(),
                'rehabilitasi'            => $locations->where('type', 'rehabilitasi')->count(),
            ];

            return view('pages.frontend.hasil-pemantauan', [
                'locations'   => $locations->map(fn($l) => $this->formatLocationForFrontend($l))->toArray(),
                'totalSites'  => $locations->count(),
                'totalArea'   => number_format($locations->sum('area'), 2),
                'regionStats' => $regionStats,
                'typeStats'   => $typeStats,
            ]);
        } catch (\Exception $e) {
            \Log::error('MonitoringController@hasilPemantauan: ' . $e->getMessage());

            return view('pages.frontend.hasil-pemantauan', [
                'locations'   => [],
                'totalSites'  => 0,
                'totalArea'   => '0.00',
                'regionStats' => ['penjaringan' => 0, 'cilincing' => 0, 'kep_seribu_utara' => 0, 'kep_seribu_selatan' => 0],
                'typeStats'   => ['dilindungi' => 0, 'pengkayaan' => 0, 'rehabilitasi' => 0, 'pengkayaan_rehabilitasi' => 0],
            ]);
        }
    }

    /**
     * Detail lokasi pemantauan.
     */
    public function detailLokasi(string $slug)
    {
        try {
            $location = MangroveLocation::where('slug', $slug)
                ->where('is_active', true)
                ->with(['details', 'images', 'damages.actions'])
                ->firstOrFail();

            return view('pages.frontend.detail-lokasi', [
                'location' => $this->formatLocationForFrontend($location),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            abort(404, 'Lokasi tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('MonitoringController@detailLokasi: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat data lokasi');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PRIVATE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Hitung data monitoring dari database.
     *
     * Menggunakan operator JSON PostgreSQL `geojson_properties->>'Kawasan'`
     * agar klasifikasi persis sesuai data GIS Plovis (APL, HL, HP, TN, SM, TWA).
     *
     * Mengapa query DB bukan filter Collection?
     * → Aggregate (SUM) lebih efisien di database, bukan di PHP memory.
     */
    private function calculateMonitoringData(): array
    {
        // Urutan tampil + konfigurasi setiap jenis kawasan
        $kawasanDef = [
            'APL' => [
                'section'    => 'luar_kawasan',
                'fungsi'     => 'APL (Areal Penggunaan Lain)',
                'konservasi' => 'Bukan Kawasan Konservasi',
            ],
            'HL' => [
                'section'    => 'dalam_kawasan',
                'fungsi'     => 'HL (Hutan Lindung)',
                'konservasi' => 'Kawasan Konservasi',
            ],
            'HP' => [
                'section'    => 'dalam_kawasan',
                'fungsi'     => 'HP (Hutan Produksi)',
                'konservasi' => 'Bukan Kawasan Konservasi',
            ],
            'TN' => [
                'section'    => 'dalam_kawasan',
                'fungsi'     => 'TN (Taman Nasional)',
                'konservasi' => 'Kawasan Konservasi',
            ],
            'SM' => [
                'section'    => 'dalam_kawasan',
                'fungsi'     => 'SM (Suaka Margasatwa)',
                'konservasi' => 'Kawasan Konservasi',
            ],
            'TWA' => [
                'section'    => 'dalam_kawasan',
                'fungsi'     => 'TWA (Taman Wisata Alam)',
                'konservasi' => 'Kawasan Konservasi',
            ],
        ];

        // Aggregate dari DB: kawasan (dari JSON properties) × density → total_area
        try {
            $rows = DB::table('mangrove_locations')
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->whereNotNull('geojson_properties')
                ->selectRaw("
                    UPPER(TRIM(geojson_properties->>'Kawasan')) AS kawasan,
                    LOWER(TRIM(density))                        AS density,
                    ROUND(CAST(SUM(COALESCE(area, 0)) AS numeric), 2) AS total_area
                ")
                ->groupByRaw("
                    UPPER(TRIM(geojson_properties->>'Kawasan')),
                    LOWER(TRIM(density))
                ")
                ->whereRaw("NULLIF(TRIM(geojson_properties->>'Kawasan'), '') IS NOT NULL")
                ->get();
        } catch (\Exception $e) {
            \Log::error('calculateMonitoringData DB error: ' . $e->getMessage());
            $rows = collect();
        }

        // Pivot: [KAWASAN][density] = luas
        $pivot = [];
        foreach ($rows as $r) {
            $kw      = strtoupper(trim($r->kawasan ?? ''));
            $density = strtolower(trim($r->density ?? ''));
            if ($kw && $density) {
                $pivot[$kw][$density] = (float) $r->total_area;
            }
        }

        $data = ['luar_kawasan' => [], 'dalam_kawasan' => [], 'totals' => null];
        $grandByDensity = ['jarang' => 0.0, 'sedang' => 0.0, 'lebat' => 0.0];

        foreach ($kawasanDef as $kw => $def) {
            $jarang = $pivot[$kw]['jarang'] ?? 0.0;
            $sedang = $pivot[$kw]['sedang'] ?? 0.0;
            $lebat  = $pivot[$kw]['lebat']  ?? 0.0;
            $total  = $jarang + $sedang + $lebat;

            if ($total <= 0) {
                continue; // Sembunyikan baris kosong
            }

            $grandByDensity['jarang'] += $jarang;
            $grandByDensity['sedang'] += $sedang;
            $grandByDensity['lebat']  += $lebat;

            $data[$def['section']][] = [
                'fungsi'     => $def['fungsi'],
                'jarang'     => number_format($jarang, 2),
                'sedang'     => number_format($sedang, 2),
                'lebat'      => number_format($lebat, 2),
                'total'      => number_format($total, 2),
                'konservasi' => $def['konservasi'],
            ];
        }

        // Grand total
        $grandTotal = array_sum($grandByDensity);
        if ($grandTotal > 0) {
            $data['totals'] = [
                'jarang' => number_format($grandByDensity['jarang'], 2),
                'sedang' => number_format($grandByDensity['sedang'], 2),
                'lebat'  => number_format($grandByDensity['lebat'], 2),
                'total'  => number_format($grandTotal, 2),
            ];
        }

        return $data;
    }

    private function formatLocationForFrontend($location): array
    {
        $details = $location->details;

        return [
            'id'                 => $location->id,
            'slug'               => $location->slug,
            'name'               => $location->name,
            'type'               => ucfirst($location->type),
            'year'               => $location->year_established ?? date('Y'),
            'area'               => $location->area ? number_format($location->area, 2) . ' ha' : 'Belum diidentifikasi',
            'density'            => strtolower($location->density),
            'health'             => $location->health_percentage ? $location->health_percentage . '% Sehat' : 'N/A',
            'health_score'       => $location->health_score ?? 'N/A',
            'coords'             => $location->latitude . ', ' . $location->longitude,
            'latitude'           => (float) $location->latitude,
            'longitude'          => (float) $location->longitude,
            'location'           => $location->location_address ?? $location->region ?? 'DKI Jakarta',
            'group'              => $this->determineGroup($location),
            'manager'            => $location->manager ?? 'DPHK',
            'species'            => $location->species ?? 'Belum diidentifikasi',
            'damage_count'       => $location->damages->whereIn('status', ['pending', 'in_progress'])->count(),
            'carbon_data'        => $location->carbon_data ?? 'Data tidak tersedia',
            'certificate_status' => 'Tidak tersedia sertifikat',
            'description'        => $location->description ?? 'Tidak ada deskripsi',
            'images'             => $location->images->pluck('image_url')->toArray(),
            'damages'            => $location->damages->pluck('title')->toArray(),
            'actions'            => $location->damages->flatMap(fn($d) => $d->actions->pluck('action_description'))->toArray(),
            'species_detail'     => $details ? [
                'vegetasi' => is_array($details->vegetasi) ? $details->vegetasi : (json_decode($details->vegetasi, true) ?? []),
                'fauna'    => is_array($details->fauna)    ? $details->fauna    : (json_decode($details->fauna, true)    ?? []),
            ] : null,
            'activities'         => $details ? (is_array($details->activities)         ? $details->activities         : json_decode($details->activities, true))         : null,
            'forest_utilization' => $details ? (is_array($details->forest_utilization) ? $details->forest_utilization : json_decode($details->forest_utilization, true)) : null,
            'programs'           => $details ? (is_array($details->programs)           ? $details->programs           : json_decode($details->programs, true))           : null,
            'stakeholders'       => $details ? (is_array($details->stakeholders)       ? $details->stakeholders       : json_decode($details->stakeholders, true))       : null,
        ];
    }

    private function determineGroup($location): string
    {
        $region = strtolower($location->region ?? '');

        if (str_contains($region, 'penjaringan'))                              return 'penjaringan';
        if (str_contains($region, 'cilincing'))                                return 'cilincing';
        if (str_contains($region, 'seribu utara') || str_contains($region, 'kepulauan seribu utara'))   return 'kep-seribu-utara';
        if (str_contains($region, 'seribu selatan') || str_contains($region, 'kepulauan seribu selatan')) return 'kep-seribu-selatan';

        return 'all';
    }
}
