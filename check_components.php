<?php
require __DIR__ . '/vendor/autoload.php';

if (class_exists('Filament\Forms\Components\TextInput')) {
    echo "Forms\Components\TextInput EXISTS\n";
} else {
    echo "Forms\Components\TextInput does NOT exist\n";
    if (class_exists('Filament\Schemas\Components\TextInput')) {
        echo "Schemas\Components\TextInput EXISTS\n";
    }
}
