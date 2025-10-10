<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Fixing orphaned notes...\n";
    
    // Get all notes with missing client relationships
    echo "1. Finding notes with missing client relationships...\n";
    $notesWithMissingClients = DB::connection('mysql')->select("
        SELECT n.id, n.client_id, n.description 
        FROM notes n 
        LEFT JOIN admins a ON n.client_id = a.id 
        WHERE a.id IS NULL AND n.client_id IS NOT NULL
    ");
    
    echo "Found " . count($notesWithMissingClients) . " notes with missing client relationships\n";
    
    // Show some examples
    echo "\nExamples of problematic notes:\n";
    foreach (array_slice($notesWithMissingClients, 0, 5) as $note) {
        echo "- Note ID: {$note->id}, Client ID: {$note->client_id}, Description: " . substr($note->description, 0, 50) . "...\n";
    }
    
    echo "\n2. Options to fix this:\n";
    echo "A. Set client_id to NULL for orphaned notes (safe, preserves notes)\n";
    echo "B. Delete orphaned notes (removes them completely)\n";
    echo "C. Skip fixing (will continue to cause errors)\n";
    
    // For now, let's go with option A - set client_id to NULL
    echo "\n3. Setting client_id to NULL for orphaned notes (Option A)...\n";
    
    $fixedCount = 0;
    foreach ($notesWithMissingClients as $note) {
        try {
            DB::connection('mysql')->table('notes')
                ->where('id', $note->id)
                ->update(['client_id' => null]);
            $fixedCount++;
            
            if ($fixedCount % 10 == 0) {
                echo "Fixed $fixedCount notes...\n";
            }
        } catch (Exception $e) {
            echo "❌ Failed to fix note ID {$note->id}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "✅ Fixed $fixedCount orphaned notes\n";
    
    // Verify the fix
    echo "\n4. Verifying the fix...\n";
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
    
    // Test the dashboard query again
    echo "\n5. Testing dashboard query...\n";
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
                echo "- Note ID: {$note->id}, Client: [No client assigned]\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Dashboard query test failed: " . $e->getMessage() . "\n";
    }
    
    // Also check the component file to see if we need to add null checks
    echo "\n6. Checking the dashboard component...\n";
    $componentPath = 'resources/views/components/dashboard/note-item.blade.php';
    
    if (file_exists($componentPath)) {
        echo "Found component file: $componentPath\n";
        
        $componentContent = file_get_contents($componentPath);
        
        // Check if it has null checks for $client
        if (strpos($componentContent, '$client &&') !== false || strpos($componentContent, '$client?') !== false) {
            echo "✅ Component already has null checks for client\n";
        } else {
            echo "⚠️  Component may need null checks for client object\n";
            echo "The component should check if \$client exists before accessing its properties\n";
        }
    } else {
        echo "⚠️  Component file not found: $componentPath\n";
    }
    
    echo "\n🎉 Orphaned notes fix completed!\n";
    echo "The dashboard should now work without the 'first_name on null' error.\n";
    
} catch(Exception $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
