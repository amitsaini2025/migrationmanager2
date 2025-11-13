<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = App\Models\AppointmentConsultant::select('id','name','calendar_type','is_active')->get();
foreach ($rows as $row) {
    echo json_encode($row->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;
}
