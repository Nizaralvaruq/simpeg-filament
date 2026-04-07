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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_peminjaman')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->date('tanggal_pinjam');
            $table->date('rencana_kembali');
            $table->datetime('tanggal_kembali')->nullable(); // Realisasi kembali
            $table->text('keperluan')->nullable();
            
            // Statuses: draft, diajukan, dipinjam, menunggu_pengecekan, dikembalikan_baik, dikembalikan_rusak, ditolak
            $table->enum('status', [
                'draft', 
                'diajukan', 
                'dipinjam', 
                'menunggu_pengecekan', 
                'dikembalikan_baik', 
                'dikembalikan_rusak', 
                'ditolak'
            ])->default('draft');
            
            $table->text('catatan_pengembalian')->nullable(); // Dari peminjam
            $table->text('alasan_penolakan')->nullable();
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
