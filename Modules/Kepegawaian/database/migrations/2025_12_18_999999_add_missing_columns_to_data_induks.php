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
            if (!Schema::hasColumn('data_induks', 'nik')) {
                $table->string('nik')->nullable();
            }
            if (!Schema::hasColumn('data_induks', 'no_bpjs')) {
                $table->string('no_bpjs')->nullable();
            }
            if (!Schema::hasColumn('data_induks', 'no_kjp_2p')) {
                $table->string('no_kjp_2p')->nullable();
            }
            if (!Schema::hasColumn('data_induks', 'no_kjp_3p')) {
                $table->string('no_kjp_3p')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_induks', function (Blueprint $table) {
            $table->dropColumn(['nik', 'no_bpjs', 'no_kjp_2p', 'no_kjp_3p']);
        });
    }
};
