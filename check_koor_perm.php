<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CEK PERMISSION KOOR JENJANG ===\n\n";

$koor = User::find(17); // ID dari diagnosis tadi

if (!$koor) {
    echo "User ID 17 tidak ditemukan\n";
    exit;
}

echo "User: " . $koor->name . "\n";
echo "Email: " . $koor->email . "\n";
echo "Roles: " . $koor->getRoleNames()->implode(', ') . "\n\n";

// Check key permissions
$permissions = [
    'ViewAny:DataInduk',
    'View:DataInduk',
    'Create:DataInduk',
    'Update:DataInduk',
    'Delete:DataInduk',
];

echo "Permissions:\n";
foreach ($permissions as $perm) {
    $has = $koor->can($perm) ? '✓ YES' : '✗ NO';
    echo "  $perm: $has\n";
}

echo "\n=== END ===\n";
