<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Presensi\Models\Absensi;

$all = Absensi::all();
echo "TOTAL LATE SUM: " . $all->sum('late_minutes') . " Menit\n";
foreach ($all as $a) {
    if ($a->late_minutes > 0) {
        echo "ID " . $a->id . ": " . $a->late_minutes . "\n";
    }
}
