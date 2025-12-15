<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_induks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nip')->unique()->nullable();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('status_kepegawaian')->nullable(); // Tetap, Kontrak, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_induks');
    }
};
