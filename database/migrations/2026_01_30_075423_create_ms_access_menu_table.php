<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ms_access_menu', function (Blueprint $table) {
            $table->id('MsAccessMenuId');
            $table->unsignedBigInteger('ms_group_id');
            $table->unsignedBigInteger('ms_menu_id');
            $table->string('module')->nullable();
            $table->string('menu_group')->default('sidebar');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_create')->default(false);
            $table->boolean('is_update')->default(false);
            $table->boolean('is_delete')->default(false);
            $table->boolean('is_download')->default(false);
            $table->timestamps();

            $table->foreign('ms_group_id')->references('MsGroupId')->on('ms_group')->onDelete('cascade');
            $table->foreign('ms_menu_id')->references('MsMenuId')->on('ms_menu')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_access_menu');
    }
};
