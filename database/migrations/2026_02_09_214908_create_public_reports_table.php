<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('public_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mangrove_location_id')->constrained()->onDelete('cascade');

            // Informasi Laporan
            $table->string('report_number')->unique(); // Auto-generated: REP-YYYYMMDD-XXXXX
            $table->text('description');
            $table->enum('report_type', [
                'kerusakan',
                'pencemaran',
                'penebangan_liar',
                'kondisi_baik',
                'lainnya'
            ])->default('kerusakan');
            $table->enum('urgency_level', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');

            // Informasi Pelapor
            $table->string('reporter_name');
            $table->string('reporter_email');
            $table->string('reporter_phone');
            $table->string('reporter_address')->nullable();
            $table->string('reporter_organization')->nullable(); // Organisasi/instansi jika ada

            // Media
            $table->json('photo_urls')->nullable(); // Array of uploaded photo URLs

            // Status & Follow-up
            $table->enum('status', [
                'pending',      // Menunggu verifikasi
                'verified',     // Terverifikasi
                'in_review',    // Sedang ditinjau
                'in_progress',  // Sedang ditangani
                'resolved',     // Selesai ditangani
                'rejected'      // Ditolak/tidak valid
            ])->default('pending');

            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            // Metadata
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('report_number');
            $table->index('status');
            $table->index('report_type');
            $table->index('created_at');
            $table->index(['mangrove_location_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_reports');
    }
};
