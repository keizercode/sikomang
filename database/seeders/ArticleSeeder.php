// database/seeders/ArticleSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('username', 'admin')->first();

        $articles = [
            [
                'title' => 'Manfaat Hutan Mangrove untuk Lingkungan',
                'content' => '<p>Hutan mangrove memiliki peran penting dalam menjaga keseimbangan ekosistem pesisir...</p>',
                'excerpt' => 'Pelajari berbagai manfaat hutan mangrove bagi lingkungan dan kehidupan',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Program Konservasi Mangrove DKI Jakarta 2025',
                'content' => '<p>Program konservasi mangrove di DKI Jakarta tahun 2025 mencakup berbagai kegiatan...</p>',
                'excerpt' => 'Rencana program konservasi dan rehabilitasi mangrove di Jakarta',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now(),
            ],
            [
                'title' => 'Draft: Panduan Penanaman Mangrove',
                'content' => '<p>Panduan lengkap untuk menanam mangrove dengan benar...</p>',
                'excerpt' => 'Teknik dan tips penanaman mangrove yang efektif',
                'status' => 'draft',
                'is_featured' => false,
            ],
        ];

        foreach ($articles as $article) {
            Article::create(array_merge([
                'user_id' => $admin->id,
            ], $article));
        }
    }
}
