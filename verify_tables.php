<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = [
    'book_service_disable_slots',
    'book_service_slot_per_persons',
    'book_services',
    'tbl_paid_appointment_payment',
    'appointment_logs',
    'appointments',
];

echo "Verifying tables to remove:\n\n";

$conn = DB::connection('pgsql');
$existing = [];

foreach ($tables as $table) {
    try {
        $result = $conn->select("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?) as exists", [$table]);
        $exists = $result[0]->exists;
        
        if ($exists) {
            $count = $conn->table($table)->count();
            echo "✓ {$table} - EXISTS ({$count} rows)\n";
            $existing[] = $table;
        } else {
            echo "✗ {$table} - NOT FOUND\n";
        }
    } catch (\Exception $e) {
        echo "✗ {$table} - ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Summary: " . count($existing) . " tables found to remove\n";

if (count($existing) > 0) {
    echo "\nTables that will be dropped:\n";
    foreach ($existing as $table) {
        echo "  - {$table}\n";
    }
}

