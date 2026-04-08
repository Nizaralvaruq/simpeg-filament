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
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->integer('stok_sebelum_transaksi')->after('quantity')->nullable();
            $table->integer('stok_setelah_transaksi')->after('stok_sebelum_transaksi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn(['stok_sebelum_transaksi', 'stok_setelah_transaksi']);
        });
    }
};
