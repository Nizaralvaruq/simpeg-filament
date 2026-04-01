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
        Schema::table('cbt_students', function (Blueprint $table) {
            $table->foreignId('unit_type_id')->nullable()->constrained('unit_types')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbt_students', function (Blueprint $table) {
            $table->dropForeign(['unit_type_id']);
            $table->dropColumn('unit_type_id');
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};
