<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $columns = DB::select('DESCRIBE admins');
    echo "Admins table structure:\n";
    foreach($columns as $col) {
        echo $col->Field . ' - ' . $col->Type . "\n";
    }
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
