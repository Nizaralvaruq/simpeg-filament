<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add unit_type_id column
        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('unit_type_id')->nullable()->after('name')->constrained('unit_types')->nullOnDelete();
        });

        // 2. Initial types from the hardcoded list
        $types = [
            'LPI',
            'TK TAKFIZ',
            'RUBAT',
            'PAUD',
            'TK',
            'TAKFIZ TK',
            'SD',
            'SMP',
            'TAKFIZ SMP',
            'SMA',
            'TAKFIZ SMA',
            'SMK',
            'TAKFIZ SMK',
            'TK PG',
            'MI',
            'Mts PG',
            'GIZI LPI',
            'AEC',
            'PENGEMBANGAN'
        ];

        foreach ($types as $type) {
            DB::table('unit_types')->updateOrInsert(['name' => $type], ['created_at' => now(), 'updated_at' => now()]);
        }

        // 3. Migrate existing data if any
        $units = DB::table('units')->whereNotNull('type')->get();
        foreach ($units as $unit) {
            $typeId = DB::table('unit_types')->where('name', $unit->type)->value('id');
            if (!$typeId) {
                $typeId = DB::table('unit_types')->insertGetId([
                    'name' => $unit->type,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            DB::table('units')->where('id', $unit->id)->update(['unit_type_id' => $typeId]);
        }

        // 4. Drop the old type column
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->string('type')->nullable()->after('name');
        });

        $units = DB::table('units')->whereNotNull('unit_type_id')->get();
        foreach ($units as $unit) {
            $typeName = DB::table('unit_types')->where('id', $unit->unit_type_id)->value('name');
            DB::table('units')->where('id', $unit->id)->update(['type' => $typeName]);
        }

        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['unit_type_id']);
            $table->dropColumn('unit_type_id');
        });
    }
};
