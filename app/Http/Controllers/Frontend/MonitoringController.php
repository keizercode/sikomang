<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;

class MonitoringController extends FrontendController
{
    /**
     * Menampilkan halaman profil sebaran mangrove
     */
    public function index()
    {
        $locations = $this->getLocations();

        return view('pages.monitoring', [
            'title' => 'Profil Sebaran Mangrove DKI Jakarta 2025',
            'total_sites' => $this->getTotalSites(),
            'total_area' => $this->getTotalArea(),
            'monitoring_data' => $this->getMonitoringData(),
            'locations' => $locations
        ]);
    }

    /**
     * Menampilkan hasil pemantauan dengan detail lokasi
     */
    public function hasilPemantauan()
    {
        $locations = $this->getLocations();

        return view('pages.hasil-pemantauan', compact('locations'));
    }

    /**
     * Menampilkan detail lokasi pemantauan
     */
    public function detailLokasi($slug)
    {
        $location = $this->getLocationBySlug($slug);

        if (!$location) {
            abort(404, 'Lokasi tidak ditemukan');
        }

        return view('pages.detail-lokasi', compact('location'));
    }

    /**
     * Get total monitoring sites
     */
    private function getTotalSites(): int
    {
        return 23;
    }

    /**
     * Get total area coverage
     */
    private function getTotalArea(): int
    {
        return 297;
    }

    /**
     * Get monitoring data untuk tabel
     */
    private function getMonitoringData(): array
    {
        return [
            'luar_kawasan' => [
                [
                    'fungsi' => 'APL (Areal Penggunaan Lain)',
                    'jarang' => 36.54,
                    'sedang' => 56.38,
                    'lebat' => 171.28,
                    'total' => 264.21,
                    'status' => 'Bukan Kawasan Konservasi'
                ]
            ],
            'dalam_kawasan' => [
                [
                    'fungsi' => 'HL (Hutan Lindung)',
                    'jarang' => 3.03,
                    'sedang' => 39.06,
                    'lebat' => 19.59,
                    'total' => 61.67,
                    'status' => 'Kawasan Konservasi'
                ],
                [
                    'fungsi' => 'HP (Hutan Produksi)',
                    'jarang' => 2.20,
                    'sedang' => 6.53,
                    'lebat' => 71.84,
                    'total' => 80.57,
                    'status' => 'Bukan Kawasan Konservasi'
                ],
                [
                    'fungsi' => 'TN (Taman Nasional)',
                    'jarang' => 29.72,
                    'sedang' => 5.01,
                    'lebat' => 21.65,
                    'total' => 56.39,
                    'status' => 'Kawasan Konservasi'
                ],
                [
                    'fungsi' => 'SM (Suaka Margasatwa)',
                    'jarang' => 8.31,
                    'sedang' => 11.96,
                    'lebat' => 26.83,
                    'total' => 47.10,
                    'status' => 'Kawasan Konservasi'
                ],
                [
                    'fungsi' => 'TWA (Taman Wisata Alam)',
                    'jarang' => 2.01,
                    'sedang' => 94.49,
                    'lebat' => 1.79,
                    'total' => 98.28,
                    'status' => 'Kawasan Konservasi'
                ]
            ]
        ];
    }

    /**
     * Get all locations with complete data
     */
    private function getLocations(): array
    {
        return [
            // Kategori Jarang - Penjaringan
            [
                'slug' => 'rawa-hutan-lindung',
                'name' => 'Rawa Hutan Lindung',
                'type' => 'Pengkayaan',
                'year' => '2025',
                'area' => '44.7 ha',
                'density' => 'Jarang',
                'health' => '98% Sehat',
                'health_score' => 'NAK: 7.2',
                'coords' => '-6.1023, 106.7655',
                'location' => 'Kecamatan Penjaringan, Jakarta Utara, Rawa Hutan Lindung',
                'group' => 'penjaringan',
                'manager' => 'DPHK',
                'species' => 'Avicennia alba, Avicennia marina...',
                'damage_count' => 2,
                'carbon_data' => 'Data tidak tersedia',
                'certificate_status' => 'Tidak tersedia sertifikat',
                'description' => 'Kawasan hutan mangrove lindung dengan tingkat kesehatan yang baik.',
                'images' => [
                    'https://res.cloudinary.com/dgctlfa2t/image/upload/v1755389322/rhl2_htfyvf.jpg',
                    'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758002227/2-tanah_timbul-1_couywb.jpg',
                    'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758003912/3-pos_2_hutan_lindung-3_hk6fqt.jpg'
                ],
                'damages' => [
                    'Dahan patah dan pohon tumbang akibat angin kencang',
                    'Sampah dari laut'
                ],
                'actions' => [
                    'Pembersihan area terdampak yang menghalangi akses',
                    'Pagar jaring dekat pesisir'
                ],
                'species_detail' => [
                    'vegetasi' => [
                        'Avicennia alba',
                        'Avicennia marina',
                        'Excoecaria agallocha',
                        'Nypa fruticans',
                        'Rhizophora apiculata',
                        'Rhizophora mucronata'
                    ],
                    'fauna' => [
                        'Ular Tambang',
                        'Tupai',
                        'Biawak',
                        'Monyet ekor panjang',
                        'Ikan',
                        '16 Jenis Burung'
                    ]
                ],
                'activities' => [
                    'description' => 'Titik pengamatan berada di rawa dekat muara Kali Adem. Aktivitas di sekitarnya terbatas oleh:',
                    'items' => [
                        'Nelayan, Petani tambak',
                        'Petugas kawasan'
                    ]
                ],
                'forest_utilization' => [
                    'Hutan Lindung',
                    'Hutan Konservasi',
                    'Habitat flora & fauna',
                    'Pelindung pesisir',
                    'Ekowisata'
                ],
                'programs' => [
                    'Pembibitan/persemaian',
                    'Grebek sampah (pembersihan)',
                    'Penanaman mangrove',
                    'Penyediaan lahan'
                ],
                'stakeholders' => [
                    'PLN PJB',
                    'Universitas Trisakti',
                    'Yamaha',
                    'Bank DKI',
                    'AEON',
                    'Mitsubishi Motor'
                ]
            ],

            // Additional simplified entries
            [
                'slug' => 'pos-5-hutan-lindung',
                'name' => 'Pos 5 Hutan Lindung',
                'type' => 'Dilindungi',
                'year' => '2025',
                'area' => '4.7 ha',
                'density' => 'Jarang',
                'health' => '92% Sehat',
                'health_score' => 'NAK: 6.8',
                'coords' => '-6.0895, 106.7820',
                'location' => 'Kecamatan Penjaringan, Jakarta Utara, Pos 5 Hutan Lindung',
                'group' => 'penjaringan',
                'manager' => 'DPHK',
                'species' => 'Sonneratia caseolaris, Avicennia alba...',
                'damage_count' => 1,
                'carbon_data' => 'Data tidak tersedia',
                'certificate_status' => 'Tidak tersedia sertifikat',
                'description' => 'Pos pemantauan 5 di kawasan hutan lindung dengan status dilindungi.',
                'images' => [
                    'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758005191/4-pos5_hl-3_gezsmd.jpg'
                ]
            ],

            [
                'slug' => 'rusun-tni-al',
                'name' => 'Rusun TNI AL',
                'type' => 'Pengkayaan',
                'year' => '2025',
                'area' => '6 ha',
                'density' => 'Jarang',
                'health' => '80% Sehat',
                'health_score' => 'NAK: 6.2',
                'coords' => '-6.0912, 106.9105',
                'location' => 'Kecamatan Cilincing, Jakarta Utara, Rusun TNI AL',
                'group' => 'cilincing',
                'manager' => 'DPHK',
                'species' => 'Avicennia alba, Rhizophora mucronata...',
                'damage_count' => 3,
                'carbon_data' => 'Data tidak tersedia',
                'certificate_status' => 'Tidak tersedia sertifikat',
                'description' => 'Kawasan mangrove di sekitar Rusun TNI AL dengan program pengkayaan.',
                'images' => [
                    'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758068635/7-rusun_tni_al-1_gx1iqa.jpg'
                ]
            ],
        ];
    }

    /**
     * Get location by slug
     */
    private function getLocationBySlug(string $slug): ?array
    {
        $locations = $this->getLocations();

        foreach ($locations as $location) {
            if ($location['slug'] === $slug) {
                return $location;
            }
        }

        return null;
    }
}
