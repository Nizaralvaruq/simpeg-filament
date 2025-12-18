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
            $columns = [
                'no_hp',
                'tempat_lahir',
                'tanggal_lahir',
                'status_perkawinan',
                'suami_istri',
                'alamat',
                'pendidikan',
                'instansi',
                'tmt_awal',
                'tmt_akhir',
                'pindah_tugas',
                'status_kepegawaian'
            ];

            foreach ($columns as $column) {
                if (!Schema::hasColumn('data_induks', $column)) {
                    if (in_array($column, ['tanggal_lahir', 'tmt_awal', 'tmt_akhir'])) {
                        $table->date($column)->nullable();
                    } elseif ($column === 'alamat') {
                        $table->text($column)->nullable();
                    } else {
                        $table->string($column)->nullable();
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_induks', function (Blueprint $table) {
            // We generally don't drop these in down() if they might have existed before, 
            // but for this specific "sync" migration, it's safer to leave them or drop them only if we are strict.
            // Leaving empty to avoid accidental data loss on rollback of this specific patch.
        });
    }
};
