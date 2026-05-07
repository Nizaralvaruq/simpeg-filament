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
        Schema::table('absensi_kegiatans', function (Blueprint $table) {
            $table->enum('metode_scan', ['petugas', 'self'])->default('petugas')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_kegiatans', function (Blueprint $table) {
            $table->dropColumn('metode_scan');
        });
    }
};
