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
        // Menyimpan peserta mana saja yang berhak ikut ujian
        Schema::create('cbt_exam_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('cbt_exams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_exam_participants');
    }
};
