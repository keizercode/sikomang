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
    public function index()
    {
        // Get all active locations
        $locations = MangroveLocation::where('is_active', true)
            ->with(['details', 'images', 'damages.actions'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total sites and area
        $total_sites = $locations->count();
        $total_area = $locations->sum('area');

        // Calculate monitoring data by density and conservation type
        $monitoring_data = $this->calculateMonitoringData($locations);

        return view('pages.frontend.monitoring', [
            'title' => 'Profil Sebaran Mangrove DKI Jakarta 2025',
            'total_sites' => $total_sites,
            'total_area' => number_format($total_area, 2),
            'monitoring_data' => $monitoring_data,
            'locations' => $locations
        ]);
    }

    /**
     * Menampilkan hasil pemantauan dengan detail lokasi
     */
    public function hasilPemantauan()
    {
        $locations = MangroveLocation::where('is_active', true)
            ->with(['details', 'images', 'damages.actions'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Format locations for frontend
        $formattedLocations = $locations->map(function ($location) {
            return $this->formatLocationForFrontend($location);
        })->toArray();

        return view('pages.frontend.hasil-pemantauan', [
            'locations' => $formattedLocations
        ]);
    }

    /**
     * Menampilkan detail lokasi pemantauan
     */
    public function detailLokasi($slug)
    {
        $location = MangroveLocation::where('slug', $slug)
            ->where('is_active', true)
            ->with(['details', 'images', 'damages.actions'])
            ->firstOrFail();

        $formattedLocation = $this->formatLocationForFrontend($location);

        return view('pages.frontend.detail-lokasi', [
            'location' => $formattedLocation
        ]);
    }

    /**
     * Calculate monitoring data dari database
     */
    private function calculateMonitoringData($locations)
    {
        $data = [
            'luar_kawasan' => [],
            'dalam_kawasan' => []
        ];

        // Group by kawasan type (you may need to add this field to database)
        // For now, using simplified logic

        // APL (Luar Kawasan)
        $apl = $locations->filter(function ($loc) {
            return $loc->type === 'pengkayaan' || $loc->type === 'rehabilitasi';
        });

        if ($apl->count() > 0) {
            $data['luar_kawasan'][] = [
                'fungsi' => 'APL (Areal Penggunaan Lain)',
                'jarang' => number_format($apl->where('density', 'jarang')->sum('area'), 2),
                'sedang' => number_format($apl->where('density', 'sedang')->sum('area'), 2),
                'lebat' => number_format($apl->where('density', 'lebat')->sum('area'), 2),
                'total' => number_format($apl->sum('area'), 2),
                'status' => 'Bukan Kawasan Konservasi'
            ];
        }

        // HL, HP, TN, SM, TWA (Dalam Kawasan)
        $hl = $locations->filter(fn($loc) => $loc->type === 'dilindungi');
        if ($hl->count() > 0) {
            $data['dalam_kawasan'][] = [
                'fungsi' => 'HL (Hutan Lindung)',
                'jarang' => number_format($hl->where('density', 'jarang')->sum('area'), 2),
                'sedang' => number_format($hl->where('density', 'sedang')->sum('area'), 2),
                'lebat' => number_format($hl->where('density', 'lebat')->sum('area'), 2),
                'total' => number_format($hl->sum('area'), 2),
                'status' => 'Kawasan Konservasi'
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
            'slug' => $location->slug,
            'name' => $location->name,
            'type' => ucfirst($location->type),
            'year' => $location->year_established ?? date('Y'),
            'area' => $location->area ? $location->area . ' ha' : 'Belum diidentifikasi',
            'density' => ucfirst($location->density),
            'health' => $location->health_percentage ? $location->health_percentage . '% Sehat' : 'N/A',
            'health_score' => $location->health_score ?? 'N/A',
            'coords' => $location->latitude . ', ' . $location->longitude,
            'location' => $location->location_address ?? $location->region ?? 'DKI Jakarta',
            'group' => $this->determineGroup($location),
            'manager' => $location->manager ?? 'DPHK',
            'species' => $location->species ?? 'Belum diidentifikasi',
            'damage_count' => $location->damages->whereIn('status', ['pending', 'in_progress'])->count(),
            'carbon_data' => $location->carbon_data ?? 'Data tidak tersedia',
            'certificate_status' => 'Tidak tersedia sertifikat',
            'description' => $location->description ?? 'Tidak ada deskripsi',
            'images' => $location->images->pluck('image_url')->toArray(),
            'damages' => $location->damages->pluck('title')->toArray(),
            'actions' => $location->damages->flatMap(fn($d) => $d->actions->pluck('action_description'))->toArray(),
            'species_detail' => $details ? [
                'vegetasi' => json_decode($details->vegetasi, true) ?? [],
                'fauna' => json_decode($details->fauna, true) ?? []
            ] : null,
            'activities' => $details ? json_decode($details->activities, true) : null,
            'forest_utilization' => $details ? json_decode($details->forest_utilization, true) : null,
            'programs' => $details ? json_decode($details->programs, true) : null,
            'stakeholders' => $details ? json_decode($details->stakeholders, true) : null,
        ];
    }

    /**
     * Determine location group based on region
     */
    private function determineGroup($location)
    {
        $region = strtolower($location->region ?? '');

        if (str_contains($region, 'penjaringan')) {
            return 'penjaringan';
        } elseif (str_contains($region, 'cilincing')) {
            return 'cilincing';
        } elseif (str_contains($region, 'seribu utara') || str_contains($region, 'kepulauan seribu utara')) {
            return 'kep-seribu-utara';
        } elseif (str_contains($region, 'seribu selatan') || str_contains($region, 'kepulauan seribu selatan')) {
            return 'kep-seribu-selatan';
        }

        return 'all';
    }
}
