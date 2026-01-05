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
        Schema::create('performance_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_induk_id')->constrained('data_induks')->onDelete('cascade');
            $table->foreignId('penilai_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipe_penilai', ['atasan', 'rekan']);
            $table->string('periode'); // Format: YYYY-MM
            $table->integer('aspek_disiplin')->nullable();
            $table->integer('aspek_kinerja')->nullable();
            $table->integer('aspek_perilaku')->nullable();
            $table->integer('aspek_kerjasama')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_scores');
    }
};
