<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mangrove_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('area', 8, 2)->nullable(); // in hectares
            $table->enum('density', ['jarang', 'sedang', 'lebat']);
            $table->enum('type', ['pengkayaan', 'rehabilitasi', 'dilindungi', 'restorasi']);
            $table->integer('year_established')->nullable();
            $table->decimal('health_percentage', 5, 2)->nullable();
            $table->string('health_score')->nullable(); // NAK score
            $table->string('manager')->nullable();
            $table->string('region')->nullable(); // kecamatan
            $table->text('location_address')->nullable();
            $table->text('description')->nullable();
            $table->text('species')->nullable();
            $table->text('carbon_data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mangrove_locations');
    }
};
