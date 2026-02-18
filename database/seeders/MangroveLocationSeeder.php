<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\MangroveLocation;
use App\Models\LocationDetail;
use App\Models\LocationImage;
use App\Models\LocationDamage;
use App\Models\LocationAction;

/**
 * MangroveLocationSeeder - 23 Titik Monitoring Mangrove DKI Jakarta
 *
 * Distribusi:
 *   - 11 titik: Penjaringan, Jakarta Utara
 *   -  5 titik: Cilincing, Jakarta Utara
 *   -  3 titik: Kepulauan Seribu Utara
 *   -  4 titik: Kepulauan Seribu Selatan
 *
 * Catatan:
 *   - 7 lokasi pertama: data LENGKAP (detail, images, damages, actions)
 *   - 16 lokasi sisanya: data DASAR (bisa diisi via admin panel)
 */
class MangroveLocationSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FK check
        DB::statement('SET session_replication_role = replica;');
        DB::table('location_actions')->delete();
        DB::table('location_damages')->delete();
        DB::table('location_images')->delete();
        DB::table('location_details')->delete();
        DB::table('mangrove_locations')->delete();
        DB::statement('SET session_replication_role = DEFAULT;');

        $this->command->info('🌳 Seeding 23 lokasi monitoring mangrove...');

        // ============================================================
        // KELOMPOK 1: PENJARINGAN (11 LOKASI)
        // ============================================================

        // 1. Rawa Hutan Lindung - LENGKAP
        $this->createFullLocation([
            'name'             => 'Rawa Hutan Lindung',
            'latitude'         => -6.1023,
            'longitude'        => 106.7655,
            'area'             => 44.70,
            'density'          => 'jarang',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Rawa Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK DKI Jakarta',
            'year_established' => 2000,
            'health_percentage' => 55.00,
            'health_score'     => 'NAK: 5.5',
            'description'      => 'Kawasan hutan lindung mangrove dengan kerapatan jarang di wilayah Penjaringan.',
            'species'          => 'Avicennia alba, Avicennia marina, Excoecaria agallocha, Nypa fruticans',
        ], [
            'vegetasi' => ['Avicennia alba', 'Avicennia marina', 'Excoecaria agallocha', 'Nypa fruticans', 'Rhizophora apiculata', 'Rhizophora mucronata'],
            'fauna'    => ['Ular Tambang', 'Tupai', 'Biawak', 'Monyet ekor panjang', 'Ikan', '16 Jenis Burung'],
            'activities' => [
                'description' => 'Titik pengamatan berada di rawa dekat muara Kali Adem.',
                'items'       => ['Nelayan, Petani tambak', 'Petugas kawasan']
            ],
            'forest_utilization' => ['Hutan Lindung', 'Hutan Konservasi', 'Habitat flora & fauna', 'Pelindung pesisir', 'Ekowisata'],
            'programs'           => ['Pembibitan/persemaian', 'Grebek sampah (pembersihan)', 'Penanaman mangrove', 'Penyediaan lahan'],
            'stakeholders'       => ['PLN PJB', 'Universitas Trisakti', 'Yamaha', 'Bank DKI', 'AEON', 'Mitsubishi Motor'],
        ], [
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1755389322/rhl2_htfyvf.jpg',
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758002227/2-tanah_timbul-1_couywb.jpg',
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758003912/3-pos_2_hutan_lindung-3_hk6fqt.jpg'
        ], [
            ['title' => 'Dahan patah dan pohon tumbang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'high', 'action' => 'Pembersihan area terdampak yang menghalangi akses'],
            ['title' => 'Sampah dari laut', 'description' => 'Sampah dari laut terbawa arus', 'priority' => 'medium', 'action' => 'Pagar jaring dekat pesisir'],
        ]);

        // 2. Pos 5 Hutan Lindung - LENGKAP
        $this->createFullLocation([
            'name'             => 'Pos 5 Hutan Lindung',
            'latitude'         => -6.0895,
            'longitude'        => 106.7820,
            'area'             => 4.70,
            'density'          => 'jarang',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Pos 5 Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK DKI Jakarta',
            'year_established' => 2000,
            'health_percentage' => 52.00,
            'health_score'     => 'NAK: 5.2',
            'description'      => 'Pos pemantauan 5 dalam kawasan hutan lindung mangrove Penjaringan.',
            'species'          => 'Sonneratia caseolaris, Avicennia alba',
        ], [
            'vegetasi' => ['Avicennia marina', 'Excoecaria agallocha', 'Rhizophora mucronata'],
            'fauna'    => ['Burung Kenari', 'Ikan'],
            'activities' => [
                'description' => 'Titik pengamatan berada di ujung kawasan HL sebelah barat, dekat TWA.',
                'items'       => ['Petugas kawasan']
            ],
            'forest_utilization' => ['Hutan Lindung', 'Hutan Konservasi', 'Habitat flora & fauna', 'Pelindung pesisir'],
            'programs'           => ['Pembibitan/persemaian', 'Grebek sampah (pembersihan)', 'Monitoring petugas'],
            'stakeholders'       => ['PLN PJB', 'Yamaha', 'Bank DKI', 'AEON'],
        ], [
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758005191/4-pos5_hl-3_gezsmd.jpg',
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758005192/4-pos5_hl_ujgm5s.jpg',
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758005191/4-pos5_hl-1_v29w7a.jpg',
        ], [
            ['title' => 'Erosi tanah', 'description' => 'Erosi tanah di area pesisir', 'priority' => 'medium', 'action' => 'Pembersihan area terdampak'],
        ]);

        // 3. Pos 8 Hutan Lindung - LENGKAP
        $this->createFullLocation([
            'name'             => 'Pos 8 Hutan Lindung',
            'latitude'         => -6.0912,
            'longitude'        => 106.7731,
            'area'             => 6.20,
            'density'          => 'jarang',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Pos 8 Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK DKI Jakarta',
            'year_established' => 2000,
            'health_percentage' => 50.00,
            'health_score'     => 'NAK: 5.0',
            'description'      => 'Pos pemantauan 8 dalam kawasan hutan lindung mangrove Penjaringan.',
            'species'          => 'Avicennia alba, Rhizophora mucronata',
        ], [
            'vegetasi' => ['Avicennia alba', 'Rhizophora mucronata', 'Sonneratia caseolaris'],
            'fauna'    => ['Ikan', 'Kepiting bakau', 'Burung bangau'],
            'activities' => [
                'description' => 'Pos monitoring di tengah kawasan hutan lindung.',
                'items'       => ['Petugas DPHK', 'Peneliti']
            ],
            'forest_utilization' => ['Hutan Lindung', 'Penelitian ekologi'],
            'programs'           => ['Monitoring rutin', 'Pembersihan kawasan'],
            'stakeholders'       => ['DPHK DKI Jakarta', 'Universitas Indonesia'],
        ], [], [
            ['title' => 'Sampah plastik', 'description' => 'Penumpukan sampah plastik dari laut', 'priority' => 'medium', 'action' => 'Pembersihan berkala'],
        ]);

        // 4. Teluk Jakarta Barat - LENGKAP
        $this->createFullLocation([
            'name'             => 'Teluk Jakarta Barat',
            'latitude'         => -6.0867,
            'longitude'        => 106.7602,
            'area'             => 12.50,
            'density'          => 'jarang',
            'type'             => 'rehabilitasi',
            'region'           => 'Penjaringan',
            'location_address' => 'Teluk Jakarta Barat, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK DKI Jakarta',
            'year_established' => 2015,
            'health_percentage' => 40.00,
            'health_score'     => 'NAK: 4.0',
            'description'      => 'Area rehabilitasi mangrove di pesisir barat Teluk Jakarta.',
            'species'          => 'Rhizophora apiculata, Avicennia marina',
        ], [
            'vegetasi' => ['Rhizophora apiculata', 'Avicennia marina', 'Sonneratia alba'],
            'fauna'    => ['Ikan bandeng', 'Udang', 'Kepiting'],
            'activities' => [
                'description' => 'Kawasan rehabilitasi aktif dengan program penanaman.',
                'items'       => ['Petani tambak', 'Relawan penanaman', 'Nelayan']
            ],
            'forest_utilization' => ['Rehabilitasi ekosistem', 'Perikanan berkelanjutan'],
            'programs'           => ['Penanaman mangrove', 'Penyuluhan masyarakat', 'Monitoring pertumbuhan'],
            'stakeholders'       => ['Kementerian Kelautan', 'NGO Konservasi', 'Komunitas lokal'],
        ], [], [
            ['title' => 'Abrasi pantai', 'description' => 'Abrasi pantai yang mengancam area rehabilitasi', 'priority' => 'high', 'action' => 'Penanaman intensif di garis pantai'],
            ['title' => 'Pencemaran limbah', 'description' => 'Limbah dari permukiman', 'priority' => 'high', 'action' => 'Koordinasi dengan Dinas Lingkungan Hidup'],
        ]);

        // 5. Tanah Timbul (Bird Feeding) - LENGKAP
        $this->createFullLocation([
            'name'             => 'Tanah Timbul (Bird Feeding)',
            'latitude'         => -6.1012,
            'longitude'        => 106.7645,
            'area'             => 2.89,
            'density'          => 'sedang',
            'type'             => 'pengkayaan',
            'region'           => 'Penjaringan',
            'location_address' => 'Tanah Timbul, Penjaringan, Jakarta Utara',
            'manager'          => 'BKSDA DKI Jakarta',
            'year_established' => 2010,
            'health_percentage' => 68.00,
            'health_score'     => 'NAK: 6.8',
            'description'      => 'Kawasan tanah timbul yang berfungsi sebagai area feeding burung laut dan pengkayaan mangrove.',
            'species'          => 'Avicennia alba, Sonneratia caseolaris',
        ], [
            'vegetasi' => ['Avicennia alba', 'Sonneratia caseolaris', 'Rhizophora apiculata'],
            'fauna'    => ['Burung kuntul', 'Burung bangau', 'Ikan gelodok', 'Kepiting'],
            'activities' => [
                'description' => 'Kawasan feeding ground burung migrasi dengan aktivitas minimal.',
                'items'       => ['Birdwatcher', 'Fotografer alam', 'Petugas BKSDA']
            ],
            'forest_utilization' => ['Habitat burung', 'Ekowisata', 'Pengkayaan mangrove'],
            'programs'           => ['Monitoring burung', 'Pengkayaan vegetasi', 'Edukasi lingkungan'],
            'stakeholders'       => ['BKSDA DKI Jakarta', 'Komunitas Birdwatcher', 'Pecinta Alam'],
        ], [], [
            ['title' => 'Gangguan pengunjung', 'description' => 'Pengunjung yang mengganggu burung', 'priority' => 'low', 'action' => 'Pemasangan rambu larangan dan edukasi'],
        ]);

        // 6. Pos 2 Hutan Lindung - LENGKAP
        $this->createFullLocation([
            'name'             => 'Pos 2 Hutan Lindung',
            'latitude'         => -6.1025,
            'longitude'        => 106.7680,
            'area'             => 8.30,
            'density'          => 'sedang',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Pos 2 Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK DKI Jakarta',
            'year_established' => 2000,
            'health_percentage' => 65.00,
            'health_score'     => 'NAK: 6.5',
            'description'      => 'Pos pemantauan 2 kawasan hutan lindung dengan kerapatan sedang.',
            'species'          => 'Rhizophora mucronata, Avicennia marina',
        ], [
            'vegetasi' => ['Rhizophora mucronata', 'Avicennia marina', 'Bruguiera gymnorrhiza'],
            'fauna'    => ['Monyet ekor panjang', 'Biawak', 'Burung elang laut', 'Ikan'],
            'activities' => [
                'description' => 'Pos monitoring di kawasan hutan lindung dengan akses terbatas.',
                'items'       => ['Petugas DPHK', 'Peneliti ekologi']
            ],
            'forest_utilization' => ['Hutan Lindung', 'Konservasi biodiversitas', 'Penelitian'],
            'programs'           => ['Monitoring ekosistem', 'Penelitian flora fauna', 'Pembersihan berkala'],
            'stakeholders'       => ['DPHK DKI Jakarta', 'IPB University', 'LIPI'],
        ], [], [
            ['title' => 'Perburuan satwa', 'description' => 'Indikasi perburuan monyet dan biawak', 'priority' => 'high', 'action' => 'Patroli rutin dan penegakan hukum'],
        ]);

        // 7. TWA Angke Kapuk - LENGKAP
        $this->createFullLocation([
            'name'             => 'TWA Angke Kapuk',
            'latitude'         => -6.0921,
            'longitude'        => 106.7590,
            'area'             => 99.82,
            'density'          => 'sedang',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Jl. Garden House, Penjaringan, Jakarta Utara',
            'manager'          => 'BKSDA DKI Jakarta',
            'year_established' => 1995,
            'health_percentage' => 72.00,
            'health_score'     => 'NAK: 7.2',
            'description'      => 'Taman Wisata Alam Angke Kapuk — kawasan konservasi mangrove terbesar di DKI Jakarta yang dikelola BKSDA.',
            'species'          => 'Avicennia alba, Rhizophora apiculata, Sonneratia caseolaris',
        ], [
            'vegetasi' => ['Avicennia alba', 'Rhizophora apiculata', 'Sonneratia caseolaris', 'Bruguiera gymnorrhiza', 'Excoecaria agallocha'],
            'fauna'    => ['Burung cangak merah', 'Burung kuntul', 'Monyet ekor panjang', 'Biawak', 'Ikan gelodok', 'Kepiting bakau'],
            'activities' => [
                'description' => 'Kawasan ekowisata dengan jalur trekking dan fasilitas edukasi.',
                'items'       => ['Wisatawan', 'Pelajar', 'Peneliti', 'Petugas BKSDA']
            ],
            'forest_utilization' => ['Ekowisata', 'Edukasi lingkungan', 'Konservasi', 'Penelitian'],
            'programs'           => ['Ekowisata mangrove', 'Edukasi sekolah', 'Penelitian ekologi', 'Monitoring satwa'],
            'stakeholders'       => ['BKSDA DKI Jakarta', 'Pemprov DKI', 'Sekolah-sekolah', 'Universitas', 'NGO Konservasi'],
        ], [], [
            ['title' => 'Sampah wisatawan', 'description' => 'Penumpukan sampah dari aktivitas wisata', 'priority' => 'medium', 'action' => 'Penambahan tempat sampah dan pembersihan harian'],
            ['title' => 'Kerusakan jembatan kayu', 'description' => 'Beberapa jembatan kayu trekking rusak', 'priority' => 'high', 'action' => 'Perbaikan dan penggantian jembatan'],
        ]);

        // 8-11. Lokasi Penjaringan sisanya (data minimal)
        foreach (
            [
                ['name' => 'Ekowisata Mangrove Kapuk',        'lat' => -6.0943, 'lng' => 106.7561, 'area' => 35.40, 'density' => 'sedang', 'type' => 'pengkayaan',   'year' => 2008, 'health' => 70.00],
                ['name' => 'Muara Angke',                     'lat' => -6.1050, 'lng' => 106.7480, 'area' => 21.60, 'density' => 'lebat',  'type' => 'dilindungi',  'year' => 1984, 'health' => 82.00],
                ['name' => 'Titik 2 Elang Laut',              'lat' => -6.1015, 'lng' => 106.7670, 'area' => 5.10,  'density' => 'lebat',  'type' => 'dilindungi',  'year' => 2000, 'health' => 80.00],
                ['name' => 'Pantai Indah Kapuk Utara',        'lat' => -6.0788, 'lng' => 106.7510, 'area' => 18.75, 'density' => 'lebat',  'type' => 'restorasi',   'year' => 2019, 'health' => 78.00],
            ] as $data
        ) {
            $this->createBasicLocation($data, 'Penjaringan');
        }

        // ============================================================
        // KELOMPOK 2: CILINCING (5 LOKASI)
        // ============================================================

        foreach (
            [
                ['name' => 'Rusun TNI AL Cilincing', 'lat' => -6.0912, 'lng' => 106.9105, 'area' => 6.00,  'density' => 'jarang', 'type' => 'rehabilitasi', 'year' => 2012, 'health' => 42.00],
                ['name' => 'Mangrove Marunda',       'lat' => -6.1102, 'lng' => 106.8892, 'area' => 11.30, 'density' => 'sedang', 'type' => 'pengkayaan',   'year' => 2016, 'health' => 63.00],
                ['name' => 'Mangrove STIP',          'lat' => -6.1223, 'lng' => 106.9512, 'area' => 4.60,  'density' => 'lebat',  'type' => 'pengkayaan',   'year' => 2014, 'health' => 76.00],
                ['name' => 'Mangrove Si Pitung',     'lat' => -6.1198, 'lng' => 106.8645, 'area' => 5.50,  'density' => 'lebat',  'type' => 'pengkayaan',   'year' => 2013, 'health' => 79.00],
                ['name' => 'Pasmar 1 TNI AL',        'lat' => -6.1156, 'lng' => 106.8598, 'area' => 5.50,  'density' => 'lebat',  'type' => 'pengkayaan',   'year' => 2011, 'health' => 81.00],
            ] as $data
        ) {
            $this->createBasicLocation($data, 'Cilincing');
        }

        // ============================================================
        // KELOMPOK 3: KEPULAUAN SERIBU UTARA (3 LOKASI)
        // ============================================================

        foreach (
            [
                ['name' => 'Mangrove Pulau Panjang',  'lat' => -5.5723, 'lng' => 106.5612, 'area' => 8.90,  'density' => 'sedang', 'type' => 'dilindungi',   'year' => 2005, 'health' => 67.00],
                ['name' => 'Mangrove Pulau Harapan',  'lat' => -5.6234, 'lng' => 106.6145, 'area' => 14.20, 'density' => 'lebat',  'type' => 'dilindungi',   'year' => 2005, 'health' => 85.00],
                ['name' => 'Mangrove Pulau Kelapa',   'lat' => -5.5889, 'lng' => 106.5344, 'area' => 6.75,  'density' => 'jarang', 'type' => 'rehabilitasi', 'year' => 2010, 'health' => 48.00],
            ] as $data
        ) {
            $this->createBasicLocation($data, 'Kepulauan Seribu Utara');
        }

        // ============================================================
        // KELOMPOK 4: KEPULAUAN SERIBU SELATAN (4 LOKASI)
        // ============================================================

        foreach (
            [
                ['name' => 'Mangrove Pulau Tidung',      'lat' => -5.7923, 'lng' => 106.5098, 'area' => 9.60,  'density' => 'sedang', 'type' => 'pengkayaan',   'year' => 2007, 'health' => 66.00],
                ['name' => 'Mangrove Pulau Pari',        'lat' => -5.8612, 'lng' => 106.5723, 'area' => 7.30,  'density' => 'jarang', 'type' => 'rehabilitasi', 'year' => 2012, 'health' => 45.00],
                ['name' => 'Mangrove Pulau Untung Jawa', 'lat' => -5.9812, 'lng' => 106.7189, 'area' => 13.80, 'density' => 'lebat',  'type' => 'dilindungi',   'year' => 1998, 'health' => 83.00],
                ['name' => 'Mangrove Pulau Lancang',     'lat' => -5.9345, 'lng' => 106.6512, 'area' => 5.95,  'density' => 'sedang', 'type' => 'pengkayaan',   'year' => 2009, 'health' => 62.00],
            ] as $data
        ) {
            $this->createBasicLocation($data, 'Kepulauan Seribu Selatan');
        }

        $this->command->info('✅ Berhasil seed 23 lokasi monitoring mangrove!');
        $this->displaySummary();
    }

    /**
     * Create location with FULL details (images, damages, actions)
     */
    private function createFullLocation(array $location, array $details, array $images = [], array $damages = []): MangroveLocation
    {
        $loc = MangroveLocation::create(array_merge($location, [
            'slug'       => Str::slug($location['name']),
            'is_active'  => true,
            'carbon_data' => 'Data tidak tersedia',
        ]));

        LocationDetail::create(array_merge(['mangrove_location_id' => $loc->id], $details));

        foreach ($images as $index => $url) {
            LocationImage::create([
                'mangrove_location_id' => $loc->id,
                'image_url'            => $url,
                'order'                => $index,
            ]);
        }

        foreach ($damages as $damageData) {
            $damage = LocationDamage::create([
                'mangrove_location_id' => $loc->id,
                'title'                => $damageData['title'],
                'description'          => $damageData['description'],
                'priority'             => $damageData['priority'],
                'status'               => 'pending',
            ]);

            if (isset($damageData['action'])) {
                LocationAction::create([
                    'location_damage_id' => $damage->id,
                    'action_description' => $damageData['action'],
                    'action_date'        => now(),
                ]);
            }
        }

        return $loc;
    }

    /**
     * Create location with BASIC data only (no images/damages)
     */
    private function createBasicLocation(array $data, string $region): MangroveLocation
    {
        $loc = MangroveLocation::create([
            'name'             => $data['name'],
            'slug'             => Str::slug($data['name']),
            'latitude'         => $data['lat'],
            'longitude'        => $data['lng'],
            'area'             => $data['area'],
            'density'          => $data['density'],
            'type'             => $data['type'],
            'region'           => $region,
            'location_address' => "{$data['name']}, Kecamatan {$region}, Jakarta Utara",
            'manager'          => $region === 'Cilincing' || $region === 'Penjaringan' ? 'DPHK DKI Jakarta' : 'Balai TN Kepulauan Seribu',
            'year_established' => $data['year'],
            'health_percentage' => $data['health'],
            'health_score'     => 'NAK: ' . number_format($data['health'] / 10, 1),
            'description'      => "Kawasan mangrove {$data['density']} di wilayah {$region}.",
            'species'          => 'Avicennia alba, Rhizophora mucronata',
            'carbon_data'      => 'Data tidak tersedia',
            'is_active'        => true,
        ]);

        LocationDetail::create([
            'mangrove_location_id' => $loc->id,
            'vegetasi'             => [],
            'fauna'                => [],
            'activities'           => [],
            'forest_utilization'   => [],
            'programs'             => [],
            'stakeholders'         => [],
        ]);

        return $loc;
    }

    /**
     * Display summary
     */
    private function displaySummary(): void
    {
        $stats = DB::table('mangrove_locations')
            ->select('region', DB::raw('COUNT(*) as count'), DB::raw('SUM(area) as total_area'))
            ->groupBy('region')
            ->orderBy('region')
            ->get();

        $this->command->table(['Wilayah', 'Jumlah', 'Total Area (ha)'], $stats->map(fn($s) => [
            $s->region,
            $s->count,
            number_format($s->total_area, 2),
        ])->toArray());

        $total = DB::table('mangrove_locations')->count();
        $this->command->info("📊 Total: {$total} lokasi");
    }
}
