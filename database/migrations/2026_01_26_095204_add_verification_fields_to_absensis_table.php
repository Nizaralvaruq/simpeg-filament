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
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha', 'dinas_luar'])->default('hadir')->change();
            $table->string('foto_verifikasi')->nullable()->after('longitude');
            $table->text('alamat_lokasi')->nullable()->after('foto_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir')->change();
            $table->dropColumn(['foto_verifikasi', 'alamat_lokasi']);
        });
    }
};
