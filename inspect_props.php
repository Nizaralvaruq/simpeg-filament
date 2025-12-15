<?php

use Filament\Resources\Resource;
use ReflectionClass;

require __DIR__ . '/vendor/autoload.php';

try {
    $ref = new ReflectionClass(Resource::class);
    foreach ($ref->getProperties() as $prop) {
        if ($prop->isStatic()) {
            echo "Property: $" . $prop->getName() . "\n";
            echo "  Type: " . ($prop->getType() ? $prop->getType()->getName() : 'none') . "\n";
            echo "  Nullable: " . ($prop->getType() && $prop->getType()->allowsNull() ? 'Yes' : 'No') . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
