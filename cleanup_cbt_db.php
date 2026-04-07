<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'students',
    'subjects',
    'question_banks',
    'questions',
    'exams',
    'exam_sessions'
];

foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        Schema::dropIfExists($t);
        echo "Dropped table: $t\n";
    }
}

$cbtMigrations = [
    'create_students_table',
    'create_subjects_table',
    'create_question_banks_table',
    'create_questions_table',
    'create_exams_table',
    'create_exam_sessions_table',
    'cbt_'
];

foreach ($cbtMigrations as $m) {
    $deleted = DB::table('migrations')->where('migration', 'LIKE', '%' . $m . '%')->delete();
    if ($deleted) {
        echo "Deleted migration tracking for: $m (count: $deleted)\n";
    }
}

echo "Cleanup done.\n";
