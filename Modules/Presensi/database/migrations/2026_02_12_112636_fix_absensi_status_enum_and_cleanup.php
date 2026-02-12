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
        Schema::table('absensis', function (Blueprint $table) {
            // Restore 'dinas_luar' and include 'cuti' properly
            // Using DB::statement for ENUM modification to ensure compatibility across MySQL/MariaDB
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha', 'dinas_luar', 'cuti') DEFAULT 'hadir'");

            // Remove legacy manual field
            if (Schema::hasColumn('absensis', 'uraian_harian')) {
                $table->dropColumn('uraian_harian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha', 'cuti') DEFAULT 'hadir'");

            if (!Schema::hasColumn('absensis', 'uraian_harian')) {
                $table->text('uraian_harian')->nullable()->after('keterangan');
            }
        });
    }
};
