<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ACTIVITIES_LOGS COMPARISON ===\n\n";

$mysql = DB::connection('mysql_source');
$pgsql = DB::connection('pgsql');

// Get total counts
$mysqlCount = $mysql->table('activities_logs')->count();
$pgsqlCount = $pgsql->table('activities_logs')->count();

echo "Total Records:\n";
echo "  MySQL: " . number_format($mysqlCount) . "\n";
echo "  PostgreSQL: " . number_format($pgsqlCount) . "\n";
echo "  Difference: " . number_format($mysqlCount - $pgsqlCount) . " records missing in PostgreSQL\n\n";

// Get ID ranges
$mysqlMin = $mysql->table('activities_logs')->min('id');
$mysqlMax = $mysql->table('activities_logs')->max('id');
$pgsqlMin = $pgsql->table('activities_logs')->min('id');
$pgsqlMax = $pgsql->table('activities_logs')->max('id');

echo "ID Ranges:\n";
echo "  MySQL: {$mysqlMin} to {$mysqlMax}\n";
echo "  PostgreSQL: {$pgsqlMin} to {$pgsqlMax}\n\n";

// Sample missing IDs (first 20)
echo "Checking for missing IDs...\n";
$mysqlIds = $mysql->table('activities_logs')
    ->orderBy('id')
    ->limit(10000)
    ->pluck('id')
    ->toArray();

$pgsqlIds = $pgsql->table('activities_logs')
    ->whereIn('id', $mysqlIds)
    ->pluck('id')
    ->toArray();

$missingIds = array_diff($mysqlIds, $pgsqlIds);

echo "Sample of missing IDs (first 20 from sample of 10,000):\n";
$sample = array_slice($missingIds, 0, 20);
foreach ($sample as $id) {
    echo "  - ID: {$id}\n";
}

echo "\nMissing IDs in sample: " . count($missingIds) . " out of 10,000 checked\n\n";

// Check date ranges
echo "Date Ranges:\n";
$mysqlOldest = $mysql->table('activities_logs')->orderBy('created_at')->first();
$mysqlNewest = $mysql->table('activities_logs')->orderBy('created_at', 'desc')->first();
$pgsqlOldest = $pgsql->table('activities_logs')->orderBy('created_at')->first();
$pgsqlNewest = $pgsql->table('activities_logs')->orderBy('created_at', 'desc')->first();

echo "  MySQL oldest: {$mysqlOldest->created_at} (ID: {$mysqlOldest->id})\n";
echo "  MySQL newest: {$mysqlNewest->created_at} (ID: {$mysqlNewest->id})\n";
echo "  PostgreSQL oldest: {$pgsqlOldest->created_at} (ID: {$pgsqlOldest->id})\n";
echo "  PostgreSQL newest: {$pgsqlNewest->created_at} (ID: {$pgsqlNewest->id})\n\n";

// Check if missing records are at the end
echo "Checking if missing records are recent...\n";
$recentMysql = $mysql->table('activities_logs')
    ->where('id', '>', $pgsqlMax)
    ->count();

echo "Records in MySQL with ID > {$pgsqlMax}: " . number_format($recentMysql) . "\n\n";

// Check for gaps in PostgreSQL
echo "Checking for gaps in PostgreSQL IDs...\n";
$gaps = DB::connection('pgsql')->select("
    SELECT 
        id + 1 AS gap_start,
        next_id - 1 AS gap_end,
        next_id - id - 1 AS gap_size
    FROM (
        SELECT id, LEAD(id) OVER (ORDER BY id) AS next_id
        FROM activities_logs
    ) AS gaps
    WHERE next_id - id > 1
    ORDER BY gap_size DESC
    LIMIT 10
");

if (empty($gaps)) {
    echo "No gaps found in PostgreSQL IDs (sequential)\n";
} else {
    echo "Top 10 largest gaps in PostgreSQL:\n";
    foreach ($gaps as $gap) {
        echo sprintf("  Gap: %d to %d (size: %d records)\n", 
            $gap->gap_start, $gap->gap_end, $gap->gap_size);
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "RECOMMENDATION:\n";
if ($recentMysql > ($mysqlCount - $pgsqlCount) * 0.8) {
    echo "✓ Most missing records are recent (high IDs)\n";
    echo "  Strategy: Sync records with ID > {$pgsqlMax}\n";
} else {
    echo "⚠ Missing records are scattered throughout the table\n";
    echo "  Strategy: Full comparison sync needed\n";
}

