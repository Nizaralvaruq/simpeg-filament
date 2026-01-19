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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('SIMPEG');
            $table->time('office_start_time')->default('07:00:00');
            $table->time('office_end_time')->default('16:00:00');
            $table->decimal('office_latitude', 10, 8)->nullable();
            $table->decimal('office_longitude', 11, 8)->nullable();
            $table->integer('office_radius')->default(100)->comment('In meters');
            $table->integer('late_tolerance')->default(15)->comment('In minutes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
