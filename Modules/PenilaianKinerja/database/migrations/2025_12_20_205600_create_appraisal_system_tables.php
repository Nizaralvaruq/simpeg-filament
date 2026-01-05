<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sessions
        Schema::create('appraisal_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->enum('status', ['Draft', 'Published', 'Closed'])->default('Draft');
            $table->timestamps();
        });

        // 2. Categories
        Schema::create('appraisal_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('weight')->default(0);
            $table->timestamps();
        });

        // 3. Indicators
        Schema::create('appraisal_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('appraisal_categories')->onDelete('cascade');
            $table->text('indicator_text');
            $table->integer('weight')->default(1);
            $table->timestamps();
        });

        // 4. Assignments
        Schema::create('appraisal_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('appraisal_sessions')->onDelete('cascade');
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ratee_id')->constrained('data_induks')->onDelete('cascade');
            $table->enum('relation_type', ['self', 'peer', 'superior']);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });

        // 5. Results
        Schema::create('appraisal_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('appraisal_assignments')->onDelete('cascade');
            $table->foreignId('indicator_id')->constrained('appraisal_indicators')->onDelete('cascade');
            $table->unsignedTinyInteger('score'); // 1-5
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appraisal_results');
        Schema::dropIfExists('appraisal_assignments');
        Schema::dropIfExists('appraisal_indicators');
        Schema::dropIfExists('appraisal_categories');
        Schema::dropIfExists('appraisal_sessions');
    }
};
