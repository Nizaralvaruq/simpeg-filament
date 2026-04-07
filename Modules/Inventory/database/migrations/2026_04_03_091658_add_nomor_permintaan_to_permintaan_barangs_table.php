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
        Schema::table('permintaan_barangs', function (Blueprint $table) {
            $table->string('nomor_permintaan')->nullable()->unique()->after('id');
            $table->text('alasan_penolakan')->nullable()->after('catatan');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('alasan_penolakan');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_barangs', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['nomor_permintaan', 'alasan_penolakan', 'approved_by', 'approved_at']);
        });
    }
};
