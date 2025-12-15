<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::where('name', 'staff')->first();

if (!$role) {
    echo "Role 'staff' not found!\n";
    exit;
}

echo "Permissions for role 'staff':\n";
$permissions = $role->permissions->pluck('name');

if ($permissions->isEmpty()) {
    echo "  (No permissions assigned)\n";
} else {
    foreach ($permissions as $perm) {
        echo "  - $perm\n";
    }
}

echo "\nChecking specific permissions for LeaveRequest:\n";
$needed = [
    'ViewAny:LeaveRequest',
    'View:LeaveRequest',
    'Create:LeaveRequest',
    'Update:LeaveRequest',
    'Delete:LeaveRequest',
];

foreach ($needed as $permName) {
    if ($role->hasPermissionTo($permName)) {
        echo "  [x] $permName\n";
    } else {
        echo "  [ ] $permName\n";
    }
}
