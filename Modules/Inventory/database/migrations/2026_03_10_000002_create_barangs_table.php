2<?php

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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_barangs')->onDelete('cascade');
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('jenis')->default('Aset'); // Aset atau BHP
            $table->text('spesifikasi')->nullable();
            $table->string('foto')->nullable();
            $table->string('lokasi_ruangan')->nullable();
            $table->integer('stok_saat_ini')->default(0);
            $table->integer('minimum_stok')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
