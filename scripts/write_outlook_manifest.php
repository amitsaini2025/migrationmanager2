<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(App\Http\Controllers\CRM\OutlookAddinController::class);
$xml = $controller->buildManifestXml('https://migrationmanager.bansalcrm.com');

$dir = __DIR__ . '/../public/outlook-addin';
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

file_put_contents($dir . '/manifest.xml', $xml);
file_put_contents($dir . '/manifest.production.xml', $xml);

echo 'wrote ' . strlen($xml) . " bytes\n";
