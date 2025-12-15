<?php

require __DIR__ . '/vendor/autoload.php';

$classes = [
    'Filament\Schemas\Components\Section',
    'Filament\Forms\Components\TextInput',
    'Filament\Actions\EditAction',
    'Filament\Actions\DeleteBulkAction',
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "OK: $class\n";
    } else {
        echo "MISSING: $class\n";
    }
}
