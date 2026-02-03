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
        // Cek apakah kolom sudah ada atau belum
        if (!Schema::hasColumn('mangrove_locations', 'health_percentage')) {
            Schema::table('mangrove_locations', function (Blueprint $table) {
                $table->decimal('health_percentage', 5, 2)->nullable()->after('year_established');
            });
        }

        if (!Schema::hasColumn('mangrove_locations', 'health_score')) {
            Schema::table('mangrove_locations', function (Blueprint $table) {
                $table->string('health_score')->nullable()->after('health_percentage');
            });
        }

        // Update kolom area menjadi nullable jika belum
        Schema::table('mangrove_locations', function (Blueprint $table) {
            $table->decimal('area', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mangrove_locations', function (Blueprint $table) {
            if (Schema::hasColumn('mangrove_locations', 'health_percentage')) {
                $table->dropColumn('health_percentage');
            }
            if (Schema::hasColumn('mangrove_locations', 'health_score')) {
                $table->dropColumn('health_score');
            }
        });
    }
};
