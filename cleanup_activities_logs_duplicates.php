<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CLEANING UP ACTIVITIES_LOGS DUPLICATES ===\n\n";

$pgsql = DB::connection('pgsql');

// Check for duplicates
echo "Checking for duplicate IDs...\n";
$duplicates = $pgsql->select("
    SELECT id, COUNT(*) as count
    FROM activities_logs
    GROUP BY id
    HAVING COUNT(*) > 1
    ORDER BY count DESC
    LIMIT 10
");

if (empty($duplicates)) {
    echo "✓ No duplicates found!\n";
    exit(0);
}

echo "Found duplicates! Top 10:\n";
$totalDuplicates = 0;
foreach ($duplicates as $dup) {
    echo "  ID {$dup->id}: {$dup->count} copies\n";
    $totalDuplicates += ($dup->count - 1); // -1 because we keep one
}

// Get total count of duplicate records
$totalDupCount = $pgsql->selectOne("
    SELECT SUM(count - 1) as total_duplicates
    FROM (
        SELECT id, COUNT(*) as count
        FROM activities_logs
        GROUP BY id
        HAVING COUNT(*) > 1
    ) as dups
");

echo "\nTotal duplicate records to remove: " . number_format($totalDupCount->total_duplicates) . "\n\n";

echo "Do you want to remove duplicates? This will:\n";
echo "1. Keep the FIRST occurrence of each ID\n";
echo "2. Delete all duplicate copies\n";
echo "3. Add a PRIMARY KEY constraint to prevent future duplicates\n\n";

echo "Type 'yes' to proceed: ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    echo "Operation cancelled.\n";
    exit(0);
}

echo "\nRemoving duplicates...\n";

// Use PostgreSQL's ctid (internal row ID) to keep only the first occurrence
$result = $pgsql->statement("
    DELETE FROM activities_logs
    WHERE ctid NOT IN (
        SELECT MIN(ctid)
        FROM activities_logs
        GROUP BY id
    )
");

echo "✓ Duplicates removed!\n\n";

// Verify
$newCount = $pgsql->table('activities_logs')->count();
echo "New count: " . number_format($newCount) . "\n\n";

// Add primary key constraint
echo "Adding PRIMARY KEY constraint on id column...\n";
try {
    $pgsql->statement("ALTER TABLE activities_logs ADD PRIMARY KEY (id)");
    echo "✓ PRIMARY KEY constraint added!\n";
} catch (\Exception $e) {
    if (str_contains($e->getMessage(), 'already exists')) {
        echo "✓ PRIMARY KEY already exists\n";
    } else {
        echo "⚠ Error adding PRIMARY KEY: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Cleanup complete!\n";
echo "Future syncs will be much faster with the PRIMARY KEY in place.\n";

