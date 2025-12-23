<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$oldAppointmentTables = [
    'appointments',
    'appointment_logs', 
    'book_services',
    'book_service_disable_slots',
    'book_service_slot_per_person',
    'nature_of_enquiries',
];

echo "Checking for old appointment system tables...\n\n";

// Try MySQL first
try {
    $conn = DB::connection('mysql');
    $dbName = config('database.connections.mysql.database');
    $result = $conn->select("SHOW TABLES");
    $key = "Tables_in_{$dbName}";
    
    $allTables = array_map(function($row) use ($key) {
        return $row->$key;
    }, $result);
    
    echo "MySQL Database: {$dbName}\n";
    echo "Total tables: " . count($allTables) . "\n\n";
    echo "Old Appointment Tables:\n";
    echo str_repeat("-", 50) . "\n";
    
    $found = [];
    foreach ($oldAppointmentTables as $table) {
        $exists = in_array($table, $allTables);
        echo sprintf("%-35s %s\n", $table, $exists ? "EXISTS" : "NOT FOUND");
        if ($exists) {
            $count = $conn->table($table)->count();
            echo sprintf("  %-33s %d rows\n", "", $count);
            $found[] = $table;
        }
    }
    
    if (count($found) > 0) {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "TABLES TO REMOVE:\n";
        foreach ($found as $table) {
            echo "  - {$table}\n";
        }
    } else {
        echo "\n✓ All old appointment tables already removed.\n";
    }
    
} catch (\Exception $e) {
    echo "MySQL Error: " . $e->getMessage() . "\n";
    echo "\nTrying PostgreSQL...\n\n";
    
    try {
        $conn = DB::connection('pgsql');
        $result = $conn->select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        $allTables = array_map(function($row) {
            return $row->tablename;
        }, $result);
        
        echo "PostgreSQL Database\n";
        echo "Total tables: " . count($allTables) . "\n\n";
        echo "Old Appointment Tables:\n";
        echo str_repeat("-", 50) . "\n";
        
        $found = [];
        foreach ($oldAppointmentTables as $table) {
            $exists = in_array($table, $allTables);
            echo sprintf("%-35s %s\n", $table, $exists ? "EXISTS" : "NOT FOUND");
            if ($exists) {
                $count = $conn->table($table)->count();
                echo sprintf("  %-33s %d rows\n", "", $count);
                $found[] = $table;
            }
        }
        
        if (count($found) > 0) {
            echo "\n" . str_repeat("=", 50) . "\n";
            echo "TABLES TO REMOVE:\n";
            foreach ($found as $table) {
                echo "  - {$table}\n";
            }
        } else {
            echo "\n✓ All old appointment tables already removed.\n";
        }
        
    } catch (\Exception $e2) {
        echo "PostgreSQL Error: " . $e2->getMessage() . "\n";
    }
}

