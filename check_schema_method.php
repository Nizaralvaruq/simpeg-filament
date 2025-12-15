<?php

use Filament\Schemas\Schema;
use ReflectionClass;

require __DIR__ . '/vendor/autoload.php';

$ref = new ReflectionClass(Schema::class);
if ($ref->hasMethod('components')) {
    echo "Method components EXISTS\n";
} else {
    echo "Method components DOES NOT EXIST\n";
}

if ($ref->hasMethod('schema')) {
    echo "Method schema EXISTS\n";
} else {
    echo "Method schema DOES NOT EXIST\n";
}
