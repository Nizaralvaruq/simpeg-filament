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
        Schema::table('performance_scores', function (Blueprint $table) {
            // Remove old general columns if they exist (optional, or just add new ones)
            $table->dropColumn(['aspek_disiplin', 'aspek_kinerja', 'aspek_perilaku', 'aspek_kerjasama']);

            // Kualitas (1-5)
            $table->unsignedTinyInteger('kualitas_hasil')->default(3);
            $table->unsignedTinyInteger('ketelitian')->default(3);

            // Produktivitas (1-5)
            $table->unsignedTinyInteger('kuantitas_hasil')->default(3);
            $table->unsignedTinyInteger('ketepatan_waktu')->default(3);

            // Disiplin (1-5)
            $table->unsignedTinyInteger('kehadiran')->default(3);
            $table->unsignedTinyInteger('kepatuhan_aturan')->default(3);

            // Perilaku (1-5)
            $table->unsignedTinyInteger('etika_kerja')->default(3);
            $table->unsignedTinyInteger('tanggung_jawab')->default(3);

            // Kerjasama (1-5)
            $table->unsignedTinyInteger('komunikasi')->default(3);
            $table->unsignedTinyInteger('kerjasama_tim')->default(3);
        });
    }

    public function down(): void
    {
        Schema::table('performance_scores', function (Blueprint $table) {
            $table->dropColumn([
                'kualitas_hasil',
                'ketelitian',
                'kuantitas_hasil',
                'ketepatan_waktu',
                'kehadiran',
                'kepatuhan_aturan',
                'etika_kerja',
                'tanggung_jawab',
                'komunikasi',
                'kerjasama_tim'
            ]);

            $table->integer('aspek_disiplin')->nullable();
            $table->integer('aspek_kinerja')->nullable();
            $table->integer('aspek_perilaku')->nullable();
            $table->integer('aspek_kerjasama')->nullable();
        });
    }
};
