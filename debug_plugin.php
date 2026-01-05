<?php
require 'vendor/autoload.php';
$ref = new ReflectionClass('DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin');
$results = [];
foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
    if (str_contains($m->getName(), 'Color') || str_contains($m->getName(), 'Panel')) {
        $params = [];
        foreach ($m->getParameters() as $p) {
            $type = $p->hasType() ? $p->getType() : 'mixed';
            $params[] = $type . ' $' . $p->getName();
        }
        $results[] = $m->getName() . '(' . implode(', ', $params) . ')';
    }
}
file_put_contents('plugin_methods.txt', implode(PHP_EOL, $results));
echo "Done! Check plugin_methods.txt";
