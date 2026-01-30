<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('ms_group_id')->nullable()->after('id');
            $table->string('username')->unique()->nullable()->after('name');

            $table->foreign('ms_group_id')->references('MsGroupId')->on('ms_group')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ms_group_id']);
            $table->dropColumn(['ms_group_id', 'username']);
        });
    }
};
