<?php

use App\Models\User;
use Modules\Pegawai\Models\Unit;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== UNIT LIST (LPI VARIATIONS) ===\n";
$units = Unit::where('name', 'LIKE', '%LPI%')->get();
foreach ($units as $u) {
    $count = $u->employees()->count();
    echo "ID: {$u->id} | Name: {$u->name} | Type: {$u->type} | Employee Count: {$count}\n";
}

echo "\n=== USER -> UNIT ASSIGNMENTS ===\n";
$users = User::all();
foreach ($users as $user) {
    $roles = $user->getRoleNames()->implode(',');
    if ($roles == 'super_admin') continue; // Skip super admin if any

    echo "User: {$user->name} ({$user->email}) | Role: [$roles]\n";
    if ($user->employee) {
        $userUnits = $user->employee->units;
        if ($userUnits->isEmpty()) {
            echo "  -> NO UNITS ASSIGNED\n";
        } else {
            foreach ($userUnits as $uu) {
                echo "  -> Assigned Unit: ID {$uu->id} ({$uu->name})\n";
            }
        }
    } else {
        echo "  -> NO EMPLOYEE PROFILE\n";
    }
    echo "---------------------------------------------------\n";
}
