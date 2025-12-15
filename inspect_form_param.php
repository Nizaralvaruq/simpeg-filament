<?php

use Filament\Resources\Resource;
use ReflectionClass;

require __DIR__ . '/vendor/autoload.php';

try {
    $ref = new ReflectionClass(Resource::class);
    if ($ref->hasMethod('form')) {
        $method = $ref->getMethod('form');
        echo "PARAM_START\n";
        foreach ($method->getParameters() as $param) {
            echo $param->getName() . ":" . ($param->getType() ? $param->getType()->getName() : 'none') . "\n";
        }
        echo "PARAM_END\n";
    } else {
        echo "NO_FORM_METHOD\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
