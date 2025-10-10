<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Fixing notes and client relationships...\n";
    
    // Check notes table and their client relationships
    echo "1. Checking notes table...\n";
    $notesCount = DB::connection('mysql')->select('SELECT COUNT(*) as count FROM notes')[0]->count;
    echo "Total notes: $notesCount\n";
    
    // Check for notes with missing clients
    echo "2. Checking for notes with missing client relationships...\n";
    $notesWithMissingClients = DB::connection('mysql')->select("
        SELECT n.id, n.client_id, n.description 
        FROM notes n 
        LEFT JOIN admins a ON n.client_id = a.id 
        WHERE a.id IS NULL AND n.client_id IS NOT NULL
        LIMIT 10
    ");
    
    if (!empty($notesWithMissingClients)) {
        echo "❌ Found " . count($notesWithMissingClients) . " notes with missing client relationships:\n";
        foreach ($notesWithMissingClients as $note) {
            echo "- Note ID: {$note->id}, Client ID: {$note->client_id}, Description: " . substr($note->description, 0, 50) . "...\n";
        }
        
        echo "\n3. Checking if these clients exist in old database...\n";
        
        foreach ($notesWithMissingClients as $note) {
            if ($note->client_id) {
                $oldClient = DB::connection('old_db')->select('SELECT * FROM admins WHERE id = ?', [$note->client_id]);
                
                if (!empty($oldClient)) {
                    echo "Found missing client ID {$note->client_id} in old database. Restoring...\n";
                    
                    $client = $oldClient[0];
                    
                    // Check if client already exists in current database
                    $existingClient = DB::connection('mysql')->select('SELECT id FROM admins WHERE id = ?', [$note->client_id]);
                    
                    if (empty($existingClient)) {
                        // Insert the missing client
                        $clientData = (array)$client;
                        unset($clientData['id']); // Remove id to avoid conflicts
                        
                        try {
                            DB::connection('mysql')->table('admins')->insert($clientData);
                            echo "✅ Restored client ID {$note->client_id}\n";
                        } catch (Exception $e) {
                            echo "❌ Failed to restore client ID {$note->client_id}: " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "⚠️  Client ID {$note->client_id} already exists in current database\n";
                    }
                } else {
                    echo "⚠️  Client ID {$note->client_id} not found in old database either\n";
                }
            }
        }
    } else {
        echo "✅ No notes with missing client relationships found\n";
    }
    
    // Check if there are any orphaned notes (notes without client_id)
    echo "\n4. Checking for orphaned notes...\n";
    $orphanedNotes = DB::connection('mysql')->select('SELECT COUNT(*) as count FROM notes WHERE client_id IS NULL');
    $orphanedCount = $orphanedNotes[0]->count;
    
    if ($orphanedCount > 0) {
        echo "⚠️  Found $orphanedCount orphaned notes (no client_id)\n";
        
        // Get some examples
        $orphanedExamples = DB::connection('mysql')->select('SELECT id, description FROM notes WHERE client_id IS NULL LIMIT 5');
        echo "Examples of orphaned notes:\n";
        foreach ($orphanedExamples as $note) {
            echo "- Note ID: {$note->id}, Description: " . substr($note->description, 0, 50) . "...\n";
        }
        
        echo "\n5. Attempting to fix orphaned notes...\n";
        echo "This would require manual review. For now, we'll leave them as is.\n";
    } else {
        echo "✅ No orphaned notes found\n";
    }
    
    // Verify the fix
    echo "\n6. Final verification...\n";
    $notesWithMissingClientsAfter = DB::connection('mysql')->select("
        SELECT COUNT(*) as count 
        FROM notes n 
        LEFT JOIN admins a ON n.client_id = a.id 
        WHERE a.id IS NULL AND n.client_id IS NOT NULL
    ");
    
    $missingClientsAfter = $notesWithMissingClientsAfter[0]->count;
    
    if ($missingClientsAfter == 0) {
        echo "✅ All notes now have valid client relationships!\n";
    } else {
        echo "⚠️  Still have $missingClientsAfter notes with missing client relationships\n";
    }
    
    // Test a sample query that the dashboard would use
    echo "\n7. Testing dashboard query...\n";
    try {
        $testNotes = DB::connection('mysql')->select("
            SELECT n.*, a.first_name, a.last_name, a.client_id 
            FROM notes n 
            LEFT JOIN admins a ON n.client_id = a.id 
            WHERE n.note_deadline IS NOT NULL AND n.status != 1 
            LIMIT 5
        ");
        
        echo "✅ Dashboard query test successful! Found " . count($testNotes) . " notes\n";
        
        foreach ($testNotes as $note) {
            if ($note->first_name) {
                echo "- Note ID: {$note->id}, Client: {$note->first_name} {$note->last_name}\n";
            } else {
                echo "- Note ID: {$note->id}, Client: [NULL]\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Dashboard query test failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Notes and client relationships fix completed!\n";
    
} catch(Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
