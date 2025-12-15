<?php

use App\Models\User;
use Modules\Pegawai\Models\Unit;
use Modules\Pegawai\Models\DataInduk;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== 1. CHECKING LOGGED IN KOOR ===\n";
// Assuming the user is testing with 'koor@admin.com' or the latest created Koor
$koor = User::role('koor_jenjang')->latest()->first();

if (!$koor) {
    echo "ERROR: No user with role 'koor_jenjang' found!\n";
    exit;
}

echo "Testing with Koor User: {$koor->name} ({$koor->email})\n";
if (!$koor->employee) {
    echo "ERROR: This user is NOT linked to any Data Induk (Pegawai).\n";
    echo "       The system cannot determine their Unit.\n";
} else {
    echo "Linked Employee Profile: {$koor->employee->nama} (ID: {$koor->employee->id})\n";

    $koorUnits = $koor->employee->units;
    if ($koorUnits->isEmpty()) {
        echo "ERROR: Employee Profile has NO Units assigned.\n";
    } else {
        echo "Assigned Units (This is what they CAN see):\n";
        foreach ($koorUnits as $u) {
            echo "   - [ID: {$u->id}] '{$u->name}' (Type: {$u->type})\n";
        }
    }
}

echo "\n=== 2. CHECKING AVAILABLE EMPLOYEES ===\n";
$allEmployees = DataInduk::with('units')->get();
$visibleCount = 0;

foreach ($allEmployees as $emp) {
    $empUnits = $emp->units;
    $unitString = $empUnits->map(fn($u) => "[ID:{$u->id}] {$u->name}")->implode(', ');

    if ($empUnits->isEmpty()) {
        $unitString = "NO UNITS";
    }

    // Check visibility logic manually
    $isVisible = false;
    if ($koor->employee && $koor->employee->units->isNotEmpty()) {
        $koorUnitIds = $koor->employee->units->pluck('id')->toArray();
        $empUnitIds = $empUnits->pluck('id')->toArray();
        if (array_intersect($koorUnitIds, $empUnitIds)) {
            $isVisible = true;
            $visibleCount++;
        }
    }

    echo "Emp: {$emp->nama} | Units: {$unitString} | Visible to Koor? " . ($isVisible ? "YES" : "NO") . "\n";
}

echo "\nSummary: Koor should see $visibleCount employees.\n";
