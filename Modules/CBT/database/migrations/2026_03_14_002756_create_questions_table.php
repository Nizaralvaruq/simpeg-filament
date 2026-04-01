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
        Schema::create('cbt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('cbt_question_banks')->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice');
            $table->longText('content');
            $table->string('media')->nullable();
            $table->integer('score_weight')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_questions');
    }
};
