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
        Schema::table('data_induks', function (Blueprint $table) {
            $table->string('foto_profil')->nullable()->after('nama');
            $table->string('agama')->nullable()->after('jenis_kelamin');
            $table->string('golongan_darah', 5)->nullable()->after('agama');
            $table->text('alamat_domisili')->nullable()->after('alamat');
            $table->double('jarak_ke_kantor')->nullable()->after('alamat_domisili');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_induks', function (Blueprint $table) {
            $table->dropColumn(['agama', 'golongan_darah', 'alamat_domisili', 'foto_profil', 'jarak_ke_kantor']);
        });
    }
};
