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
        Schema::create('riwayat_keluargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_induk_id')->constrained('data_induks')->cascadeOnDelete();
            $table->string('nama');
            $table->enum('hubungan', ['Suami', 'Istri', 'Anak']);
            $table->string('pekerjaan')->nullable();
            $table->string('nik', 16)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->boolean('is_bpjs')->default(false);
            $table->string('file_kk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_keluargas');
    }
};
