<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, number, boolean, file, json
            $table->string('group')->default('general'); // general, contact, social, seo, mail, etc
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed from frontend
            $table->timestamps();

            $table->index('group');
            $table->index(['key', 'is_public']);
        });

        // Insert default settings
        DB::table('settings')->insert([
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'SIKOMANG - Sistem Informasi dan Komunikasi Mangrove DKI Jakarta',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Nama Website',
                'description' => 'Nama website yang ditampilkan di header dan title',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Platform Monitoring dan Konservasi Ekosistem Mangrove',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Tagline Website',
                'description' => 'Tagline atau slogan website',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo.png',
                'type' => 'file',
                'group' => 'general',
                'label' => 'Logo Website',
                'description' => 'Logo yang ditampilkan di header',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'info@sikomang-dki.go.id',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Email Kontak',
                'description' => 'Email untuk kontak dan pemberitahuan',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone',
                'value' => '(021) 1234-5678',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Nomor Telepon',
                'description' => 'Nomor telepon untuk kontak',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Casablanca Kav. 1, Jakarta Selatan',
                'type' => 'textarea',
                'group' => 'contact',
                'label' => 'Alamat Kantor',
                'description' => 'Alamat lengkap kantor',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Social Media
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/dkijakarta',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Link ke halaman Facebook',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/dkijakarta',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Link ke halaman Instagram',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/dkijakarta',
                'type' => 'text',
                'group' => 'social',
                'label' => 'Twitter/X URL',
                'description' => 'Link ke halaman Twitter/X',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SEO Settings
            [
                'key' => 'seo_meta_description',
                'value' => 'Platform digital untuk monitoring, konservasi, dan edukasi ekosistem mangrove di DKI Jakarta',
                'type' => 'textarea',
                'group' => 'seo',
                'label' => 'Meta Description',
                'description' => 'Deskripsi meta untuk SEO',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'seo_meta_keywords',
                'value' => 'mangrove, jakarta, konservasi, lingkungan, DKI Jakarta',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Meta Keywords',
                'description' => 'Keywords untuk SEO (dipisah koma)',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
