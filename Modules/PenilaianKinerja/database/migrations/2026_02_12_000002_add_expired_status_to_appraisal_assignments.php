<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use a raw query to update the enum
        DB::statement("ALTER TABLE appraisal_assignments MODIFY COLUMN status ENUM('pending', 'completed', 'expired') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back and handle cases where data might be 'expired' by resetting to 'pending'
        DB::table('appraisal_assignments')->where('status', 'expired')->update(['status' => 'pending']);
        DB::statement("ALTER TABLE appraisal_assignments MODIFY COLUMN status ENUM('pending', 'completed') DEFAULT 'pending'");
    }
};
