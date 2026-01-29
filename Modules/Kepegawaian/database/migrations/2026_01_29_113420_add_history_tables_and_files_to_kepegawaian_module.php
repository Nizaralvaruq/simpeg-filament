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
        // 1. Modify existing tables
        if (Schema::hasTable('riwayat_jabatans')) {
            Schema::table('riwayat_jabatans', function (Blueprint $table) {
                $table->string('file_sk')->nullable()->after('nama_jabatan');
            });
        }

        if (Schema::hasTable('riwayat_golongans')) {
            Schema::table('riwayat_golongans', function (Blueprint $table) {
                $table->string('file_sk')->nullable()->after('golongan_id');
            });
        }

        // 2. Create new tables
        // Riwayat Pendidikan
        if (!Schema::hasTable('riwayat_pendidikans')) {
            Schema::create('riwayat_pendidikans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('data_induk_id')->constrained('data_induks')->cascadeOnDelete();
                $table->string('jenjang'); // SD, SMP, SMA, D3, S1, S2, S3
                $table->string('institusi');
                $table->string('jurusan')->nullable();
                $table->year('tahun_lulus');
                $table->string('file_ijazah')->nullable();
                $table->timestamps();
            });
        }

        // Riwayat Diklat
        if (!Schema::hasTable('riwayat_diklats')) {
            Schema::create('riwayat_diklats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('data_induk_id')->constrained('data_induks')->cascadeOnDelete();
                $table->string('nama_diklat');
                $table->string('penyelenggara');
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->unsignedInteger('durasi_jam')->nullable();
                $table->string('file_sertifikat')->nullable();
                $table->timestamps();
            });
        }

        // Riwayat Penghargaan
        if (!Schema::hasTable('riwayat_penghargaans')) {
            Schema::create('riwayat_penghargaans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('data_induk_id')->constrained('data_induks')->cascadeOnDelete();
                $table->string('nama_penghargaan');
                $table->string('pemberi');
                $table->date('tanggal');
                $table->string('file_sertifikat')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_penghargaans');
        Schema::dropIfExists('riwayat_diklats');
        Schema::dropIfExists('riwayat_pendidikans');

        if (Schema::hasTable('riwayat_golongans')) {
            Schema::table('riwayat_golongans', function (Blueprint $table) {
                $table->dropColumn('file_sk');
            });
        }

        if (Schema::hasTable('riwayat_jabatans')) {
            Schema::table('riwayat_jabatans', function (Blueprint $table) {
                $table->dropColumn('file_sk');
            });
        }
    }
};
