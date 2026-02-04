<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mangrove_location_id')->constrained()->onDelete('cascade');
            $table->json('vegetasi')->nullable();
            $table->json('fauna')->nullable();
            $table->json('activities')->nullable();
            $table->json('forest_utilization')->nullable();
            $table->json('programs')->nullable();
            $table->json('stakeholders')->nullable();
            $table->timestamps();
        });

        Schema::create('location_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mangrove_location_id')->constrained()->onDelete('cascade');
            $table->string('image_url');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('location_damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mangrove_location_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'resolved'])->default('pending');
            $table->timestamps();
        });

        Schema::create('location_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_damage_id')->constrained()->onDelete('cascade');
            $table->text('action_description');
            $table->date('action_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_actions');
        Schema::dropIfExists('location_damages');
        Schema::dropIfExists('location_images');
        Schema::dropIfExists('location_details');
    }
};
