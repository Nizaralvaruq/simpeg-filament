<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSIS FILAMENT SHIELD ===\n\n";

// 1. Cek Roles
echo "[1] Checking Roles:\n";
$roles = Role::all();
if ($roles->isEmpty()) {
    echo " -> [CRITICAL] No Roles found in database!\n";
} else {
    foreach ($roles as $role) {
        echo " -> Role: " . $role->name . " (Guard: " . $role->guard_name . ")\n";
    }
}
echo "\n";

// 2. Cek Permissions Sample for 'super_admin'
echo "[2] Checking 'super_admin' Permissions:\n";
$superAdmin = Role::where('name', 'super_admin')->first();
if ($superAdmin) {
    $permissions = $superAdmin->permissions;
    echo " -> Total Permissions: " . $permissions->count() . "\n";
    if ($permissions->count() > 0) {
        echo " -> Samples: " . $permissions->take(5)->pluck('name')->implode(', ') . "\n";
    } else {
        echo " -> [WARNING] Super Admin has 0 direct permissions (This is OK if using Gate::before)\n";
    }
} else {
    echo " -> [ERROR] Role 'super_admin' not found.\n";
}
echo "\n";

// 3. Test Actual User Gate Check
echo "[3] Testing Gate for User ID 1:\n";
$user = User::find(1);
if ($user) {
    echo " -> User: " . $user->name . " (" . $user->email . ")\n";
    $roles = $user->getRoleNames();
    echo " -> Roles: " . $roles->implode(', ') . "\n";

    // Check specific permission
    $check = 'delete_any_pegawai'; // Standard Shield naming
    // Or maybe CamelCase? Shield can be configured.
    echo " -> Testing 'delete_any_pegawai': " . ($user->can('delete_any_pegawai') ? 'YES' : 'NO') . "\n";
    echo " -> Testing 'delete_any_data::induk': " . ($user->can('delete_any_data::induk') ? 'YES' : 'NO') . "\n";
} else {
    echo " -> User ID 1 not found.\n";
}

echo "\n=== END DIAGNOSIS ===\n";
