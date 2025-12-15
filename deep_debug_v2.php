<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$output = "";

$user = \App\Models\User::find(2);
$output .= "Debugging User ID 2 ({$user->name})\n";

// 1. Verify Manual Gate::before
$isSuperAdmin = $user->hasRole('super_admin');
$output .= "Is Super Admin Role: " . ($isSuperAdmin ? 'YES' : 'NO') . "\n";

$randomCheck = \Illuminate\Support\Facades\Gate::forUser($user)->allows('random_non_existent_permission');
$output .= "Gate::before active (allows random): " . ($randomCheck ? 'YES' : 'NO') . "\n";

// 2. Test Authorization
$output .= "Can 'viewAny' User model: " . ($user->can('viewAny', \App\Models\User::class) ? 'YES' : 'NO') . "\n";

// 3. Test Shield Authorization
$output .= "Can 'viewAny' Role model: " . ($user->can('viewAny', \Spatie\Permission\Models\Role::class) ? 'YES' : 'NO') . "\n";

file_put_contents('debug_output.txt', $output);
echo "Debug finished. Check debug_output.txt\n";
