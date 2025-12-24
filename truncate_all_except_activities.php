<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TRUNCATING ALL TABLES (EXCEPT activities_logs) ===\n\n";

$pgsql = DB::connection('pgsql');

// Tables to exclude from truncation
$excludedTables = [
    'migrations',
    'cache',
    'cache_locks',
    'sessions',
    'jobs',
    'failed_jobs',
    'activities_logs', // DON'T TOUCH THIS ONE
];

// Get all tables
$tables = $pgsql->select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name");

$toTruncate = [];
foreach ($tables as $t) {
    $table = $t->table_name;
    if (!in_array($table, $excludedTables)) {
        $count = $pgsql->table($table)->count();
        $toTruncate[] = [
            'name' => $table,
            'count' => $count
        ];
    }
}

echo "Tables to truncate: " . count($toTruncate) . "\n\n";

$totalRecords = 0;
foreach ($toTruncate as $info) {
    echo sprintf("%-40s : %8d records\n", $info['name'], $info['count']);
    $totalRecords += $info['count'];
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "Total records to delete: " . number_format($totalRecords) . "\n";
echo "activities_logs will be preserved!\n\n";

echo "Type 'TRUNCATE' to proceed: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if ($line !== 'TRUNCATE') {
    echo "Operation cancelled.\n";
    exit(0);
}

echo "\nTruncating tables...\n\n";

// Disable foreign key checks temporarily
$pgsql->statement('SET session_replication_role = replica;');

$truncated = 0;
$errors = 0;

foreach ($toTruncate as $info) {
    $table = $info['name'];
    try {
        $pgsql->statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE");
        echo "✓ {$table}\n";
        $truncated++;
    } catch (\Exception $e) {
        echo "✗ {$table}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Re-enable foreign key checks
$pgsql->statement('SET session_replication_role = DEFAULT;');

echo "\n" . str_repeat("=", 70) . "\n";
echo "✅ Truncation complete!\n";
echo "  Tables truncated: {$truncated}\n";
echo "  Errors: {$errors}\n\n";

// Verify activities_logs is untouched
$activitiesCount = $pgsql->table('activities_logs')->count();
echo "activities_logs count: " . number_format($activitiesCount) . " (preserved)\n";

