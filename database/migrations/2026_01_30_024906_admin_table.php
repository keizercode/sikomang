<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table: ms_group (Roles)
        Schema::create('ms_group', function (Blueprint $table) {
            $table->id('MsGroupId');
            $table->string('name', 100);
            $table->string('alias', 50)->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Table: ms_menu
        Schema::create('ms_menu', function (Blueprint $table) {
            $table->id('MsMenuId');
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('title', 100);
            $table->string('url', 255)->nullable();
            $table->string('module', 100)->nullable();
            $table->string('menu_type', 20)->default('sidebar'); // sidebar, header
            $table->string('menu_icons', 50)->nullable();
            $table->integer('ordering')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('parent_id');
            $table->index('menu_type');
        });

        // Table: ms_access_menu
        Schema::create('ms_access_menu', function (Blueprint $table) {
            $table->id('MsAccessMenuId');
            $table->unsignedBigInteger('ms_group_id');
            $table->unsignedBigInteger('ms_menu_id');
            $table->string('module', 100);
            $table->string('menu_group', 50)->default('sidebar');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_create')->default(false);
            $table->boolean('is_update')->default(false);
            $table->boolean('is_delete')->default(false);
            $table->boolean('is_download')->default(false);
            $table->timestamps();

            $table->foreign('ms_group_id')->references('MsGroupId')->on('ms_group')->onDelete('cascade');
            $table->foreign('ms_menu_id')->references('MsMenuId')->on('ms_menu')->onDelete('cascade');
            $table->index(['ms_group_id', 'ms_menu_id']);
        });

        // Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('id');
            $table->unsignedBigInteger('ms_group_id')->nullable()->after('password');

            $table->foreign('ms_group_id')->references('MsGroupId')->on('ms_group')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ms_group_id']);
            $table->dropColumn(['username', 'ms_group_id']);
        });

        Schema::dropIfExists('ms_access_menu');
        Schema::dropIfExists('ms_menu');
        Schema::dropIfExists('ms_group');
    }
};
