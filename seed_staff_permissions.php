<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "Seeding/Restoring Staff Permissions...\n";

$role = Role::where('name', 'staff')->firstOrCreate(['name' => 'staff']);

$perms = [
    // Leave Requests
    'ViewAny:LeaveRequest',
    'View:LeaveRequest',
    'Create:LeaveRequest',
    // Resign
    'ViewAny:Resign',
    'View:Resign',
    'Create:Resign',
];

// Ensure permissions exist
foreach ($perms as $permName) {
    Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
}

// Sync permissions (careful not to wipe existing ones if possible, but user said "optimize")
// Actually, let's just GIVE them these permissions.
$role->givePermissionTo($perms);

echo "Permissions assigned to 'staff' role:\n";
foreach ($role->permissions->pluck('name') as $p) {
    echo " - $p\n";
}
