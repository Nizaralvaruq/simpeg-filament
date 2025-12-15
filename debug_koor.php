<?php

use App\Models\User;
use Modules\Kepegawaian\Models\DataInduk;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSIS KOORDINATOR JENJANG ===\n\n";

// Find all Koor Jenjang users
$koors = User::whereHas('roles', function ($q) {
    $q->where('name', 'koor_jenjang');
})->get();

if ($koors->isEmpty()) {
    echo "[!] Tidak ada user dengan role 'koor_jenjang'.\n";
    exit;
}

foreach ($koors as $koor) {
    echo "User: " . $koor->name . " (" . $koor->email . ")\n";
    echo "  - ID: " . $koor->id . "\n";

    // Check employee record
    if (!$koor->employee) {
        echo "  - [MASALAH] Tidak punya Employee Record (DataInduk)\n";
        echo "  - Solusi: Buat DataInduk untuk user ini dan hubungkan via user_id\n\n";
        continue;
    }

    echo "  - Employee Record: Ada (ID: " . $koor->employee->id . ")\n";
    echo "  - Nama Pegawai: " . $koor->employee->nama . "\n";

    // Check unit assignment
    $units = $koor->employee->units;
    if ($units->isEmpty()) {
        echo "  - [MASALAH] Employee belum di-assign ke Unit/Jenjang manapun\n";
        echo "  - Solusi: Edit data pegawai ini dan pilih Unit/Jenjang\n\n";
        continue;
    }

    echo "  - Unit/Jenjang: ";
    foreach ($units as $unit) {
        echo $unit->name . " ";
    }
    echo "\n";

    // Check how many employees in those units
    $unitIds = $units->pluck('id')->toArray();
    $employeeCount = DataInduk::whereHas('units', function ($q) use ($unitIds) {
        $q->whereIn('units.id', $unitIds);
    })->count();

    echo "  - Jumlah Pegawai di Unit tersebut: " . $employeeCount . "\n";
    echo "  - [OK] Seharusnya bisa melihat " . $employeeCount . " pegawai\n\n";
}

echo "=== END DIAGNOSIS ===\n";
