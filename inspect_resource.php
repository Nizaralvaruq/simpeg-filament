<?php

use Filament\Resources\Resource;
use ReflectionClass;

require __DIR__ . '/vendor/autoload.php';

// Try to avoid full app boot if possible, but Resource might need it.
// $app = require_once __DIR__ . '/bootstrap/app.php';
// $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
// $kernel->bootstrap();

try {
    $ref = new ReflectionClass(Resource::class);
    echo "Class: " . $ref->getName() . "\n";

    if ($ref->hasMethod('form')) {
        $method = $ref->getMethod('form');
        echo "Method: form\n";
        echo "  Static: " . ($method->isStatic() ? 'Yes' : 'No') . "\n";
        echo "  Parameters:\n";
        foreach ($method->getParameters() as $param) {
            $type = $param->getType();
            echo "    " . $param->getName() . " : " . ($type ? $type->getName() : 'none') . "\n";
        }
        $ret = $method->getReturnType();
        echo "  Return: " . ($ret ? $ret->getName() : 'none') . "\n";
    } else {
        echo "Method 'form' not found!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
