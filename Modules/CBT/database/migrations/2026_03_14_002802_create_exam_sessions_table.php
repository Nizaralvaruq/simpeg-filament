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
        // Menyimpan sesi pengerjaan ujian per user
        Schema::create('cbt_exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('cbt_exams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->boolean('force_closed')->default(false);
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_exam_sessions');
    }
};
