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
        // Menyimpan setiap jawaban user per soal
        Schema::create('cbt_exam_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('cbt_exam_sessions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('cbt_questions')->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained('cbt_question_options')->nullOnDelete();
            $table->text('essay_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_exam_responses');
    }
};
