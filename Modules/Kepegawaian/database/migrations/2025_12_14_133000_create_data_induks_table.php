<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_induks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('nama');
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik')->nullable();
            $table->string('nip')->unique()->nullable();
            $table->string('no_hp')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('suami_istri')->nullable();
            $table->text('alamat')->nullable();

            // mulai bertugas (pegawai mulai kerja)
            $table->date('tmt_awal')->nullable();

            $table->string('pendidikan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('status_kepegawaian')->nullable();
            $table->string('pindah_tugas')->comment('pernah | tetap')->nullable();

            // snapshot golongan aktif
            $table->foreignId('golongan_id')->nullable()->constrained('golongans')->nullOnDelete();
            $table->date('tmt_akhir')->nullable();

            $table->string('no_bpjs')->nullable();
            $table->string('no_kjp_2p')->nullable();
            $table->string('no_kjp_3p')->nullable();

            $table->enum('status', ['Aktif', 'Cuti', 'Resign'])->default('Aktif');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_induks');
    }
};
