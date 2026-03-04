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
        Schema::table('data_induks', function (Blueprint $table) {
            DB::statement("ALTER TABLE data_induks MODIFY COLUMN status ENUM('Aktif', 'Cuti', 'Resign', 'Izin', 'Sakit', 'Pensiun') DEFAULT 'Aktif'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_induks', function (Blueprint $table) {
            DB::statement("ALTER TABLE data_induks MODIFY COLUMN status ENUM('Aktif', 'Cuti', 'Resign', 'Izin', 'Sakit') DEFAULT 'Aktif'");
        });
    }
};
