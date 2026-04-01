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
        Schema::create('cbt_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('question_bank_id')->constrained('cbt_question_banks')->cascadeOnDelete();
            $table->foreignId('unit_type_id')->nullable()->constrained('unit_types')->nullOnDelete(); // Jenjang
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete(); // Unit spesifik (opsional)
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration_minutes');
            $table->string('token')->nullable();
            $table->boolean('show_result')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_exams');
    }
};
