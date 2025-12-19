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
        // 1. Kategori KPI (e.g., Kompetensi, Kehadiran, Loyalitas)
        Schema::create('kpi_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('weight', 5, 2)->default(0); // Bobot kategori dalam %
            $table->timestamps();
        });

        // 2. Indikator KPI (Sub dari kategori)
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_category_id')->constrained('kpi_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_score')->default(5);
            $table->timestamps();
        });

        // 3. Periode Penilaian (e.g., Semester Ganjil 2024)
        Schema::create('kpi_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Header Penilaian
        Schema::create('performance_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_induk_id')->constrained('data_induks')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('kpi_periods')->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_score', 8, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'finalized'])->default('draft');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // 5. Detail Penilaian (Skor per indikator)
        Schema::create('assessment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_assessment_id')->constrained('performance_assessments')->onDelete('cascade');
            $table->foreignId('kpi_indicator_id')->constrained('kpi_indicators')->onDelete('cascade');
            $table->integer('score');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_details');
        Schema::dropIfExists('performance_assessments');
        Schema::dropIfExists('kpi_periods');
        Schema::dropIfExists('kpi_indicators');
        Schema::dropIfExists('kpi_categories');
    }
};
