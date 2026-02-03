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
        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->string('nomor_sk')->nullable()->after('nama_jabatan');
        });

        Schema::table('riwayat_golongans', function (Blueprint $table) {
            $table->string('nomor_sk')->nullable()->after('golongan_id');
        });

        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->string('gelar')->nullable()->after('jenjang');
        });

        Schema::table('riwayat_diklats', function (Blueprint $table) {
            $table->string('nomor_sertifikat')->nullable()->after('nama_diklat');
        });

        Schema::table('riwayat_penghargaans', function (Blueprint $table) {
            $table->string('nomor_sertifikat')->nullable()->after('nama_penghargaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->dropColumn('nomor_sk');
        });

        Schema::table('riwayat_golongans', function (Blueprint $table) {
            $table->dropColumn('nomor_sk');
        });

        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->dropColumn('gelar');
        });

        Schema::table('riwayat_diklats', function (Blueprint $table) {
            $table->dropColumn('nomor_sertifikat');
        });

        Schema::table('riwayat_penghargaans', function (Blueprint $table) {
            $table->dropColumn('nomor_sertifikat');
        });
    }
};
