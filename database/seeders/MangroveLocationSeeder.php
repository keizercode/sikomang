<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MangroveLocation;
use App\Models\LocationDetail;
use App\Models\LocationImage;
use App\Models\LocationDamage;
use App\Models\LocationAction;

class MangroveLocationSeeder extends Seeder
{
    public function run(): void
    {
        // Location 1: Rawa Hutan Lindung
        $location1 = MangroveLocation::create([
            'name' => 'Rawa Hutan Lindung',
            'slug' => 'rawa-hutan-lindung',
            'latitude' => -6.1023,
            'longitude' => 106.7655,
            'area' => 44.7,
            'density' => 'jarang',
            'type' => 'pengkayaan',
            'year_established' => 2025,
            'health_percentage' => 98,
            'health_score' => 'NAK: 7.2',
            'manager' => 'DPHK',
            'region' => 'Kecamatan Penjaringan, Jakarta Utara',
            'location_address' => 'Rawa Hutan Lindung, Penjaringan, Jakarta Utara',
            'description' => 'Kawasan hutan mangrove lindung dengan tingkat kesehatan yang baik.',
            'species' => 'Avicennia alba, Avicennia marina, Excoecaria agallocha',
            'carbon_data' => 'Data tidak tersedia',
            'is_active' => true,
        ]);

        LocationDetail::create([
            'mangrove_location_id' => $location1->id,
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
        ]);

        foreach (
            [
                'https://res.cloudinary.com/dgctlfa2t/image/upload/v1755389322/rhl2_htfyvf.jpg',
                'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758002227/2-tanah_timbul-1_couywb.jpg',
                'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758003912/3-pos_2_hutan_lindung-3_hk6fqt.jpg'
            ] as $index => $imageUrl
        ) {
            LocationImage::create([
                'mangrove_location_id' => $location1->id,
                'image_url' => $imageUrl,
                'order' => $index
            ]);
        }

        $damage1 = LocationDamage::create([
            'mangrove_location_id' => $location1->id,
            'title' => 'Dahan patah dan pohon tumbang',
            'description' => 'Dahan patah dan pohon tumbang akibat angin kencang',
            'priority' => 'high',
            'status' => 'pending'
        ]);

        LocationAction::create([
            'location_damage_id' => $damage1->id,
            'action_description' => 'Pembersihan area terdampak yang menghalangi akses',
            'action_date' => now()
        ]);

        $damage2 = LocationDamage::create([
            'mangrove_location_id' => $location1->id,
            'title' => 'Sampah dari laut',
            'description' => 'Sampah dari laut',
            'priority' => 'medium',
            'status' => 'pending'
        ]);

        LocationAction::create([
            'location_damage_id' => $damage2->id,
            'action_description' => 'Pagar jaring dekat pesisir',
            'action_date' => now()
        ]);

        // Location 2: Pos 5 Hutan Lindung
        $location2 = MangroveLocation::create([
            'name' => 'Pos 5 Hutan Lindung',
            'slug' => 'pos-5-hutan-lindung',
            'latitude' => -6.0895,
            'longitude' => 106.7820,
            'area' => 4.7,
            'density' => 'jarang',
            'type' => 'dilindungi',
            'year_established' => 2025,
            'health_percentage' => 92,
            'health_score' => 'NAK: 6.8',
            'manager' => 'DPHK',
            'region' => 'Kecamatan Penjaringan, Jakarta Utara',
            'location_address' => 'Pos 5 Hutan Lindung, Penjaringan, Jakarta Utara',
            'description' => 'Pos pemantauan 5 di kawasan hutan lindung dengan status dilindungi.',
            'species' => 'Sonneratia caseolaris, Avicennia alba',
            'carbon_data' => 'Data tidak tersedia',
            'is_active' => true,
        ]);

        LocationDetail::create([
            'mangrove_location_id' => $location2->id,
        ]);

        LocationImage::create([
            'mangrove_location_id' => $location2->id,
            'image_url' => 'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758005191/4-pos5_hl-3_gezsmd.jpg',
            'order' => 0
        ]);

        $damage3 = LocationDamage::create([
            'mangrove_location_id' => $location2->id,
            'title' => 'Erosi tanah',
            'description' => 'Erosi tanah di area pesisir',
            'priority' => 'medium',
            'status' => 'in_progress'
        ]);

        // Location 3: Rusun TNI AL
        $location3 = MangroveLocation::create([
            'name' => 'Rusun TNI AL',
            'slug' => 'rusun-tni-al',
            'latitude' => -6.0912,
            'longitude' => 106.9105,
            'area' => 6.0,
            'density' => 'jarang',
            'type' => 'pengkayaan',
            'year_established' => 2025,
            'health_percentage' => 80,
            'health_score' => 'NAK: 6.2',
            'manager' => 'DPHK',
            'region' => 'Kecamatan Cilincing, Jakarta Utara',
            'location_address' => 'Rusun TNI AL, Cilincing, Jakarta Utara',
            'description' => 'Kawasan mangrove di sekitar Rusun TNI AL dengan program pengkayaan.',
            'species' => 'Avicennia alba, Rhizophora mucronata',
            'carbon_data' => 'Data tidak tersedia',
            'is_active' => true,
        ]);

        LocationDetail::create([
            'mangrove_location_id' => $location3->id,
        ]);

        LocationImage::create([
            'mangrove_location_id' => $location3->id,
            'image_url' => 'https://res.cloudinary.com/dgctlfa2t/image/upload/v1758068635/7-rusun_tni_al-1_gx1iqa.jpg',
            'order' => 0
        ]);
    }
}
