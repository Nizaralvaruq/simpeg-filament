<?php
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migrations = Illuminate\Support\Facades\DB::table('migrations')->get();
$cbtMigrations = [];
foreach ($migrations as $m) {
    if (strpos($m->migration, 'cbt') !== false || 
        strpos($m->migration, 'students') !== false || 
        strpos($m->migration, 'subjects') !== false || 
        strpos($m->migration, 'question') !== false || 
        strpos($m->migration, 'exam') !== false) {
        $cbtMigrations[] = $m->migration;
    }
}
echo "Found migrations:\n";
print_r($cbtMigrations);
