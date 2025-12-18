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
        Schema::create('riwayat_golongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_induk_id')
                ->constrained('data_induks')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->foreignId('golongan_id')
                ->constrained('golongans')
                ->cascadeOnDelete(); // atau ->nullOnDelete() kalau golongan boleh dihapus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_golongans');
    }
};
