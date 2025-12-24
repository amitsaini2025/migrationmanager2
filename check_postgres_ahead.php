<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$mysql = DB::connection('mysql_source');
$pgsql = DB::connection('pgsql');

echo "=== TABLES WHERE POSTGRESQL HAS MORE DATA ===\n\n";

$tables = $pgsql->select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name");

$postgresAhead = [];
$totalExtraRecords = 0;

foreach ($tables as $t) {
    $table = $t->table_name;
    if (in_array($table, ['migrations', 'cache', 'cache_locks', 'sessions', 'jobs', 'failed_jobs'])) {
        continue;
    }
    
    try {
        $mCount = $mysql->table($table)->count();
        $pCount = $pgsql->table($table)->count();
        
        if ($pCount > $mCount) {
            $diff = $pCount - $mCount;
            $postgresAhead[] = [
                'table' => $table,
                'mysql' => $mCount,
                'postgres' => $pCount,
                'extra' => $diff
            ];
            $totalExtraRecords += $diff;
        }
    } catch (\Exception $e) {
        // Skip tables that don't exist in MySQL
    }
}

if (empty($postgresAhead)) {
    echo "✓ No tables where PostgreSQL has more data than MySQL\n";
} else {
    usort($postgresAhead, function($a, $b) {
        return $b['extra'] - $a['extra'];
    });
    
    echo "Found " . count($postgresAhead) . " tables where PostgreSQL has MORE data:\n\n";
    
    foreach ($postgresAhead as $info) {
        echo sprintf("%-40s | MySQL: %8d | PostgreSQL: %8d | Extra: %8d\n", 
            $info['table'], $info['mysql'], $info['postgres'], $info['extra']);
    }
    
    echo "\n" . str_repeat("=", 90) . "\n";
    echo "Total extra records in PostgreSQL: " . number_format($totalExtraRecords) . "\n\n";
    
    echo "OPTIONS:\n";
    echo "1. TRUNCATE all these tables and re-import from MySQL (lose PostgreSQL-only data)\n";
    echo "2. Keep PostgreSQL data and only sync missing records from MySQL\n";
    echo "3. Manually review each table\n";
}

