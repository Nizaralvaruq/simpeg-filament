<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Spatie\Permission\Models\Role;

$roles = ['admin_hr', 'kepala_sekolah', 'koor_jenjang', 'staff'];

echo "Checking/Creating Roles...\n";

foreach ($roles as $roleName) {
    if (Role::where('name', $roleName)->exists()) {
        echo " [OK] Role '$roleName' exists.\n";
    } else {
        Role::create(['name' => $roleName, 'guard_name' => 'web']);
        echo " [CREATED] Role '$roleName'.\n";
    }
}
