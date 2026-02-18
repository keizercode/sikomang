<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{

    /**
     * Menampilkan halaman profil sebaran mangrove dengan data dinamis
     */
    public function index(Request $request)
    {
        try {
            $locations = MangroveLocation::where('is_active', true)
                ->with(['details', 'images', 'damages.actions'])
                ->orderBy('created_at', 'desc')
                ->get();

            $total_sites = $locations->count();
            $total_area  = $locations->sum('area');

            $monitoring_data = $this->calculateMonitoringData($locations);

            $formattedLocations = $locations->map(function ($location) {
                return $this->formatLocationForFrontend($location);
            })->toArray();

            return view('pages.frontend.monitoring', [
                'title'           => 'Profil Sebaran Mangrove DKI Jakarta 2025',
                'total_sites'     => $total_sites,
                'total_area'      => number_format($total_area, 2),
                'monitoring_data' => $monitoring_data,
                'locations'       => $formattedLocations,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@index: ' . $e->getMessage());

            return view('pages.frontend.monitoring', [
                'title'           => 'Profil Sebaran Mangrove DKI Jakarta 2025',
                'total_sites'     => 0,
                'total_area'      => '0.00',
                'monitoring_data' => ['luar_kawasan' => [], 'dalam_kawasan' => []],
                'locations'       => [],
            ]);
        }
    }

    /**
     * Menampilkan hasil pemantauan dengan detail lokasi.
     * Semua angka di sidebar dihitung dari database — tidak ada hardcode.
     */
    public function hasilPemantauan()
    {
        try {
            $locations = MangroveLocation::where('is_active', true)
                ->with(['details', 'images', 'damages.actions'])
                ->orderBy('created_at', 'desc')
                ->get();

            // ── Statistik Utama ────────────────────────────────────────
            $totalSites = $locations->count();
            $totalArea  = $locations->sum('area');

            // ── Sebaran per Wilayah ────────────────────────────────────
            $regionStats = [
                'penjaringan'         => $locations->filter(fn($l) => stripos($l->region ?? '', 'penjaringan') !== false)->count(),
                'cilincing'           => $locations->filter(fn($l) => stripos($l->region ?? '', 'cilincing') !== false)->count(),
                'kep_seribu_utara'    => $locations->filter(fn($l) => stripos($l->region ?? '', 'seribu utara') !== false)->count(),
                'kep_seribu_selatan'  => $locations->filter(fn($l) => stripos($l->region ?? '', 'seribu selatan') !== false)->count(),
            ];

            // ── Rekomendasi Pengelolaan (berdasarkan type) ─────────────
            $typeStats = [
                'dilindungi'  => $locations->where('type', 'dilindungi')->count(),
                'pengkayaan'  => $locations->where('type', 'pengkayaan')->count(),
                'rehabilitasi' => $locations->where('type', 'rehabilitasi')->count(),
                'restorasi'   => $locations->where('type', 'restorasi')->count(),
            ];

            // ── Format kartu ───────────────────────────────────────────
            $formattedLocations = $locations->map(function ($location) {
                return $this->formatLocationForFrontend($location);
            })->toArray();

            return view('pages.frontend.hasil-pemantauan', [
                'locations'    => $formattedLocations,
                'totalSites'   => $totalSites,
                'totalArea'    => number_format($totalArea, 2),
                'regionStats'  => $regionStats,
                'typeStats'    => $typeStats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@hasilPemantauan: ' . $e->getMessage());

            return view('pages.frontend.hasil-pemantauan', [
                'locations'   => [],
                'totalSites'  => 0,
                'totalArea'   => '0.00',
                'regionStats' => ['penjaringan' => 0, 'cilincing' => 0, 'kep_seribu_utara' => 0, 'kep_seribu_selatan' => 0],
                'typeStats'   => ['dilindungi' => 0, 'pengkayaan' => 0, 'rehabilitasi' => 0, 'restorasi' => 0],
            ]);
        }
    }

    /**
     * Menampilkan detail lokasi pemantauan
     */
    public function detailLokasi($slug)
    {
        try {
            $location = MangroveLocation::where('slug', $slug)
                ->where('is_active', true)
                ->with(['details', 'images', 'damages.actions'])
                ->firstOrFail();

            $formattedLocation = $this->formatLocationForFrontend($location);

            return view('pages.frontend.detail-lokasi', [
                'location' => $formattedLocation,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Lokasi tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Error in MonitoringController@detailLokasi: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat data lokasi');
        }
    }

    /**
     * Calculate monitoring data dari database
     */
    private function calculateMonitoringData($locations)
    {
        $data = ['luar_kawasan' => [], 'dalam_kawasan' => []];

        $apl = $locations->filter(fn($loc) => in_array($loc->type, ['pengkayaan', 'rehabilitasi']));
        if ($apl->count() > 0) {
            $data['luar_kawasan'][] = [
                'fungsi' => 'APL (Areal Penggunaan Lain)',
                'jarang' => number_format($apl->where('density', 'jarang')->sum('area'), 2),
                'sedang' => number_format($apl->where('density', 'sedang')->sum('area'), 2),
                'lebat'  => number_format($apl->where('density', 'lebat')->sum('area'), 2),
                'total'  => number_format($apl->sum('area'), 2),
                'status' => 'Bukan Kawasan Konservasi',
            ];
        }

        $hl = $locations->filter(fn($loc) => in_array($loc->type, ['dilindungi', 'restorasi']));
        if ($hl->count() > 0) {
            $data['dalam_kawasan'][] = [
                'fungsi' => 'HL (Hutan Lindung)',
                'jarang' => number_format($hl->where('density', 'jarang')->sum('area'), 2),
                'sedang' => number_format($hl->where('density', 'sedang')->sum('area'), 2),
                'lebat'  => number_format($hl->where('density', 'lebat')->sum('area'), 2),
                'total'  => number_format($hl->sum('area'), 2),
                'status' => 'Kawasan Konservasi',
            ];
        }

        return $data;
    }

    /**
     * Format location data untuk frontend
     */
    private function formatLocationForFrontend($location)
    {
        $details = $location->details;

        return [
            'id'                => $location->id,
            'slug'              => $location->slug,
            'name'              => $location->name,
            'type'              => ucfirst($location->type),
            'year'              => $location->year_established ?? date('Y'),
            'area'              => $location->area ? number_format($location->area, 2) . ' ha' : 'Belum diidentifikasi',
            'density'           => strtolower($location->density),
            'health'            => $location->health_percentage ? $location->health_percentage . '% Sehat' : 'N/A',
            'health_score'      => $location->health_score ?? 'N/A',
            'coords'            => $location->latitude . ', ' . $location->longitude,
            'latitude'          => (float) $location->latitude,
            'longitude'         => (float) $location->longitude,
            'location'          => $location->location_address ?? $location->region ?? 'DKI Jakarta',
            'group'             => $this->determineGroup($location),
            'manager'           => $location->manager ?? 'DPHK',
            'species'           => $location->species ?? 'Belum diidentifikasi',
            'damage_count'      => $location->damages->whereIn('status', ['pending', 'in_progress'])->count(),
            'carbon_data'       => $location->carbon_data ?? 'Data tidak tersedia',
            'certificate_status' => 'Tidak tersedia sertifikat',
            'description'       => $location->description ?? 'Tidak ada deskripsi',
            'images'            => $location->images->pluck('image_url')->toArray(),
            'damages'           => $location->damages->pluck('title')->toArray(),
            'actions'           => $location->damages->flatMap(fn($d) => $d->actions->pluck('action_description'))->toArray(),
            'species_detail'    => $details ? [
                'vegetasi' => is_array($details->vegetasi) ? $details->vegetasi : json_decode($details->vegetasi, true) ?? [],
                'fauna'    => is_array($details->fauna)    ? $details->fauna    : json_decode($details->fauna, true)    ?? [],
            ] : null,
            'activities'        => $details ? (is_array($details->activities)       ? $details->activities       : json_decode($details->activities, true))       : null,
            'forest_utilization' => $details ? (is_array($details->forest_utilization) ? $details->forest_utilization : json_decode($details->forest_utilization, true)) : null,
            'programs'          => $details ? (is_array($details->programs)         ? $details->programs         : json_decode($details->programs, true))         : null,
            'stakeholders'      => $details ? (is_array($details->stakeholders)     ? $details->stakeholders     : json_decode($details->stakeholders, true))     : null,
        ];
    }

    /**
     * Determine location group based on region
     */
    private function determineGroup($location)
    {
        $region = strtolower($location->region ?? '');

        if (str_contains($region, 'penjaringan'))                                            return 'penjaringan';
        if (str_contains($region, 'cilincing'))                                              return 'cilincing';
        if (str_contains($region, 'seribu utara') || str_contains($region, 'kepulauan seribu utara'))   return 'kep-seribu-utara';
        if (str_contains($region, 'seribu selatan') || str_contains($region, 'kepulauan seribu selatan')) return 'kep-seribu-selatan';

        return 'all';
    }
}
