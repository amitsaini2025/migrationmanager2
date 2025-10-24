<?php
require 'vendor/autoload.php';

$class = 'App\Http\Controllers\API\ClientPortalMessageController';
$method = 'sendMessageToClient';

echo "Class exists: " . (class_exists($class) ? 'YES' : 'NO') . PHP_EOL;
echo "Method exists: " . (method_exists($class, $method) ? 'YES' : 'NO') . PHP_EOL;

if (class_exists($class)) {
    $reflection = new ReflectionClass($class);
    echo "File: " . $reflection->getFileName() . PHP_EOL;
    echo "Methods:" . PHP_EOL;
    foreach ($reflection->getMethods() as $m) {
        echo "  - " . $m->getName() . PHP_EOL;
    }
}

