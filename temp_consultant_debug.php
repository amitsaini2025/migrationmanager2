<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = $app->make(App\Services\BansalAppointmentSync\ConsultantAssignmentService::class);
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('determineCalendarType');
$method->setAccessible(true);

$rows = App\Models\BookingAppointment::whereNull('consultant_id')->limit(10)->get();

foreach ($rows as $row) {
    $calendarType = $method->invoke($service, $row->toArray());
    $consultant = $calendarType ? App\Models\AppointmentConsultant::where('calendar_type', $calendarType)->first() : null;
    echo json_encode([
        'id' => $row->id,
        'bansal_id' => $row->bansal_appointment_id,
        'service_id' => $row->service_id,
        'noe_id' => $row->noe_id,
        'location' => $row->location,
        'calendar_type' => $calendarType,
        'consultant_found' => $consultant ? $consultant->id : null
    ], JSON_PRETTY_PRINT) . PHP_EOL;
}
