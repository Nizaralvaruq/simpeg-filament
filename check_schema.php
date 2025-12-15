<?php
require __DIR__ . '/vendor/autoload.php';

if (class_exists('Filament\Schemas\Schema')) {
    echo "Filament\Schemas\Schema EXISTS\n";
} else {
    echo "Filament\Schemas\Schema does NOT exist\n";
}

if (interface_exists('Filament\Forms\Form')) {
    echo "Filament\Forms\Form EXISTS\n";
} else {
    echo "Filament\Forms\Form does NOT exist\n"; // Or it might be a class
    if (class_exists('Filament\Forms\Form')) {
        echo "Filament\Forms\Form is a CLASS\n";
    }
}
