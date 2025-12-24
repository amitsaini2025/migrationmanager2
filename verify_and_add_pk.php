<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$pgsql = DB::connection('pgsql');

echo "Verifying activities_logs table...\n";
$count = $pgsql->table('activities_logs')->count();
echo "Current count: {$count}\n\n";

if ($count > 0) {
    echo "⚠ Table is not empty. Truncating...\n";
    $pgsql->statement('TRUNCATE TABLE activities_logs RESTART IDENTITY CASCADE');
    echo "✓ Table truncated\n\n";
}

echo "Adding PRIMARY KEY constraint...\n";
try {
    $pgsql->statement("ALTER TABLE activities_logs ADD PRIMARY KEY (id)");
    echo "✓ PRIMARY KEY added successfully!\n";
} catch (\Exception $e) {
    if (str_contains($e->getMessage(), 'already exists')) {
        echo "✓ PRIMARY KEY already exists\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Table is ready for fresh import!\n";

