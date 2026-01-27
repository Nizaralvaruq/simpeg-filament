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
        Schema::table('absensis', function (Blueprint $table) {
            // Index for fast badge counting (Alpha Today)
            $table->index(['tanggal', 'status'], 'idx_absensi_tanggal_status');
            // Index for general date filtering
            $table->index('tanggal', 'idx_absensi_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('idx_absensi_tanggal_status');
            $table->dropIndex('idx_absensi_tanggal');
        });
    }
};
