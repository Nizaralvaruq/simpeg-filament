<?php

use App\Models\User;
use Modules\Kepegawaian\Models\DataInduk;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DEBUG INFO ---\n";
echo "Total Users: " . User::count() . "\n";
echo "Total DataInduk: " . DataInduk::count() . "\n";

$users = User::with('roles')->take(10)->get();
foreach ($users as $user) {
    echo "User [{$user->id}] {$user->name} ({$user->email}) Roles: " . $user->getRoleNames()->implode(', ') . "\n";
    $employee = DataInduk::where('user_id', $user->id)->first();
    if ($employee) {
        echo "   -> LINKED to: {$employee->nama} [ID: {$employee->id}]\n";
    } else {
        echo "   -> NOT LINKED\n";
    }
}
