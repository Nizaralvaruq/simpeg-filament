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
        Schema::table('riwayat_keluargas', function (Blueprint $table) {
            $table->string('no_hp')->nullable()->after('pekerjaan');
            $table->string('pendidikan')->nullable()->after('no_hp');
            $table->dropColumn('is_bpjs');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_keluargas', function (Blueprint $table) {
            $table->boolean('is_bpjs')->default(false)->after('pekerjaan');
            $table->dropColumn(['no_hp', 'pendidikan']);
        });
    }
};
