<?php

use Filament\Schemas\Schema;
use ReflectionClass;

require __DIR__ . '/vendor/autoload.php';

try {
    $ref = new ReflectionClass(Schema::class);
    echo "Class: " . $ref->getName() . "\n";
    foreach ($ref->getMethods() as $method) {
        // Just print public methods that return static or self or Schema
        if ($method->isPublic()) {
            echo "Method: " . $method->getName() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
