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
            'density'          => 'sedang',
            'type'             => 'pengkayaan',
            'region'           => 'Penjaringan',
            'location_address' => 'Rawa Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK',
            'year_established' => 2025,
            'health_percentage' => 98.00,
            'health_score'     => 'NAK: 7.2',
            'description'      => 'Titik pengamatan berada di rawa dekat muara Kali Adem.',
            'species'          => 'Avicennia alba, Avicennia marina, Excoecaria agallocha, Nypa fruticans',
        ], [
            'vegetasi' => [
                'Avicennia alba',
                'Avicennia marina',
                'Excoecaria agallocha',
                'Nypa fruticans',
                'Rhizophora apiculata',
                'Rhizophora mucronata',
                'Rhizophora stylosa',
                'Sonneratia caseolaris',
            ],
            'fauna'    => [
                'Ular Tambang',
                'Tupai',
                'Biawak',
                'Monyet ekor panjang',
                'Ikan',
                '16 Jenis Burung'
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di rawa dekat muara Kali Adem. Aktivitas di sekitarnya terbatas oleh:',
                'items'       => ['Nelayan, Petani tambak', 'Petugas kawasan']
            ],
            'forest_utilization' => [
                'Hutan Lindung',
                'Hutan Konservasi',
                'Habitat flora & fauna',
                'Pelindung pesisir',
                'Ekowisata'
            ],
            'programs'           => [
                'Pembibitan/persemaian',
                'Grebek sampah (pembersihan)',
                'Penanaman mangrove',
                'Penyediaan lahan'
            ],
            'stakeholders'       => [
                'PLN PJB',
                'Universitas Trisakti',
                'Yamaha',
                'Bank DKI',
                'AEON',
                'Mitsubishi Motor'
            ],
        ], [
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1755389322/rhl2_htfyvf.jpg',
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758002227/2-tanah_timbul-1_couywb.jpg',
            'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758003912/3-pos_2_hutan_lindung-3_hk6fqt.jpg'
        ], [
            ['title' => 'Dahan patah dan pohon tumbang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'high', 'action' => 'Pembersihan area terdampak yang menghalangi akses'],
            ['title' => 'Sampah dari laut', 'description' => 'Sampah dari laut terbawa arus', 'priority' => 'medium', 'action' => 'Pagar jaring dekat pesisir'],
        ]);

        // 2. Tanah Timbul (Bird Feeding)
        $this->createFullLocation([
            'name'             => 'Tanah Timbul (Bird Feeding)',
            'latitude'         => -6.1010,
            'longitude'        => 106.7637,
            'area'             => 2.89,
            'density'          => 'jarang',
            'type'             => 'rehabilitasi',
            'region'           => 'Penjaringan',
            'location_address' => 'Tanah Timbul (Bird Feeding), Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 6',
            'description'      => 'Titik pengamatan berada di tanah timbul di tengah perairan.',
            'species'          => 'Avicennia marina, Nypa fruticans, Sonneratia caseolaris',
        ], [
            'vegetasi' => [
                'Avicennia marina',
                'Nypa fruticans',
                'Sonneratia caseolaris'
            ],
            'fauna'    => [
                'Ular Tambang',
                'Tupai',
                'Biawak',
                'Monyet ekor panjang',
                'Ikan',
                '16 Jenis Burung'
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di tanah timbul di tengah perairan.',
                'items'       => ['Nelayan, Petani tambak', 'Petugas kawasan', 'Pemungut sampah']
            ],
            'forest_utilization' => ['Hutan Lindung', 'Hutan Konservasi', 'Habitat flora & fauna', 'Pelindung pesisir', 'Ekowisata'],
            'programs'           => ['Pembibitan/persemaian', 'Grebek sampah (pembersihan)', 'Penanaman mangrove', 'Penyediaan lahan'],
            'stakeholders'       => [
                'PLN PJB',
                'Universitas Trisakti',
                'Yamaha',
                'Bank DKI',
                'AEON',
                'Mitsubishi Motor'
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771911252/8415c576-558a-4bf2-b171-10f2103e5f78.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771911196/8d307a5d-f150-4a3e-adc5-7b48c625b59a.png',
            'hhttps://res.cloudinary.com/dmcvht1vr/image/upload/v1771911230/a3ae662c-5c9c-4d69-a184-ece1d3e507ee.png',
        ], [
            ['title' => 'Dahan patah dan pohon tumbang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'medium', 'action' => 'Pembersihan area terdampak yang menghalangi akses'],
            ['title' => 'Sampah dari laut', 'description' => 'Sampah dari laut terbawa arus', 'priority' => 'medium', 'action' => 'Pagar jaring dekat pesisir'],
            ['title' => 'Pasang laut', 'description' => 'Pasang laut membuat bibit terendam dan mati', 'priority' => 'medium', 'action' => 'Penyulaman bibit'],
        ]);

        // 3. Pos 2 Hutan Lindung
        $this->createFullLocation([
            'name'             => 'Pos 2 Hutan Lindung',
            'latitude'         => -6.1027,
            'longitude'        => 106.7619,
            'area'             => null,
            'density'          => 'sedang',
            'type'             => 'pengkayaan',
            'region'           => 'Penjaringan',
            'location_address' => 'Pos 2 Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK',
            'year_established' => 2025,
            'health_percentage' => 78.8,
            'health_score'     => 'NAK: 6.3',
            'description'      => 'Titik pengamatan berada di Pos 2 HL',
            'species'          => 'Api-api, Buta-buta, Bakau Kurap',
        ], [
            'vegetasi' => ['Api-api', 'Buta-buta', 'Bakau Kurap'],
            'fauna'    => [
                'Ular Tambang',
                'Tupai',
                'Biawak',
                'Monyet ekor panjang',
                'Ikan',
                '5 Jenis Burung',
                'Kupu-kupu',
                'Kura-kura'
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di Pos 2 HL. Aktivitas di sekitarnya terbatas oleh:',
                'items'       => [
                    'Petugas kawasan',
                    'Pengunjung'
                ]
            ],
            'forest_utilization' => [
                'Hutan Lindung',
                'Hutan Konservasi',
                'Habitat flora & fauna',
                'Pelindung pesisir',
                'Ekowisata'
            ],
            'programs'           => [
                'Pembibitan/persemaian',
                'Grebek sampah (pembersihan)',
                'Penanaman mangrove',
                'Penyediaan lahan'
            ],
            'stakeholders'       => [
                'PLN PJB',
                'Universitas Trisakti',
                'Yamaha',
                'Bank DKI',
                'AEON',
                'Mitsubishi Motor'
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771912344/05c5ed57-1556-4919-87b5-3d303ed8e120.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771912360/c2c32fd8-b470-4428-b6c6-231fd8d8c42b.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771912430/752f900b-5f69-410d-a2fb-03c39604e79c.png',

        ], [
            ['title' => 'Dahan patah dan pohon tumbang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'medium', 'action' => 'Pembersihan area terdampak yang menghalangi akses'],
            ['title' => 'Sampah dari laut', 'description' => 'Sampah dari laut terbawa arus', 'priority' => 'medium', 'action' => 'Pagar jaring dekat pesisir'],
            ['title' => 'Pasang laut', 'description' => 'Pasang laut membuat bibit terendam dan mati', 'priority' => 'medium', 'action' => 'Penyulaman bibit'],
            ['title' => 'Gangguan makaka membuat bibit', 'description' => 'Gangguan makaka membuat bibit, pohon yang baru ditanam patah/mati/tunas rusak', 'priority' => 'medium', 'action' => 'Penyulaman bibit, pemantauan aktivitas makaka'],
        ]);

        // 4. Pos 5 Hutan Lindung
        $this->createFullLocation([
            'name'             => 'Pos 5 Hutan Lindung',
            'latitude'         => -6.1003,
            'longitude'        => 106.7385,
            'area'             => 4.3,
            'density'          => 'lebat',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Pos 5 Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK',
            'year_established' => 2025,
            'health_percentage' => 58.3,
            'health_score'     => 'NAK: 6.4',
            'description'      => 'Titik pengamatan berada di ujung kawasan HL sebelah barat, dekat TWA.',
            'species'          => 'Avicennia marina, Excoecaria agallocha, Rhizophora mucronata',
        ], [
            'vegetasi' => [
                'Avicennia marina',
                'Excoecaria agallocha',
                'Rhizophora mucronata'
            ],
            'fauna'    => [
                'Burung Kuntul',
                'Ikan'
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di ujung kawasan HL sebelah barat, dekat TWA. Aktivitas di sekitarnya terbatas oleh:',
                'items'       => ['Petugas kawasan']
            ],
            'forest_utilization' => [
                'Hutan Lindung',
                'Hutan Konservasi',
                'Habitat flora & fauna',
                'Pelindung pesisir'
            ],
            'programs'           => [
                'Pembibitan/persemaian',
                'Grebek sampah (pembersihan)',
                'Monitoring petugas'
            ],
            'stakeholders'       => [
                'PLN PJB',
                'Yamaha',
                'Bank DKI',
                'AEON'
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771913352/10375865-8d1c-4a25-9c75-f035865c706f.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771913461/60734378-eb69-4ff4-af32-c239aa72def5.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771913511/605e3f25-8f0c-4710-b707-f402bab5df34.png',

        ], [
            ['title' => 'Dahan pohon patah, luka terbuka, gummosis, dan konk', 'description' => 'Dahan pohon patah, luka terbuka, gummosis, dan konk', 'priority' => 'high', 'action' => 'Pembersihan area, pemasangan penyangga, dan monitoring'],
            ['title' => 'Sampah dari laut menumpuk di pesisir', 'description' => 'Sampah dari laut menumpuk di pesisir', 'priority' => 'high', 'action' => 'Pembersihan oleh petugas kawasan dan jaring sampah'],
            ['title' => 'Pembuangan lumpur', 'description' => 'Pembuangan lumpur', 'priority' => 'medium', 'action' => 'Belum ada tindak lanjut'],
        ]);

        // 5. Titik 2 Elang Laut
        $this->createFullLocation([
            'name'             => 'Titik 2 Elang Laut',
            'latitude'         => -6.1214,
            'longitude'        => 106.7399,
            'area'             => null,
            'density'          => 'lebat',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Titik 2 Elang Laut, Penjaringan, Jakarta Utara',
            'manager'          => 'DPHK',
            'year_established' => 2025,
            'health_percentage' => 70,
            'health_score'     => 'NAK: 6.4',
            'description'      => 'Titik pengamatan berada di kawasan PT. Mandara.',
            'species'          => 'Rhizophora mucronata, Sonneratia caseolaris',
        ], [
            'vegetasi' => [
                'Rhizophora mucronata',
                'Sonneratia caseolaris'
            ],
            'fauna'    => [
                'Ular Pucuk',
                'Kura-kura',
                'Kadal',
                'Ikan',
                '3 jenis burung'
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di kawasan PT. Mandara. Aktivitas di sekitarnya terbatas oleh:',
                'items'       => [
                    'Petugas kawasan',
                    'Pemancing',
                    'KTH di Kawasan Elang Laut'
                ]
            ],
            'forest_utilization' => [
                'Hutan Konservasi',
                'Habitat flora & fauna'
            ],
            'programs'           => [
                'Pembibitan/persemaian oleh KTH',
                'Penanaman'
            ],
            'stakeholders'       => [
                'AEON',
                'Avian Brands',
                'Asian Agri',
                'ASTRA',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771913958/618748fa-5023-44b8-b349-7cb5956c1345.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771914016/fbca18cd-e65b-4a58-8dee-7fd08d915cb6.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771914051/b67b28af-1090-4501-a96c-c36b27913746.png',

        ], [
            ['title' => 'Dahan patah akibat angin kencang, Kanker, Luka terbuka, brum, liana', 'description' => 'Dahan patah akibat angin kencang, Kanker, Luka terbuka, brum, liana', 'priority' => 'medium', 'action' => 'Pembersihan dan monitoring'],
            ['title' => 'Sampah pemukiman', 'description' => 'Sampah pemukiman', 'priority' => 'medium', 'action' => 'Pembersihan oleh petugas kawasan dan KTH'],
            ['title' => 'Gulma eceng gondok', 'description' => 'Gulma eceng gondok', 'priority' => 'low', 'action' => 'Belum ada tindak lanjut'],
        ]);

        // 6. TWA Angke Kapuk
        $this->createFullLocation([
            'name'             => 'TWA Angke Kapuk',
            'latitude'         => -6.1035,
            'longitude'        => 106.7334,
            'area'             => 99.82,
            'density'          => 'sedang',
            'type'             => 'pengkayaan',
            'region'           => 'Penjaringan',
            'location_address' => 'TWA Angke Kapuk, Penjaringan, Jakarta Utara',
            'manager'          => 'PT. Murindra Karya Lestari, BKSDA',
            'year_established' => 2025,
            'health_percentage' => 86.5,
            'health_score'     => 'NAK: 6.3',
            'description'      => 'Titik pengamatan berada di dekat rawa di TWA.',
            'species'          => 'Avicennia alba,
                                    Avicennia marina,
                                    Bruguiera gymnorrhiza,
                                    Excoecaria agallocha,
                                    Rhizophora apiculata,
                                    Rhizophora mucronata,
                                    Rhizophora stylosa,
                                    Sonneratia caseolaris',
        ], [
            'vegetasi' => [
                'Avicennia alba',
                'Avicennia marina',
                'Bruguiera gymnorrhiza',
                'Excoecaria agallocha',
                'Rhizophora apiculata',
                'Rhizophora mucronata',
                'Rhizophora stylosa',
                'Sonneratia caseolaris'
            ],
            'fauna'    => [
                'Monyet ekor panjang',
                'Ikan',
                'Biawak',
                'Tupai kekes',
                '17 Jenis burung'
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di dekat rawa di TWA. Aktivitas di sekitarnya terbatas oleh:',
                'items'       => [
                    'Petugas kawasan',
                    'Pengunjung',
                    'Petugas kebersihan'
                ]
            ],
            'forest_utilization' => [
                'Hutan Lindung',
                'Hutan Konservasi',
                'Habitat flora & fauna',
                'Pelindung pesisir',
                'Ekowisata'
            ],
            'programs'           => [
                'Pembibitan/persemaian oleh KTH',
                'Pembersihan rutin',
                'Penanaman mangrove',
                'Penyediaan lahan',
                'Pembuatan tanggul'
            ],
            'stakeholders'       => [
                'Samsung',
                'Toyota',
                'Badan POM',
                'Epson',
                'Korea University'
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771914509/f9eff3eb-1663-4004-8cfb-1c4b795fcb41.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771914543/da26bfc4-b916-45d6-b0e9-4af666716b1f.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771914576/59355792-3c99-4b7c-bf0e-94e4da22ccae.png',

        ], [
            ['title' => 'Dahan patah dan pohon tumbang akibat angin kencang, luka terbuka, konk, brum, kerusakan pucuk', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang, luka terbuka, konk, brum, kerusakan pucuk', 'priority' => 'high', 'action' => 'Pembersihan area terdampak, monitoring, dan penyulaman'],
            ['title' => 'Sampah dari laut dan pengunjung', 'description' => 'Sampah dari laut dan pengunjung', 'priority' => 'medium', 'action' => 'Peninggian tanggul, pembersihan oleh petugas'],
            ['title' => 'Limbah buangan', 'description' => 'Limbah buangan', 'priority' => 'medium', 'action' => 'Recovery oleh pohon mangrove selama 1 bulan'],
        ]);

        // 7. Rusun TNI AL
        $this->createFullLocation([
            'name'             => 'Rusun TNI AL',
            'latitude'         => -6.1191,
            'longitude'        => 106.9548,
            'area'             => 6,
            'density'          => 'jarang',
            'type'             => 'pengkayaan',
            'region'           => 'Cilincing',
            'location_address' => 'Rusun TNI AL, Cilincing, Jakarta Utara',
            'manager'          => 'TNI AL',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 6.9',
            'description'      => 'Titik pengamatan berada di lahan TNI AL yang merupakan perkomplekan TNI.',
            'species'          => 'Avicennia alba,
Rhizophora mucronata,
Sonneratia caseolaris,
Sonneratia alba',
        ], [
            'vegetasi' => [
                'Avicennia alba',
                'Rhizophora mucronata',
                'Sonneratia caseolaris',
                'Sonneratia alba',
            ],
            'fauna'    => [
                'Ikan',
                '6 jenis burung (Kareo padi, Kuntul besar, Blekok cina, Blekok sawah, Kokokan laut, Caladi itik)',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di lahan TNI AL yang merupakan perkomplekan TNI. Aktivitas yang teramati:',
                'items'       => [
                    'Masyarakat setempat',
                    'Pemancing',
                    'Penembak burung',
                ],
            ],
            'forest_utilization' => ['Pencegah rob'],
            'programs'           => ['Tidak ada (karena lahan milik TNI AL, masyarakat takut mengelola lahan mangrove)'],
            'stakeholders'       => ['Hanya Internal TNI AL'],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771915215/5883f41e-78ec-439e-bffc-82254cf5ad5f.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771915146/7265997f-bd10-41db-a7f9-217fd81bc2b0.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771915253/1dda92f6-c5b4-4d2b-8232-effa2f48ace4.png',
        ], [
            ['title' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'medium', 'action' => 'Pembersihan area oleh security komplek'],
            ['title' => 'Sampah dari pemukiman dan bawaan rob', 'description' => 'Sampah dari pemukiman dan bawaan rob', 'priority' => 'high', 'action' => 'Pembersihan oleh petugas sampah dan gotong royong warga'],
        ]);

        // 8. Mangrove STIP
        $this->createFullLocation([
            'name'             => 'Mangrove STIP',
            'latitude'         => -6.1020,
            'longitude'        => 106.9554,
            'area'             => 4.6,
            'density'          => 'jarang',
            'type'             => 'pengkayaan',
            'region'           => 'Cilincing',
            'location_address' => 'Mangrove STIP, Cilincing, Jakarta Utara',
            'manager'          => 'STIP',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 6.8',
            'description'      => 'Titik pengamatan berada di dalam kawasan STIP dan disebelah bangunan.',
            'species'          => 'Avicennia alba,
Avicennia marina,
Rhizophora mucronata,
Rhizophora stylosa',
        ], [
            'vegetasi' => [
                'Avicennia alba',
                'Avicennia marina',
                'Rhizophora mucronata',
                'Rhizophora stylosa',
            ],
            'fauna'    => [
                'Ikan',
                '6 jenis burung',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di dalam kawasan STIP dan disebelah bangunan. Aktivitas yang teramati:',
                'items'       => [
                    'Civitas STIP',
                ]
            ],
            'forest_utilization' => [
                'Pencegah rob',
                'Estetika',
            ],
            'programs'           => [
                'Pembibitan dan persemaian',
            ],
            'stakeholders'       => [
                'Hanya Internal STIP',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771916188/27539d64-5ec0-40ed-9448-ce36a4beae7d.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771916227/93c7895a-bdf8-4273-af13-8fcf3ff4544f.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771916245/ea133687-8b55-4cb8-994b-7de31b7359cb.png',


        ], [
            ['title' => 'Air tercemar limbah sampah yang menumpuk di pinggir rawa', 'description' => 'Air tercemar limbah sampah yang menumpuk di pinggir rawa', 'priority' => 'medium', 'action' => 'Belum ada tindak lanjut'],
            ['title' => 'Sampah dari pemukiman dan laut', 'description' => 'Sampah dari pemukiman dan laut', 'priority' => 'medium', 'action' => 'Pembersihan oleh petugas kebersihan STIP'],
            ['title' => 'Dahan patah, mati pucuk, kekeringan akibat cuaca panas', 'description' => 'Dahan patah, mati pucuk, kekeringan akibat cuaca panas', 'priority' => 'medium', 'action' => 'Pembersihan dan monitoring'],
        ]);

        // 9. Mangrove Si Pitung
        $this->createFullLocation([
            'name'             => 'Mangrove Si Pitung',
            'latitude'         => -6.0976,
            'longitude'        => 106.9606,
            'area'             => 5.5,
            'density'          => 'jarang',
            'type'             => 'pengkayaan',
            'region'           => 'Cilincing',
            'location_address' => 'Mangrove Si Pitung, Cilincing, Jakarta Utara',
            'manager'          => 'Masyarakat',
            'year_established' => 2025,
            'health_percentage' => 86.8,
            'health_score'     => 'NAK: 6.4',
            'description'      => 'Titik pengamatan berada di dekat tambak Museum Si Pitung.',
            'species'          => 'Avicennia marina,
Rhizophora mucronata',
        ], [
            'vegetasi' => [
                'Avicennia marina',
                'Rhizophora mucronata',
            ],
            'fauna'    => [
                'Ikan',
                'Biawak',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di dekat tambak Museum Si Pitung. Aktivitas di sekitarnya terbatas oleh masyarakat dan pemilik tambak.',
                'items'       => [
                    'Masyarakat',
                    'Pemilik tambak',
                ],
            ],
            'forest_utilization' => [
                'Hutan konservasi',
                'Habitat flora & fauna',
                'Pengendali banjir'
            ],
            'programs'           => [
                'Pembibitan dan persemaian',
                'Pembersihan rutin',
            ],
            'stakeholders'       => ['Masyarakat sekitar'],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771916583/8900e34a-2574-489f-b821-46eb7e0511b6.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771916611/24bfd759-40f6-4f1f-93bc-170606a2dbbf.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771916633/a4d8ec4c-3432-4e67-8f70-8308a93b8120.png',

        ], [
            ['title' => 'Dahan dan akar patah, konk, brum, kerusakan pucuk, benalu, luka terbuka', 'description' => 'Dahan dan akar patah, konk, brum, kerusakan pucuk, benalu, luka terbuka', 'priority' => 'medium', 'action' => 'Pembersihan dan monitoring'],
            ['title' => 'Sampah dari laut dan pemukiman', 'description' => 'Sampah dari laut dan pemukiman', 'priority' => 'medium', 'action' => 'Pembersihan oleh PPSU'],
        ]);
        // 10. Pasmar 1 TNI AL
        $this->createFullLocation([
            'name'             => 'Pasmar 1 TNI AL',
            'latitude'         => -6.1131,
            'longitude'        => 106.9560,
            'area'             => 2.7,
            'density'          => 'lebat',
            'type'             => 'dilindungi',
            'region'           => 'Cilincing',
            'location_address' => 'Pasmar 1 TNI AL, Cilincing, Jakarta Utara',
            'manager'          => 'TNI AL',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 6.5',
            'description'      => 'Titik pengamatan berada di dalam kantor Pasmar TNI AL, sehingga aktivitas di sekitarnya terbatas oleh marinir TNI AL dan pemilik tambak.',
            'species'          => 'Avicennia alba,
Bruguiera gymnorrhiza,
Nypa fruticans,
Rhizophora mucronata,
Rhizophora stylosa',
        ], [
            'vegetasi' => [
                'Avicennia alba',
                'Bruguiera gymnorrhiza',
                'Nypa fruticans',
                'Rhizophora mucronata',
                'Rhizophora stylosa',
            ],
            'fauna'    => [
                'Ikan',
                '3 Jenis Burung (Kuntul, Kutilang, Gereja)',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di dalam kantor Pasmar TNI AL, sehingga aktivitas di sekitarnya terbatas oleh marinir TNI AL dan pemilik tambak.',
                'items'       => [
                    'Marinir TNI AL',
                    'Pemilik tambak',
                ],
            ],
            'forest_utilization' => [
                'Habitat flora & fauna',
                'Pembatas tambak',
                'Penyediaan bibit',
            ],
            'programs'           => [
                'Pembibitan dan persemaian',
                'Pembersihan rutin',
                'Penanaman internal',
            ],
            'stakeholders'       => ['Hanya Internal TNI AL'],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771917404/e9ebdca1-672a-443c-8704-a5b39f890264.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771917479/36ba5244-4f20-436f-b458-4c6d419fbba1.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771917523/8270f624-0de0-46ed-a2ec-a1bd306bf1f9.png',


        ], [
            ['title' => 'Limbah mencemari air hingga menghitam', 'description' => 'Limbah mencemari air hingga menghitam', 'priority' => 'medium', 'action' => 'Belum ada tindak lanjut'],
            ['title' => 'Sampah domestik', 'description' => 'Sampah domestik', 'priority' => 'medium', 'action' => 'Pembersihan rutin oleh marinir'],
            ['title' => 'Kanker, Batang pecah, Cabang patah atau mat, brum, kerusakan pucuk', 'description' => 'Kanker, Batang pecah, Cabang patah atau mat, brum, kerusakan pucuk', 'priority' => 'high', 'action' => 'Pembersihan dan monitoring'],
        ]);
        // 11. Pantai Marunda
        $this->createFullLocation([
            'name'             => 'Pantai Marunda',
            'latitude'         => -6.0939,
            'longitude'        => 106.9626,
            'area'             => null,
            'density'          => 'jarang',
            'type'             => 'pengkayaan',
            'region'           => 'Cilincing',
            'location_address' => 'Pantai Marunda, Cilincing, Jakarta Utara',
            'manager'          => 'Masyarakat',
            'year_established' => 2025,
            'health_percentage' => 81.6,
            'health_score'     => 'NAK: 6.8',
            'description'      => 'Titik pengamatan berada di pesisir Pantai Marunda, sehingga aktivitas di sekitarnya terbatas oleh wisatawan dan pedagang warung.',
            'species'          => 'Avicennia alba,
Avicennia marina,
Rhizophora mucronata',
        ], [
            'vegetasi' => [
                'Avicennia alba',
                'Avicennia marina',
                'Rhizophora mucronata',
            ],
            'fauna'    => [
                'Ikan',
                'Burung Kuntul',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di pesisir Pantai Marunda, sehingga aktivitas di sekitarnya terbatas oleh wisatawan dan pedagang warung.',
                'items'       => [
                    'Wisatawan',
                    'Pedagang warung',
                ],
            ],
            'forest_utilization' => [
                'Habitat flora & fauna',
                'Penahan ombak dan abrasi',
                'Penahan angin kencang',
            ],
            'programs'           => [
                'Penanaman (namun tidak berhasil)',
            ],
            'stakeholders'       => [
                'Komunitas Rumah Mangrove Marunda',
                'Beberapa CSR',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771918207/0c738b81-7be3-46ab-9a4b-a15f2d65d577.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771918240/ce8de372-cc21-4268-ba72-8a9663ee1bfa.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771918246/f13cf109-1047-483e-b7c5-6b4ce841cf94.png',
        ], [
            ['title' => 'Bibit mati tersapu ombak', 'description' => 'Bibit mati tersapu ombak', 'priority' => 'medium', 'action' => 'Belum ada tindak lanjut'],
            ['title' => 'Sampah domestik dan kiriman rob', 'description' => 'Sampah domestik dan kiriman rob', 'priority' => 'medium', 'action' => 'Pembersihan rutin oleh PPSU dan warga setempat'],
            ['title' => 'Angin kencang mematahkan dahan dan merusak warung warga', 'description' => 'Angin kencang mematahkan dahan dan merusak warung warga', 'priority' => 'medium', 'action' => 'Pembersihan dahan patahan oleh warga dan petugas kebersihan'],
            ['title' => 'Dahan dan akar patah, brum, kerusakan pucuk, luka terbuka, gumosis, batang pecah', 'description' => 'Dahan dan akar patah, brum, kerusakan pucuk, luka terbuka, gumosis, batang pecah', 'priority' => 'high', 'action' => 'Pembersihan dan monitoring'],
        ]);
        // 12. Pulau Kelapa Dua
        $this->createFullLocation([
            'name'             => 'Pulau Kelapa Dua',
            'latitude'         => -6.0976,
            'longitude'        => 106.9606,
            'area'             => 5.5,
            'density'          => 'jarang',
            'type'             => 'rehabilitasi',
            'region'           => 'kepulauan seribu utara',
            'location_address' => 'Pulau Kelapa Dua, kepulauan seribu utara, Jakarta Utara',
            'manager'          => 'TNKps',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 6',
            'description'      => 'Titik pengamatan berada di kawasan Taman Nasional.',
            'species'          => 'Bruguiera cylindrica,
Bruguiera gymnorrhiza,
Ceriops tagal,
Excoecaria agallocha,
Rhizophora apiculata,
Rhizophora mucronata,
Rhizophora stylosa,
Xylocarpus moluccensis',
        ], [
            'vegetasi' => [
                'Bruguiera cylindrica',
                'Bruguiera gymnorrhiza',
                'Ceriops tagal',
                'Excoecaria agallocha',
                'Rhizophora apiculata',
                'Rhizophora mucronata',
                'Rhizophora stylosa',
                'Xylocarpus moluccensis',
            ],
            'fauna'    => [
                'Ikan',
                'Kepiting',
                '2 jenis Burung',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di kawasan Taman Nasional. Aktivitas yang teramati: masyarakat, pengelola TN, dan pengunjung.',
                'items'       => [
                    'Masyarakat',
                    'Pengelola TN',
                    'Pengunjung',
                ],
            ],
            'forest_utilization' => [
                'Taman Nasional',
                'Hutan konservasi',
                'Pelindung pesisir',
                'Ekowisata',
            ],
            'programs'           => [
                'Pembibitan dan persemaian',
                'Penanaman',
                'Perawatan Taman Nasional',
            ],
            'stakeholders'       => [
                'Nusantara Regas',
                'Bank DKI',
                'Oseas',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771919452/080049d3-35cb-4a21-ae6b-835bb0830710.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771919480/2610eaf1-3b28-481d-92ce-70e8540495be.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771919513/df8c7655-33f2-4eb7-8d76-df9bedcff8a5.png',

        ], [
            ['title' => 'Air tercemar tumpahan minyak', 'description' => 'Air tercemar tumpahan minyak', 'priority' => 'medium', 'action' => 'Gotong royong pembersihan minyak oleh warga'],
            ['title' => 'Sampah dari laut dan pemukiman', 'description' => 'Sampah dari laut dan pemukiman', 'priority' => 'medium', 'action' => 'Pembersihan oleh petugas kebersihan PPSU'],
            ['title' => 'Patah cabang pohon dan pertumbuhan terhambat', 'description' => 'Patah cabang pohon dan pertumbuhan terhambat', 'priority' => 'medium', 'action' => 'Pembersihan dan penambahan unsur hara'],
        ]);
        // 13. Pulau Pramuka
        $this->createFullLocation([
            'name'             => 'Pulau Pramuka',
            'latitude'         => -5.7476,
            'longitude'        => 106.6146,
            'area'             => 1.06,
            'density'          => 'jarang',
            'type'             => 'rehabilitasi',
            'region'           => 'kepulauan seribu selatan',
            'location_address' => 'Pulau Pramuka, kepulauan seribu selatan, Jakarta Utara',
            'manager'          => 'TNKps',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 5.9',
            'description'      => 'Titik pengamatan berada di kawasan Taman Nasional.',
            'species'          => 'Rhizophora stylosa',
        ], [
            'vegetasi' => [
                'Rhizophora stylosa',
            ],
            'fauna'    => [
                'Ikan',
                'Kepiting',
                'Biawak Air',
                '11 jenis Burung',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di kawasan Taman Nasional. Aktivitas yang teramati: masyarakat, pengelola TN, dan pengunjung.',
                'items'       => [
                    'Masyarakat',
                    'Pengelola TN',
                    'Pengunjung',
                ],
            ],
            'forest_utilization' => [
                'Taman Nasional',
                'Hutan konservasi',
                'Penyerap karbon',
                'Pelindung pesisir',
                'Ekowisata',
            ],
            'programs'           => [
                'Pembibitan dan persemaian',
                'Penanaman',
                'Perawatan Taman Nasional',
            ],
            'stakeholders'       => [
                'Nusantara Regas',
                'IPB University',
                'Astra',
                'Binus University',
                'Pertamina PHE OSES',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771919762/21ed0fb1-530b-4e05-bc9a-dad5c55fccaa.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771919810/2de228e4-da55-4cb5-87e4-65a7baa07610.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771919948/d8bd295b-ef41-4c88-97d7-c65310362952.png',
        ], [
            ['title' => 'Tumpahan limbah minyak pertamina', 'description' => 'Tumpahan limbah minyak pertamina', 'priority' => 'medium', 'action' => 'Pembersihan oleh PPSU dan warga setempat (gotong royong)'],
            ['title' => 'Sampah dari laut terjebak dekat pesisir', 'description' => 'Sampah dari laut terjebak dekat pesisir', 'priority' => 'medium', 'action' => 'Pembersihan sampah oleh LH dan PPSU rutin (namun belum optimal)'],
            ['title' => 'Patah cabang pohon, konk, batang pecah', 'description' => 'Patah cabang pohon, konk, batang pecah', 'priority' => 'medium', 'action' => 'Pembersihan area dan pemasangan penyangga ke cabang serta monitoring'],
        ]);
        // 14. Pulau Tidung Besar dan Tidung Kecil
        $this->createFullLocation([
            'name'             => 'Pulau Tidung Besar dan Tidung Kecil',
            'latitude'         => -5.7993,
            'longitude'        => 106.4978,
            'area'             => 1.43,
            'density'          => 'jarang',
            'type'             => 'rehabilitasi',
            'region'           => 'kepulauan seribu selatan',
            'location_address' => 'Pulau Tidung Besar dan Tidung Kecil, kepulauan seribu selatan, Jakarta Utara',
            'manager'          => 'Pemkab',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 5.7',
            'description'      => 'Titik pengamatan berada di Pulau Tidung Kecil, aktivitas yang teramati: pengelola mangrove dan pengunjung.',
            'species'          => 'Excoecaria agallocha,
Rhizophora apiculata,
Rhizophora mucronata,
Rhizophora stylosa',
        ], [
            'vegetasi' => [
                'Excoecaria agallocha',
                'Rhizophora apiculata',
                'Rhizophora mucronata',
                'Rhizophora stylosa',
            ],
            'fauna'    => [
                'Ikan',
                'Kepiting',
                'Biawak Air',
                '7 jenis Burung',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di Pulau Tidung Kecil, aktivitas yang teramati: pengelola mangrove dan pengunjung.',
                'items'       => [
                    'Pengelola mangrove',
                    'Pengunjung',
                ],
            ],
            'forest_utilization' => [
                'Hutan konservasi',
                'Pelindung pesisir',
                'Ekowisata',
                'Habitat flora dan fauna',
            ],
            'programs'           => [
                'Pembibitan dan persemaian',
                'Penanaman',
                'Sosialisasi/edukasi wisatawan',
            ],
            'stakeholders'       => [
                'Astra',
                'Yamaha',
                'BCA',
                'Pertamina',
                'Telkom Indonesia',
                'Universitas Indonesia',
                'IPB University',
                'Universitas Negeri Jakarta',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771986694/f7343fcc-b68f-4502-9d10-249695f72485.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771986714/9564ffd8-10a0-4fa6-9f9d-1aaa22c358ac.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771986774/3f673857-0bf7-4531-8be9-659f14116b5c.png',

        ], [
            ['title' => 'Air tercemar tumpahan minyak', 'description' => 'Air tercemar tumpahan minyak', 'priority' => 'medium', 'action' => 'Gotong royong pembersihan minyak oleh warga dan PPSU'],
            ['title' => 'Sampah dari laut dan pemukiman', 'description' => 'Sampah dari laut dan pemukiman', 'priority' => 'medium', 'action' => 'Pemasangan jaring sampah dan pembersihan oleh LH dan PPSU'],
        ]);
        // 15. Pulau Lancang Besar
        $this->createFullLocation([
            'name'             => 'Pulau Lancang Besar',
            'latitude'         => -5.9262,
            'longitude'        => 106.5842,
            'area'             => 5.2,
            'density'          => 'sedang',
            'type'             => 'dilindungi',
            'region'           => 'kepulauan seribu selatan',
            'location_address' => 'Pulau Lancang Besar, kepulauan seribu selatan, Jakarta Utara',
            'manager'          => 'Pemkab',
            'year_established' => 2025,
            'health_percentage' => 100,
            'health_score'     => 'NAK: 6.9',
            'description'      => 'Titik pengamatan berada di utara pulau. Aktivitas yang teramati: masyarakat, pengunjung, komunitas pengelola mangrove.',
            'species'          => 'Bruguiera gymnorrhiza,
Lumnitzera littorea,
Rhizophora apiculata,
Rhizophora mucronata,
Rhizophora stylosa',
        ], [
            'vegetasi' => [
                'Bruguiera gymnorrhiza',
                'Lumnitzera littorea',
                'Rhizophora apiculata',
                'Rhizophora mucronata',
                'Rhizophora stylosa',
            ],
            'fauna'    => [
                'Ikan',
                'Kepiting',
                'Biawak Air',
                '2 jenis Burung',
            ],
            'activities' => [
                'description' => 'Titik pengamatan berada di utara pulau. Aktivitas yang teramati: masyarakat, pengunjung, komunitas pengelola mangrove.',
                'items'       => [
                    'Masyarakat',
                    'Pengunjung',
                    'Komunitas pengelola mangrove',
                ],
            ],
            'forest_utilization' => [
                'Hutan konservasi',
                'Penyerap karbon',
                'Pelindung pesisir',
            ],
            'programs'           => [
                'Pembibitan dan persemaian',
                'Penanaman',
            ],
            'stakeholders'       => [
                'Pertamina',
                'Bank DKI',
                'IPB University',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771987163/71a34111-405e-41d9-9157-33d17dc493e4.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771987250/fe637fe0-a7dc-45e6-af9d-b699e0e0765c.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771987254/cf04c9e0-be80-49c7-922c-46cafd1e5736.png',

        ], [
            ['title' => 'Tumpahan limbah minyak Pertamina', 'description' => 'Tumpahan limbah minyak Pertamina', 'priority' => 'medium', 'action' => 'Pembersihan oleh PPSU dan warga setempat serta mengundang pihak Pertamina'],
            ['title' => 'Sampah dari laut', 'description' => 'Sampah dari laut', 'priority' => 'medium', 'action' => 'Pembersihan sampah oleh LH dan PPSU rutin'],
            ['title' => 'Patah cabang dan akar pohon, luka terbuka, kerusakan pucuk dan gummosis', 'description' => 'Patah cabang dan akar pohon, luka terbuka, kerusakan pucuk dan gummosis', 'priority' => 'medium', 'action' => 'Pembersihan area dan pemasangan penyangga ke cabang serta monitoring'],
        ]);
        // 16. Titik 1 Elang Laut
        $this->createFullLocation([
            'name'             => 'Titik 1 Elang Laut',
            'latitude'         => -6.1196,
            'longitude'        => 106.7354,
            'area'             => 22,
            'density'          => 'lebat',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Titik 1 Elang Laut, Penjaringan, Jakarta Utara',
            'manager'          => 'Dinas Pertamanan dan Hutan Kota',
            'year_established' => 2024,
            'health_percentage' => 94.3,
            'health_score'     => 'NAK: 6.5',
            'description'      => 'Titik pengamatan dekat jalan tol dan PLN, jauh dari pemukiman. Aktivitas terbatas oleh pedagang, pemancing, dan petugas kawasan.',
            'species'          => 'Avicennia sp.,
Rhizophora sp.,
Nipah',
        ], [
            'vegetasi' => [
                'Avicennia sp.',
                'Rhizophora sp.',
                'Nipah',
            ],
            'fauna'    => [
                'Monyet ekor panjang',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat jalan tol dan PLN, jauh dari pemukiman. Aktivitas terbatas oleh pedagang, pemancing, dan petugas kawasan.',
                'items'       => [
                    'Pedagang',
                    'Pemancing',
                    'Petugas kawasan',
                ],
            ],
            'forest_utilization' => [
                'Hutan Lindung',
                'Hutan konservasi',
                'Habitat flora & fauna',
                'Penyerap karbon',
            ],
            'programs'           => [
                'Penanaman mangrove oleh berbagai CSR dan komunitas',
                'Edukasi mangrove dan penyedia lahan penanaman',
            ],
            'stakeholders'       => [
                'AEON',
                'Avian Brands',
                'Asian Agri',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771987766/54650baf-ab1d-4f30-a027-a7e0eb8d175e.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771987783/14d48336-4d42-4991-a71b-5c503c5bcea7.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771987804/f0aaa08e-1d2f-41fe-a040-6880c08e8f53.png',

        ], [
            ['title' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'medium', 'action' => 'Pembersihan area terdampak'],
            ['title' => 'Curah hujan tinggi dan banjir menyebabkan bibit terendam', 'description' => 'Curah hujan tinggi dan banjir menyebabkan bibit terendam', 'priority' => 'medium', 'action' => 'Penyulaman bibit dan penyedotan air saat banjir'],
            ['title' => 'Kekeringan menghambat pertumbuhan pohon', 'description' => 'Kekeringan menghambat pertumbuhan pohon', 'priority' => 'medium', 'action' => 'Penggenangan air'],
            ['title' => 'Hama keong', 'description' => 'Hama keong', 'priority' => 'medium', 'action' => 'Pembersihan rutin hama dan gulma'],
        ]);
        // 17. Ekowisata Mangrove PIK
        $this->createFullLocation([
            'name'             => 'Ekowisata Mangrove PIK',
            'latitude'         => -6.1223,
            'longitude'        => 106.7551,
            'area'             => 13,
            'density'          => 'lebat',
            'type'             => 'dilindungi',
            'region'           => 'Penjaringan',
            'location_address' => 'Ekowisata Mangrove PIK, Penjaringan, Jakarta Utara',
            'manager'          => 'Dinas Pertamanan dan Hutan Kota',
            'year_established' => 2024,
            'health_percentage' => 79.4,
            'health_score'     => 'NAK: 6.85',
            'description'      => 'Titik pengamatan dekat dengan jalan tol dan pemukiman sehingga aktivitas di sekitarnya didominasi oleh pedagang, pemancing, pengunjung, dan petugas kawasan.',
            'species'          => 'Avicennia sp.,
Rhizophora sp.,
Nipah,
Pidada',
        ], [
            'vegetasi' => [
                'Avicennia sp.',
                'Rhizophora sp.',
                'Nipah',
                'Pidada',
            ],
            'fauna'    => [
                'Monyet ekor panjang',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat dengan jalan tol dan pemukiman sehingga aktivitas di sekitarnya didominasi oleh pedagang, pemancing, pengunjung, dan petugas kawasan.',
                'items'       => [
                    'Pedagang',
                    'Pemancing',
                    'Pengunjung',
                    'Petugas kawasan',
                ],
            ],
            'forest_utilization' => [
                'Ekowisata',
                'Hutan Produksi (Produksi selai, dodol, sirup)',
                'Hutan konservasi',
                'Habitat flora & fauna',
                'Penyerap karbon',
            ],
            'programs'           => [
                'Program Grebek Sampah',
                'Edukasi mangrove dan penyedia lahan penanaman',
                'Penanaman mangrove oleh CSR, komunitas & Dinas terkait',
            ],
            'stakeholders'       => [
                'AEON',
                'BAF',
                'Yamaha',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771988218/50d58cec-73da-453d-8989-2de955f7cee0.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771988234/22cc9614-e186-4399-96e8-db28f7b37b48.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771988244/e4907aac-8180-47cc-9eca-f86a6a1ccd9b.png',

        ], [
            ['title' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'medium', 'action' => 'Pembersihan area terdampak'],
            ['title' => 'Curah hujan tinggi & rob menyebabkan bibit terendam', 'description' => 'Curah hujan tinggi & rob menyebabkan bibit terendam', 'priority' => 'medium', 'action' => 'Penyulaman bibit dan pembuatan saluran air'],
            ['title' => 'Gangguan monyet ekor panjang dan hama serangga', 'description' => 'Gangguan monyet ekor panjang dan hama serangga', 'priority' => 'medium', 'action' => 'Pembersihan rutin hama dan gulma'],
            ['title' => 'Pencemaran air oleh sampah dan limbah domestik', 'description' => 'Pencemaran air oleh sampah dan limbah domestik', 'priority' => 'medium', 'action' => 'Pembuatan tanggul / waring'],
        ]);
        // 18. Pos 3 Hutan Lindung
        $this->createFullLocation([
            'name'             => 'Pos 3 Hutan Lindung',
            'latitude'         => -6.1041,
            'longitude'        => 106.7518,
            'area'             => 44.7,
            'density'          => 'sedang',
            'type'             => 'pengkayaan',
            'region'           => 'Penjaringan',
            'location_address' => 'Pos 3 Hutan Lindung, Penjaringan, Jakarta Utara',
            'manager'          => 'Dinas Pertamanan dan Hutan Kota',
            'year_established' => 2024,
            'health_percentage' => 84.1,
            'health_score'     => 'NAK: 6.4',
            'description'      => 'Titik pengamatan dekat dengan pemukiman dan pesisir laut, sehingga aktivitas di sekitar hutan melibatkan pedagang, pemancing, serta petugas kawasan.',
            'species'          => 'Rhizophora sp.,
Bruguiera sp.,
Avicennia sp.,
Ketapang,
Trembesi,
Flamboyan,
Nyamplung',
        ], [
            'vegetasi' => [
                'Rhizophora sp.',
                'Bruguiera sp.',
                'Avicennia sp.',
                'Ketapang',
                'Trembesi',
                'Flamboyan',
                'Nyamplung',
            ],
            'fauna'    => [
                'Monyet ekor panjang',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat dengan pemukiman dan pesisir laut, sehingga aktivitas di sekitar hutan melibatkan pedagang, pemancing, serta petugas kawasan.',
                'items'       => [
                    'Pedagang',
                    'Pemancing',
                    'Petugas kawasan',
                ],
            ],
            'forest_utilization' => [
                'Hutan Produksi (Produksi selai, dodol, sirup)',
                'Hutan konservasi',
                'Habitat flora & fauna (Monyet ekor panjang)',
                'Penahan abrasi',
            ],
            'programs'           => [
                'Program Grebek Sampah',
                'Edukasi mangrove dan penyedia lahan penanaman',
                'Penanaman mangrove oleh CSR, komunitas & Dinas terkait',
            ],
            'stakeholders'       => [
                'AEON',
                'Yamaha',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771989233/e37d63dc-a90d-485d-9e84-7224f5461e7a.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771989276/1c4bafd3-b5da-407e-8f83-7b2a6163147f.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771989272/c7e97b02-87d6-4922-a058-c84442a29b32.png',

        ], [
            ['title' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'medium', 'action' => 'Pembersihan area terdampak'],
            ['title' => 'Curah hujan tinggi & rob menyebabkan bibit terendam', 'description' => 'Curah hujan tinggi & rob menyebabkan bibit terendam', 'priority' => 'medium', 'action' => 'Penyulaman bibit yang mati'],
            ['title' => 'Pencemaran air oleh sampah dan limbah domestik', 'description' => 'Pencemaran air oleh sampah dan limbah domestik', 'priority' => 'medium', 'action' => 'Penyaringan sampah dengan waring'],
        ]);
        // 19. Komunitas Mangrove Muara Angke
        $this->createFullLocation([
            'name'             => 'Komunitas Mangrove Muara Angke',
            'latitude'         => -6.1003,
            'longitude'        => 106.7674,
            'area'             => 2.3,
            'density'          => 'lebat',
            'type'             => 'pengkayaan',
            'region'           => 'Penjaringan',
            'location_address' => 'Komunitas Mangrove Muara Angke, Penjaringan, Jakarta Utara',
            'manager'          => 'Komunitas & Masyarakat',
            'year_established' => 2024,
            'health_percentage' => 84.5,
            'health_score'     => 'NAK: 5.8',
            'description'      => 'Titik pengamatan dekat dengan pemukiman dan pesisir laut, aktivitas masyarakat meliputi pemukiman, nelayan, serta anggota komunitas.',
            'species'          => 'Avicennia sp.,
Rhizophora sp.,
Nipah,
Pidada',
        ], [
            'vegetasi' => [
                'Avicennia sp.',
                'Rhizophora sp.',
                'Nipah',
                'Pidada',
            ],
            'fauna'    => [
                'Ikan gobi',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat dengan pemukiman dan pesisir laut, aktivitas masyarakat meliputi pemukiman, nelayan, serta anggota komunitas.',
                'items'       => [
                    'Pemukiman',
                    'Nelayan',
                    'Anggota Komunitas',
                ],
            ],
            'forest_utilization' => [
                'Hutan Produksi (Produksi selai, dodol, sirup)',
                'Hutan konservasi',
                'Penahan abrasi',
            ],
            'programs'           => [
                'Penanaman mangrove oleh CSR, komunitas, dan KTH',
                'Penyedia lahan dan bibit penanaman',
            ],
            'stakeholders'       => [
                'Astra',
                'PJB',
                'PLN Nusantara Power',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771989635/ad0f6648-53de-4a64-95c9-7bd39da9ed2b.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771989644/264b2798-2b2f-4ff9-bf4f-599f90995639.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771989646/f1405b6d-5a64-44e4-8e98-65de77c258e2.png',

        ], [
            ['title' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'description' => 'Dahan patah dan pohon tumbang akibat angin kencang', 'priority' => 'medium', 'action' => 'Pembersihan area terdampak'],
            ['title' => 'Curah hujan tinggi & air pasang sehingga bibit terendam', 'description' => 'Curah hujan tinggi & air pasang sehingga bibit terendam', 'priority' => 'medium', 'action' => 'Penyulaman bibit yang mati'],
            ['title' => 'Pencemaran air oleh sampah', 'description' => 'Pencemaran air oleh sampah', 'priority' => 'medium', 'action' => 'Penyaringan dengan waring, pembersihan area sampah'],
        ]);
        // 20. Suaka Margasatwa Muara Angke
        $this->createFullLocation([
            'name'             => 'Suaka Margasatwa Muara Angke',
            'latitude'         => -6.1156,
            'longitude'        => 106.7692,
            'area'             => 25,
            'density'          => 'sedang',
            'type'             => 'pengkayaan',
            'region'           => 'Penjaringan',
            'location_address' => 'Suaka Margasatwa Muara Angke, Penjaringan, Jakarta Utara',
            'manager'          => 'Komunitas & Masyarakat',
            'year_established' => 2024,
            'health_percentage' => 82.4,
            'health_score'     => 'NAK: 5.7',
            'description'      => 'Titik pengamatan dekat dengan pemukiman, kawasan tidak dibuka untuk umum, sehingga aktivitas terbatas pada petugas kawasan.',
            'species'          => 'Avicennia sp.,
Rhizophora sp.,
Nipah,
Pidada',
        ], [
            'vegetasi' => [
                'Avicennia sp.',
                'Rhizophora sp.',
                'Nipah',
                'Pidada',
            ],
            'fauna'    => [
                'Monyet ekor panjang',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat dengan pemukiman, kawasan tidak dibuka untuk umum, sehingga aktivitas terbatas pada petugas kawasan.',
                'items'       => [
                    'Petugas Kawasan',
                ],
            ],
            'forest_utilization' => [
                'Hutan Lindung',
                'Hutan konservasi',
                'Habitat flora dan fauna',
            ],
            'programs'           => [
                'Penanaman mangrove oleh CSR, komunitas, dan KTH',
                'Penyedia lahan penanaman',
            ],
            'stakeholders'       => [
                'BKSDA',
                'Yayasan YKAN',
                'Indofood',
                'PLN Nusantara Power',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990109/020cb1bc-9de5-4f03-8ecb-415a2c3a8612.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990129/41ccdea6-7c7b-47ae-b848-c774d1ab65ee.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990126/14ef8f12-4a15-4f58-8322-86ff96bcc96d.png',

        ], [
            ['title' => 'Gangguan monyet ekor panjang', 'description' => 'Gangguan monyet ekor panjang', 'priority' => 'medium', 'action' => 'Pemberian makan rutin'],
            ['title' => 'Gangguan gulma (eceng gondok)', 'description' => 'Gangguan gulma (eceng gondok)', 'priority' => 'medium', 'action' => 'Pembersihan gulma (program belum efektif)'],

        ]);
        // 21. Pulau Untung Jawa
        $this->createFullLocation([
            'name'             => 'Pulau Untung Jawa',
            'latitude'         => -5.9755,
            'longitude'        => 106.7044,
            'area'             => 4.1,
            'density'          => 'jarang',
            'type'             => 'rehabilitasi',
            'region'           => 'kepulauan seribu selatan',
            'location_address' => 'Pulau Untung Jawa, kepulauan seribu selatan, Jakarta Utara',
            'manager'          => 'Pegawai BKSDA & Masyarakat',
            'year_established' => 2024,
            'health_percentage' => 83.3,
            'health_score'     => 'NAK: 5.6',
            'description'      => 'Titik pengamatan dekat pemukiman dan berada di pesisir laut, aktivitas didominasi oleh masyarakat setempat, nelayan, dan petugas pengelola.',
            'species'          => 'Avicennia sp.,
Rhizophora sp.,
Bruguiera,
Pidada',
        ], [
            'vegetasi' => [
                'Avicennia sp.',
                'Rhizophora sp.',
                'Bruguiera',
                'Pidada',
            ],
            'fauna'    => [
                'Kepiting',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat pemukiman dan berada di pesisir laut, aktivitas didominasi oleh masyarakat setempat, nelayan, dan petugas pengelola.',
                'items'       => [
                    'Masyarakat setempat',
                    'Nelayan',
                    'Petugas Pengelola',
                ],
            ],
            'forest_utilization' => [
                'Penahan abrasi',
                'Habitat flora dan fauna',
                'Hutan konservasi',
                'Ekowisata',
            ],
            'programs'           => [
                'Penanaman mangrove oleh CSR, komunitas, dan KTH',
                'Penyuluhan dan edukasi mangrove',
            ],
            'stakeholders'       => [
                'BKSDA',
                'Sobat Bluwok',
                'Pertamina',
                'Korpolairud',
                'Mayora',
                'PLN Nusantara Power',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990562/3aae4aa9-c680-4e12-9bc6-a3d3d5e70a0b.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990566/e82dd05e-d5ee-4473-9e1e-eea40a30a8cb.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990570/7f2f55cf-2d37-4afb-9a58-ff9921fa88dd.png',

        ], [
            ['title' => 'Ombak tinggi dan rob membuat bibit terendam', 'description' => 'Ombak tinggi dan rob membuat bibit terendam', 'priority' => 'medium', 'action' => 'Tanggul pemecah gelombang'],
            ['title' => 'Sampah dari area wisata dan laut', 'description' => 'Sampah dari area wisata dan laut', 'priority' => 'medium', 'action' => 'Pengelolaan sampah oleh petugas'],
            ['title' => 'Tumpahan minyak Pertamina', 'description' => 'Tumpahan minyak Pertamina', 'priority' => 'medium', 'action' => 'Penanaman kembali oleh Pertamina'],
        ]);
        // 22. Pulau Harapan
        $this->createFullLocation([
            'name'             => 'Pulau Harapan',
            'latitude'         => -5.6539,
            'longitude'        => 106.5760,
            'area'             => 2.68,
            'density'          => 'sedang',
            'type'             => 'pengkayaan',
            'region'           => 'kepulauan seribu utara',
            'location_address' => 'Pulau Harapan, kepulauan seribu utara, Jakarta Utara',
            'manager'          => 'Komunitas & Masyarakat',
            'year_established' => 2024,
            'health_percentage' => 76.7,
            'health_score'     => 'NAK: 6.4',
            'description'      => 'Titik pengamatan dekat pemukiman dan berada di pesisir laut, aktivitas didominasi oleh masyarakat setempat, nelayan, dan petugas pengelola.',
            'species'          => 'Avicennia sp.,
Rhizophora sp.,
Bruguiera,
Waru,
Lamtoro,
Santigi,
Cemara,
Tancang,
Beringin',
        ], [
            'vegetasi' => [
                'Avicennia sp.',
                'Rhizophora sp.',
                'Bruguiera',
                'Waru',
                'Lamtoro',
                'Santigi',
                'Cemara',
                'Tancang',
                'Beringin',
            ],
            'fauna'    => [
                'Kepiting',
                'Ikan kecil',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat dengan pemukiman dan pesisir laut, aktivitas masyarakat meliputi pemukiman, nelayan, serta anggota komunitas.',
                'items'       => [
                    'Masyarakat setempat',
                    'Nelayan',
                    'Petugas Pengelola',
                ],
            ],
            'forest_utilization' => [
                'Penahan abrasi dan sedimentasi',
                'Habitat flora dan fauna',
                'Hutan konservasi',
                'Ekowisata',
            ],
            'programs'           => [
                'Penanaman mangrove oleh CSR, komunitas, dan KTH',
                'Penyuluhan dan edukasi mangrove',
            ],
            'stakeholders'       => [
                'BUMN',
                'Pertamina',
                'Grab',
                'Traveloka',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990871/a176dd97-5354-4027-bdd3-ca7c86167d2a.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990876/539ac951-c7d4-485e-bbb6-5bd6040c3516.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771990889/a78a3f12-d0f4-4fbe-a28f-320280c818c9.png',

        ], [
            ['title' => 'Limbah pemukiman terkumpul di area mangrove', 'description' => 'Limbah pemukiman terkumpul di area mangrove', 'priority' => 'medium', 'action' => 'Pembangunan tanggul pembatas'],
            ['title' => 'Banjir rob mengakibatkan kematian bibit', 'description' => 'Banjir rob mengakibatkan kematian bibit', 'priority' => 'medium', 'action' => 'Penyulaman mangrove'],
            ['title' => 'Tumpahan minyak Pertamina', 'description' => 'Tumpahan minyak Pertamina', 'priority' => 'medium', 'action' => 'Penanaman kembali oleh Pertamina'],
        ]);
        // 23. Pulau Kelapa
        $this->createFullLocation([
            'name'             => 'Pulau Kelapa',
            'latitude'         => -5.6571,
            'longitude'        => 106.5677,
            'area'             => 3.2,
            'density'          => 'lebat',
            'type'             => 'pengkayaan',
            'region'           => 'kepulauan seribu utara',
            'location_address' => 'Pulau Kelapa, kepulauan seribu utara, Jakarta Utara',
            'manager'          => 'Pegawai BKSDA & Masyarakat',
            'year_established' => 2024,
            'health_percentage' => 87.2,
            'health_score'     => 'NAK: 6.1',
            'description'      => 'Titik pengamatan dekat dengan pemukiman dan berada di pesisir laut, aktivitas didominasi oleh masyarakat setempat, nelayan, dan petugas pengelola.',
            'species'          => 'Avicennia sp.,
Rhizophora sp.,
Ketapang,
Cemara laut',
        ], [
            'vegetasi' => [
                'Avicennia sp.',
                'Rhizophora sp.',
                'Ketapang',
                'Cemara laut',
            ],
            'fauna'    => [
                'Kepiting',
                'Ikan kecil',
                'Ular laut',
            ],
            'activities' => [
                'description' => 'Titik pengamatan dekat dengan pemukiman dan berada di pesisir laut, aktivitas didominasi oleh masyarakat setempat, nelayan, dan petugas pengelola.',
                'items'       => [
                    'Masyarakat setempat',
                    'Nelayan',
                    'Petugas Pengelola',
                ],
            ],
            'forest_utilization' => [
                'Pelindung pantai, pengendali rob',
                'Habitat flora dan fauna (ular laut, ikan kecil)',
                'Hutan konservasi',
            ],
            'programs'           => [
                'Penanaman mangrove oleh CSR, komunitas, dan KTH',
                'Penyuluhan dan edukasi mangrove',
            ],
            'stakeholders'       => [
                'Dinas Lingkungan Hidup',
                'Pertamina',
                'Dompet Dhuafa',
                'Transjakarta',
            ],
        ], [
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771991211/b60eedbc-ded4-43eb-b566-7b0126b28d95.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771991215/87f2fdc7-dcb8-4818-98bb-ab5f65edbcfe.png',
            'https://res.cloudinary.com/dmcvht1vr/image/upload/v1771991231/689c03c8-2b08-4db8-81cd-d99f679cee6e.png',

        ], [
            ['title' => 'Air pasang dan rob membuat bibit terendam', 'description' => 'Air pasang dan rob membuat bibit terendam', 'priority' => 'medium', 'action' => 'Penyulaman bibit'],
            ['title' => 'Sampah dari laut dan area pemukiman', 'description' => 'Sampah dari laut dan area pemukiman', 'priority' => 'medium', 'action' => 'Pengolahan sampah oleh petugas'],
            ['title' => 'Alih fungsi lahan menjadi pemukiman', 'description' => 'Alih fungsi lahan menjadi pemukiman', 'priority' => 'medium', 'action' => 'Penanaman di lokasi baru oleh pemilik bangunan'],
        ]);



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
