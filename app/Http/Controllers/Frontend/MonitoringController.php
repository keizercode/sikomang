<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    /**
     * URL sumber GeoJSON Plovis — sama persis dengan GeoJsonApiController & ExcelExportController
     */
    private const PLOVIS_URLS = [
        'jarang' => 'https://asset.plovis.id/plovis/public/67f25022-a757-4f90-a114-16e3f3ad671c.geojson',
        'sedang' => 'https://asset.plovis.id/plovis/public/1c7b760f-7458-4353-bfd9-1ba6084cdce6.geojson',
        'lebat'  => 'https://asset.plovis.id/plovis/public/cb7b89d7-2ac7-4fa4-a16c-02734432838e.geojson',
    ];

    /**
     * Definisi kawasan: urutan tampil, section, label, & status konservasi.
     * Key = nilai field `Kawasan` di Plovis (case-insensitive match).
     */
    private const KAWASAN_DEF = [
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

    // ─────────────────────────────────────────────────────────────────────────

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
            Log::error('MonitoringController@index: ' . $e->getMessage());

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
            Log::error('MonitoringController@hasilPemantauan: ' . $e->getMessage());

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
            Log::error('MonitoringController@detailLokasi: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat data lokasi');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PRIVATE — Kalkulasi Data Tabel
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Hitung data tabel monitoring.
     *
     * Mengambil data LANGSUNG dari Plovis API (sumber yang sama dengan peta & Excel export)
     * sehingga angka tabel selalu sinkron dengan data GIS.
     *
     * Cache 1 jam agar tidak tiap page-load hit Plovis.
     * Fallback ke query DB jika Plovis tidak tersedia.
     */
    private function calculateMonitoringData(): array
    {
        return Cache::remember('monitoring_table_plovis', 3600, function () {
            try {
                return $this->fetchMonitoringDataFromPlovis();
            } catch (\Exception $e) {
                Log::warning('Plovis fetch gagal, fallback ke DB: ' . $e->getMessage());
                return $this->calculateMonitoringDataFromDb();
            }
        });
    }

    /**
     * Ambil & aggregate data dari Plovis API.
     *
     * Field yang dipakai dari setiap feature.properties:
     *   - `Kawasan`  : jenis kawasan (APL / HL / HP / TN / SM / TWA)
     *   - `LSMGR`   : luas mangrove (bisa ha atau m² — konversi otomatis jika > 1000)
     */
    private function fetchMonitoringDataFromPlovis(): array
    {
        // pivot[KAWASAN][density] = total luas (ha)
        $pivot = [];

        foreach (self::PLOVIS_URLS as $density => $url) {
            $cacheKey = "geojson_plovis_{$density}";

            // Gunakan cache GeoJSON yang sama dengan GeoJsonApiController
            $geojson = Cache::remember($cacheKey, 3600, function () use ($url, $density) {
                $response = Http::timeout(60)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->get($url);

                if (!$response->successful()) {
                    throw new \RuntimeException("Plovis HTTP {$response->status()} untuk density={$density}");
                }

                $json = $response->json();
                // Plovis membungkus GeoJSON dalam key "geojson"
                return $json['geojson'] ?? $json;
            });

            if (empty($geojson['features']) || !is_array($geojson['features'])) {
                Log::warning("Plovis: tidak ada features untuk density={$density}");
                continue;
            }

            foreach ($geojson['features'] as $feature) {
                $props   = $feature['properties'] ?? [];
                $kawasan = strtoupper(trim($props['Kawasan'] ?? ''));
                $lsmgr   = isset($props['LSMGR']) ? (float) $props['LSMGR'] : 0.0;

                // Skip baris tanpa kawasan atau tanpa luas
                if ($kawasan === '' || $lsmgr <= 0) {
                    continue;
                }

                // Konversi m² → ha jika nilai sangat besar (> 1000)
                $areaHa = $lsmgr > 1000 ? $lsmgr / 10000 : $lsmgr;

                $pivot[$kawasan][$density] = ($pivot[$kawasan][$density] ?? 0.0) + $areaHa;
            }
        }

        return $this->buildResultFromPivot($pivot);
    }

    /**
     * Bangun array hasil dari pivot [kawasan][density] → luas.
     */
    private function buildResultFromPivot(array $pivot): array
    {
        $data           = ['luar_kawasan' => [], 'dalam_kawasan' => [], 'totals' => null];
        $grandByDensity = ['jarang' => 0.0, 'sedang' => 0.0, 'lebat' => 0.0];

        foreach (self::KAWASAN_DEF as $kw => $def) {
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

        $grandTotal = array_sum($grandByDensity);
        if ($grandTotal > 0) {
            $data['totals'] = [
                'jarang' => number_format($grandByDensity['jarang'], 2),
                'sedang' => number_format($grandByDensity['sedang'], 2),
                'lebat'  => number_format($grandByDensity['lebat'],  2),
                'total'  => number_format($grandTotal, 2),
            ];
        }

        return $data;
    }

    /**
     * Fallback: hitung dari database (dipakai jika Plovis tidak tersedia).
     * Menggunakan operator JSON PostgreSQL `geojson_properties->>'Kawasan'`.
     */
    private function calculateMonitoringDataFromDb(): array
    {
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
            Log::error('calculateMonitoringDataFromDb error: ' . $e->getMessage());
            return ['luar_kawasan' => [], 'dalam_kawasan' => [], 'totals' => null];
        }

        $pivot = [];
        foreach ($rows as $r) {
            $kw      = strtoupper(trim($r->kawasan ?? ''));
            $density = strtolower(trim($r->density ?? ''));
            if ($kw && $density) {
                $pivot[$kw][$density] = (float) $r->total_area;
            }
        }

        return $this->buildResultFromPivot($pivot);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PRIVATE — Format Location
    // ─────────────────────────────────────────────────────────────────────────

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

        if (str_contains($region, 'penjaringan'))                                                        return 'penjaringan';
        if (str_contains($region, 'cilincing'))                                                          return 'cilincing';
        if (str_contains($region, 'seribu utara') || str_contains($region, 'kepulauan seribu utara'))   return 'kep-seribu-utara';
        if (str_contains($region, 'seribu selatan') || str_contains($region, 'kepulauan seribu selatan')) return 'kep-seribu-selatan';

        return 'all';
    }
}
