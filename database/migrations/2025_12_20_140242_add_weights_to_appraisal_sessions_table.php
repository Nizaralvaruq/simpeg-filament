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
        Schema::table('appraisal_sessions', function (Blueprint $table) {
            $table->integer('superior_weight')->default(50)->nullable();
            $table->integer('peer_weight')->default(30)->nullable();
            $table->integer('self_weight')->default(20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisal_sessions', function (Blueprint $table) {
            $table->dropColumn(['superior_weight', 'peer_weight', 'self_weight']);
        });
    }
};
