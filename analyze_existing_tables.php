<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== EXISTING TABLES ANALYSIS ===\n\n";

echo "DOCUMENTS table:\n";
$cols = DB::select('DESCRIBE documents');
foreach($cols as $c) {
    echo "- {$c->Field} ({$c->Type})\n";
}

echo "\nSIGNERS table:\n";
$cols = DB::select('DESCRIBE signers');
foreach($cols as $c) {
    echo "- {$c->Field} ({$c->Type})\n";
}

echo "\n=== WHAT WE CAN REUSE ===\n";
echo "Documents table already has:\n";
echo "- id, file_name, myfile, user_id, client_id\n";
echo "- status (draft/sent/signed)\n";
echo "- created_at, updated_at\n";
echo "- doc_type, type\n\n";

echo "Signers table already has:\n";
echo "- document_id, email, name, token\n";
echo "- status (pending/signed)\n";
echo "- signed_at, opened_at\n";
echo "- reminder tracking\n\n";

echo "=== WHAT WE NEED TO ADD ===\n";
echo "Minimal additions needed:\n";
echo "1. created_by (who uploaded/sent the doc)\n";
echo "2. documentable_type/id (polymorphic for Client/Lead association)\n";
echo "3. title (human-readable name)\n";
echo "4. last_activity_at (for tracking)\n";
