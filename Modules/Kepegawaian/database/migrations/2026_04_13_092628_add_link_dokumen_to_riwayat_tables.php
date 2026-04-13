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
        Schema::table('riwayat_golongans', function (Blueprint $table) {
            $table->string('link_dokumen')->nullable()->after('file_sk');
        });
        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->string('link_dokumen')->nullable()->after('file_sk');
        });
        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->string('link_dokumen')->nullable()->after('file_ijazah');
        });
        Schema::table('riwayat_diklats', function (Blueprint $table) {
            $table->string('link_dokumen')->nullable()->after('file_sertifikat');
        });
        Schema::table('riwayat_penghargaans', function (Blueprint $table) {
            $table->string('link_dokumen')->nullable()->after('file_sertifikat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_golongans', function (Blueprint $table) {
            $table->dropColumn('link_dokumen');
        });
        Schema::table('riwayat_jabatans', function (Blueprint $table) {
            $table->dropColumn('link_dokumen');
        });
        Schema::table('riwayat_pendidikans', function (Blueprint $table) {
            $table->dropColumn('link_dokumen');
        });
        Schema::table('riwayat_diklats', function (Blueprint $table) {
            $table->dropColumn('link_dokumen');
        });
        Schema::table('riwayat_penghargaans', function (Blueprint $table) {
            $table->dropColumn('link_dokumen');
        });
    }
};
