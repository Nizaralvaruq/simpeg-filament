<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cbt_subjects', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->unique()->after('id');
            $table->foreignId('unit_id')->nullable()->after('unit_type_id')->constrained('units')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbt_subjects', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['code', 'unit_id']);
        });
    }
};
