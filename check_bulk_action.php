<?php
require __DIR__ . '/vendor/autoload.php';

$classes = [
    'Filament\Tables\Actions\BulkAction',
    'Filament\Actions\BulkAction',
    'Filament\Support\Actions\BulkAction',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "$class EXISTS\n";
    } else {
        echo "$class NOT FOUND\n";
    }
}
