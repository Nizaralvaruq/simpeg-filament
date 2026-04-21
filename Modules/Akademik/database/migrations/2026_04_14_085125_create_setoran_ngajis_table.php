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
        Schema::create('setoran_ngajis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_setoran');
            $table->string('jenis_setoran');
            $table->string('nama_materi');
            $table->string('ayat_halaman')->nullable();
            $table->enum('predikat_nilai', ['A', 'B', 'C']);
            $table->text('catatan_guru')->nullable();
            $table->boolean('status_notifikasi')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_ngajis');
    }
};
