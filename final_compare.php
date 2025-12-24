<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$mysql = DB::connection('mysql_source');
$pgsql = DB::connection('pgsql');

echo "=== FINAL SYNC RESULTS ===\n\n";

$totalMysql = 0;
$totalPgsql = 0;
$differences = 0;

$tables = $pgsql->select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name");

foreach ($tables as $t) {
    $table = $t->table_name;
    if ($table === 'activities_logs') continue; // Skipped intentionally
    
    $mCount = $mysql->table($table)->count();
    $pCount = $pgsql->table($table)->count();
    $totalMysql += $mCount;
    $totalPgsql += $pCount;
    
    if ($mCount != $pCount) {
        $differences++;
        echo sprintf("%-40s | MySQL: %8d | PostgreSQL: %8d | Diff: %8d\n", 
            $table, $mCount, $pCount, $pCount - $mCount);
    }
}

// Add activities_logs separately
$mActivities = $mysql->table('activities_logs')->count();
$pActivities = $pgsql->table('activities_logs')->count();

echo "\n" . str_repeat("=", 90) . "\n";
echo "Total (excluding activities_logs):\n";
echo "  MySQL: " . number_format($totalMysql) . "\n";
echo "  PostgreSQL: " . number_format($totalPgsql) . "\n";
echo "  Difference: " . number_format($totalPgsql - $totalMysql) . "\n";
echo "  Tables with differences: {$differences}\n\n";

echo "activities_logs (skipped during sync):\n";
echo "  MySQL: " . number_format($mActivities) . "\n";
echo "  PostgreSQL: " . number_format($pActivities) . "\n";
echo "  Remaining to sync: " . number_format($mActivities - $pActivities) . "\n";

echo "\n" . str_repeat("=", 90) . "\n";
if ($differences == 0) {
    echo "✅ ALL TABLES SYNCED PERFECTLY (except activities_logs)!\n";
} else {
    echo "⚠️  {$differences} tables still have differences\n";
}

