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
        Schema::table('kpi_indicators', function (Blueprint $table) {
            $table->string('level_1_label')->nullable()->after('max_score');
            $table->string('level_2_label')->nullable()->after('level_1_label');
            $table->string('level_3_label')->nullable()->after('level_2_label');
            $table->string('level_4_label')->nullable()->after('level_3_label');
            $table->string('level_5_label')->nullable()->after('level_4_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_indicators', function (Blueprint $table) {
            $table->dropColumn(['level_1_label', 'level_2_label', 'level_3_label', 'level_4_label', 'level_5_label']);
        });
    }
};
