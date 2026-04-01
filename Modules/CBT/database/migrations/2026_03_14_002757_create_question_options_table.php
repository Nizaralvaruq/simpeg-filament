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
        Schema::create('cbt_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('cbt_questions')->cascadeOnDelete();
            $table->string('label')->nullable(); // A, B, C, D
            $table->text('content');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_question_options');
    }
};
