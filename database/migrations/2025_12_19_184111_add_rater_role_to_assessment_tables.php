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
        Schema::table('rater_assignments', function (Blueprint $table) {
            $table->string('rater_role')->nullable()->after('rater_type');
        });

        Schema::table('performance_assessments', function (Blueprint $table) {
            $table->string('rater_role')->nullable()->after('rater_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rater_assignments', function (Blueprint $table) {
            $table->dropColumn('rater_role');
        });

        Schema::table('performance_assessments', function (Blueprint $table) {
            $table->dropColumn('rater_role');
        });
    }
};
