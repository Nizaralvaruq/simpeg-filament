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
        Schema::create('resigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_induk_id')->constrained('data_induks')->cascadeOnDelete();
            $table->date('tanggal_resign');
            $table->text('alasan');
            $table->string('status')->default('diajukan'); // diajukan, disetujui, ditolak
            $table->text('keterangan_tindak_lanjut')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resigns');
    }
};
