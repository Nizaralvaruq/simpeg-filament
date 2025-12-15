<?php

use Modules\Pegawai\Models\Unit;
use Modules\Pegawai\Models\DataInduk;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Find the LPI Unit
$lpi = Unit::where('name', 'LIKE', '%LPI%')->get();
echo "Found Units matching 'LPI':\n";
foreach ($lpi as $u) {
    echo "- ID: {$u->id}, Name: {$u->name}, Type: {$u->type}\n";

    // 2. Count Employees in this unit
    $count = $u->employees()->count();
    echo "  > Total Employees in this unit: $count\n";

    // List some names
    foreach ($u->employees()->take(5)->get() as $emp) {
        echo "    - {$emp->nama} (User ID: {$emp->user_id})\n";
    }
}

// 3. Check the Koor User again
$koorEmail = 'koor@admin.com';
$koorUser = \App\Models\User::where('email', $koorEmail)->first();
if ($koorUser) {
    echo "\nCheck Koor User ($koorEmail):\n";
    if ($koorUser->employee) {
        echo "  - Employee Profile Found: {$koorUser->employee->nama}\n";
        echo "  - Assigned Units: " . $koorUser->employee->units->pluck('name', 'id')->toJson() . "\n";
    } else {
        echo "  - [ERROR] No Employee Profile linked to this user!\n";
    }
}
