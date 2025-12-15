<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ASSIGN PERMISSIONS KE KOOR JENJANG ===\n\n";

$role = Role::where('name', 'koor_jenjang')->first();

if (!$role) {
    echo "[ERROR] Role 'koor_jenjang' tidak ditemukan\n";
    exit;
}

// Permissions needed for Koor Jenjang
$permissionsNeeded = [
    'ViewAny:DataInduk',
    'View:DataInduk',
    'ViewAny:LeaveRequest',
    'View:LeaveRequest',
    'Update:LeaveRequest', // For approval
    'ViewAny:Resign',
    'View:Resign',
    'Update:Resign', // For approval
];

$assigned = [];
$alreadyHas = [];

foreach ($permissionsNeeded as $permName) {
    $permission = Permission::where('name', $permName)->first();

    if (!$permission) {
        echo "[SKIP] Permission '$permName' belum ada di database\n";
        continue;
    }

    if ($role->hasPermissionTo($permName)) {
        $alreadyHas[] = $permName;
    } else {
        $role->givePermissionTo($permission);
        $assigned[] = $permName;
    }
}

echo "Permissions yang DI-ASSIGN:\n";
foreach ($assigned as $perm) {
    echo "  ✓ $perm\n";
}

if (!empty($alreadyHas)) {
    echo "\nPermissions yang SUDAH DIMILIKI:\n";
    foreach ($alreadyHas as $perm) {
        echo "  → $perm\n";
    }
}

echo "\n=== SELESAI ===\n";
echo "Total di-assign: " . count($assigned) . "\n";
echo "Total sudah ada: " . count($alreadyHas) . "\n";
