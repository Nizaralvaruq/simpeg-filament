<?php

use Filament\Schemas\Schema;
use ReflectionClass;

require __DIR__ . '/vendor/autoload.php';

try {
    $ref = new ReflectionClass(Schema::class);
    foreach ($ref->getMethods() as $method) {
        $name = $method->getName();
        if (stripos($name, 'comp') !== false || stripos($name, 'schem') !== false) {
            echo "Method: " . $name . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
