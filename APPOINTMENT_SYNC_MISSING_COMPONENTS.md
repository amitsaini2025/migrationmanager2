# 🔧 Appointment Sync - Missing Components

This document contains all missing components referenced in the main implementation plan.

---

## 📝 TABLE OF CONTENTS
1. [Console Commands](#console-commands)
2. [Mail Class](#mail-class)
3. [Model Scopes & Helpers](#model-scopes)
4. [Config Updates](#config-updates)
5. [.env Variables](#env-variables)
6. [Service Provider Registration](#service-provider-registration)

---

## 1. CONSOLE COMMANDS

### Command 1: Sync Appointments (Main Polling Command)

**File:** `app/Console/Commands/SyncAppointments.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use Illuminate\Support\Facades\Log;

class SyncAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:sync-appointments 
                            {--minutes=10 : Number of minutes to look back for recent appointments}
                            {--force : Force sync even if already running}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync recent appointments from Bansal Immigration website';

    protected AppointmentSyncService $syncService;

    /**
     * Create a new command instance.
     */
    public function __construct(AppointmentSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = $this->option('minutes');
        
        $this->info("Starting appointment sync (last {$minutes} minutes)...");
        $this->newLine();

        try {
            $stats = $this->syncService->syncRecentAppointments($minutes);

            // Display results
            $this->components->info("Sync completed successfully!");
            $this->newLine();
            
            $this->components->twoColumnDetail('Appointments fetched', $stats['fetched']);
            $this->components->twoColumnDetail('New appointments', "<fg=green>{$stats['new']}</>");
            $this->components->twoColumnDetail('Updated appointments', "<fg=blue>{$stats['updated']}</>");
            $this->components->twoColumnDetail('Skipped (duplicates)', "<fg=yellow>{$stats['skipped']}</>");
            $this->components->twoColumnDetail('Failed', "<fg=red>{$stats['failed']}</>");
            
            if ($stats['failed'] > 0 && !empty($stats['errors'])) {
                $this->newLine();
                $this->warn('⚠️  Errors encountered:');
                foreach ($stats['errors'] as $error) {
                    $this->line("  • Appointment #{$error['appointment_id']}: {$error['error']}");
                }
            }

            $this->newLine();
            $this->info('✓ Sync process completed.');

            // Return success code
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('✗ Sync failed: ' . $e->getMessage());
            
            Log::error('Console: Appointment sync command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
```

---

### Command 2: Send Appointment Reminders

**File:** `app/Console/Commands/SendAppointmentReminders.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\NotificationService;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:send-reminders 
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders for appointments scheduled tomorrow';

    protected NotificationService $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending appointment reminders...');
        $this->newLine();

        try {
            if ($this->option('dry-run')) {
                $this->warn('🔍 DRY RUN MODE - No SMS will be sent');
                $this->newLine();
                
                // Show what would be sent
                $tomorrow = now()->addDay()->startOfDay();
                $endOfTomorrow = now()->addDay()->endOfDay();
                
                $appointments = \App\Models\BookingAppointment::where('reminder_sms_sent', false)
                    ->where('status', 'confirmed')
                    ->whereBetween('appointment_datetime', [$tomorrow, $endOfTomorrow])
                    ->get();
                
                $this->table(
                    ['Client', 'Phone', 'Date/Time', 'Location'],
                    $appointments->map(fn($apt) => [
                        $apt->client_name,
                        $apt->client_phone,
                        $apt->appointment_datetime->format('d/m/Y H:i'),
                        ucfirst($apt->location)
                    ])
                );
                
                $this->info("Would send {$appointments->count()} reminder(s)");
                return Command::SUCCESS;
            }

            // Actually send reminders
            $stats = $this->notificationService->sendUpcomingReminders();

            $this->components->info('Reminders sent successfully!');
            $this->newLine();
            
            $this->components->twoColumnDetail('Total appointments', $stats['total']);
            $this->components->twoColumnDetail('Reminders sent', "<fg=green>{$stats['sent']}</>");
            $this->components->twoColumnDetail('Failed', "<fg=red>{$stats['failed']}</>");
            
            $this->newLine();

            if ($stats['sent'] > 0) {
                $this->info("✓ Sent {$stats['sent']} reminder(s) successfully.");
            } else {
                $this->comment('No reminders to send at this time.');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('✗ Reminder sending failed: ' . $e->getMessage());
            
            Log::error('Console: Send reminders command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
```

---

### Command 3: Test API Connection

**File:** `app/Console/Commands/TestBansalApi.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\BansalApiClient;
use Illuminate\Support\Facades\Config;

class TestBansalApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:test-api 
                            {--show-config : Display current API configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to Bansal Immigration API';

    protected BansalApiClient $apiClient;

    /**
     * Create a new command instance.
     */
    public function __construct(BansalApiClient $apiClient)
    {
        parent::__construct();
        $this->apiClient = $apiClient;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔌 Testing Bansal API Connection...');
        $this->newLine();

        // Show config if requested
        if ($this->option('show-config')) {
            $this->displayConfig();
            $this->newLine();
        }

        // Test 1: Connection Test
        $this->components->task('Testing API connection', function () {
            return $this->apiClient->testConnection();
        });

        // Test 2: Fetch Recent Appointments (1 minute)
        $this->components->task('Fetching recent appointments (1 min)', function () {
            try {
                $appointments = $this->apiClient->getRecentAppointments(1);
                $count = is_array($appointments) ? count($appointments) : 0;
                $this->line("  Found: {$count} appointment(s)");
                return true;
            } catch (\Exception $e) {
                $this->line("  Error: {$e->getMessage()}");
                return false;
            }
        });

        // Test 3: Fetch Single Appointment (if available)
        $this->newLine();
        $this->info('API connection test completed!');
        $this->newLine();

        // Additional diagnostics
        $this->displayDiagnostics();

        return Command::SUCCESS;
    }

    /**
     * Display current API configuration
     */
    protected function displayConfig()
    {
        $this->components->info('Current API Configuration:');
        
        $url = config('services.bansal_api.url');
        $token = config('services.bansal_api.token');
        $timeout = config('services.bansal_api.timeout', 30);
        
        $this->components->twoColumnDetail('API URL', $url ?: '<fg=red>NOT SET</>');
        $this->components->twoColumnDetail('API Token', $token ? '<fg=green>SET</> (' . substr($token, 0, 10) . '...)' : '<fg=red>NOT SET</>');
        $this->components->twoColumnDetail('Timeout', $timeout . 's');
    }

    /**
     * Display diagnostic information
     */
    protected function displayDiagnostics()
    {
        $this->components->info('System Diagnostics:');
        
        // Check database tables
        $tablesExist = [
            'appointment_consultants' => \Illuminate\Support\Facades\Schema::hasTable('appointment_consultants'),
            'booking_appointments' => \Illuminate\Support\Facades\Schema::hasTable('booking_appointments'),
            'appointment_sync_logs' => \Illuminate\Support\Facades\Schema::hasTable('appointment_sync_logs'),
        ];
        
        foreach ($tablesExist as $table => $exists) {
            $status = $exists ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $this->components->twoColumnDetail("Table: {$table}", $status);
        }
        
        // Check consultant count
        if ($tablesExist['appointment_consultants']) {
            $consultantCount = \App\Models\AppointmentConsultant::count();
            $activeCount = \App\Models\AppointmentConsultant::where('is_active', true)->count();
            $this->components->twoColumnDetail('Consultants (Active/Total)', "{$activeCount}/{$consultantCount}");
        }
        
        // Check sync logs
        if ($tablesExist['appointment_sync_logs']) {
            $lastSync = \App\Models\AppointmentSyncLog::latest('started_at')->first();
            $lastSyncTime = $lastSync ? $lastSync->started_at->diffForHumans() : 'Never';
            $this->components->twoColumnDetail('Last sync', $lastSyncTime);
        }
    }
}
```

---

### Command 4: Backfill Historical Data (Bonus)

**File:** `app/Console/Commands/BackfillAppointments.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use Carbon\Carbon;

class BackfillAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:backfill 
                            {--from= : Start date (Y-m-d)}
                            {--to= : End date (Y-m-d)}
                            {--days=30 : Number of days to backfill (if from/to not specified)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill historical appointments from Bansal website';

    protected AppointmentSyncService $syncService;

    public function __construct(AppointmentSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Determine date range
        if ($this->option('from') && $this->option('to')) {
            $startDate = Carbon::parse($this->option('from'));
            $endDate = Carbon::parse($this->option('to'));
        } else {
            $days = $this->option('days');
            $startDate = now()->subDays($days)->startOfDay();
            $endDate = now()->endOfDay();
        }

        $this->warn('⚠️  This will backfill appointments from:');
        $this->line("  From: {$startDate->format('Y-m-d')}");
        $this->line("  To:   {$endDate->format('Y-m-d')}");
        $this->newLine();

        if (!$this->confirm('Do you want to continue?', true)) {
            $this->info('Backfill cancelled.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('Starting backfill...');
        $this->newLine();

        try {
            $stats = $this->syncService->backfillHistoricalData($startDate, $endDate);

            $this->components->info('Backfill completed successfully!');
            $this->newLine();
            
            $this->components->twoColumnDetail('Appointments fetched', $stats['fetched']);
            $this->components->twoColumnDetail('New appointments', "<fg=green>{$stats['new']}</>");
            $this->components->twoColumnDetail('Skipped (duplicates)', "<fg=yellow>{$stats['skipped']}</>");
            $this->components->twoColumnDetail('Failed', "<fg=red>{$stats['failed']}</>");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('✗ Backfill failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
```

---

## 2. MAIL CLASS

### AppointmentDetailedConfirmation Mail

**File:** `app/Mail/AppointmentDetailedConfirmation.php`

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentDetailedConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $details;

    /**
     * Create a new message instance.
     *
     * @param array $details Appointment details
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Appointment Confirmation - Bansal Immigration')
                    ->view('emails.appointment-detailed-confirmation')
                    ->with([
                        'clientName' => $this->details['client_name'],
                        'appointmentDate' => $this->details['appointment_datetime'],
                        'timeslot' => $this->details['timeslot_full'],
                        'location' => ucfirst($this->details['location']),
                        'consultant' => $this->details['consultant'] ?? 'Your assigned consultant',
                        'serviceType' => $this->details['service_type'] ?? 'Immigration Consultation',
                        'adminNotes' => $this->details['admin_notes'] ?? null,
                    ]);
    }
}
```

**Email View:** `resources/views/emails/appointment-detailed-confirmation.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e3a8a; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #1e3a8a; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; color: #1e3a8a; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 12px; }
        .button { display: inline-block; padding: 12px 30px; background: #1e3a8a; color: white !important; text-decoration: none; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Appointment Confirmed</h1>
            <p>Bansal Immigration</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $clientName }},</p>
            
            <p>This email confirms your appointment details with Bansal Immigration:</p>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="label">📅 Date:</span> 
                    {{ \Carbon\Carbon::parse($appointmentDate)->format('l, F j, Y') }}
                </div>
                <div class="info-row">
                    <span class="label">🕐 Time:</span> 
                    {{ $timeslot }}
                </div>
                <div class="info-row">
                    <span class="label">📍 Location:</span> 
                    {{ $location }} Office
                </div>
                <div class="info-row">
                    <span class="label">👨‍💼 Consultant:</span> 
                    {{ $consultant }}
                </div>
                <div class="info-row">
                    <span class="label">🎯 Service:</span> 
                    {{ $serviceType }}
                </div>
            </div>
            
            @if($adminNotes)
            <div class="info-box">
                <div class="info-row">
                    <span class="label">📝 Important Notes:</span><br>
                    <p style="margin-top: 10px;">{{ $adminNotes }}</p>
                </div>
            </div>
            @endif
            
            <p><strong>Please arrive 10 minutes early and bring:</strong></p>
            <ul>
                <li>Valid photo ID (Passport/Driver's License)</li>
                <li>Any relevant documents for your case</li>
                <li>Proof of payment (if applicable)</li>
            </ul>
            
            <center>
                <a href="https://bansalimmigration.com/my-appointments" class="button">
                    View My Appointments
                </a>
            </center>
            
            <p><strong>Need to reschedule or cancel?</strong><br>
            Please call us at <strong>1300 859 368</strong> or email <strong>info@bansalimmigration.com</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>Bansal Immigration</strong><br>
            📧 info@bansalimmigration.com | 📞 1300 859 368<br>
            Melbourne | Adelaide | India</p>
            
            <p style="font-size: 11px; color: #9ca3af;">
                This is an automated confirmation email from Bansal Immigration CRM.
            </p>
        </div>
    </div>
</body>
</html>
```

---

## 3. MODEL SCOPES & HELPERS

### Update BookingAppointment Model

**Add these methods to:** `app/Models/BookingAppointment.php`

```php
/**
 * Scope: Active appointments (not cancelled)
 */
public function scopeActive($query)
{
    return $query->whereNotIn('status', ['cancelled', 'no_show']);
}

/**
 * Scope: Upcoming appointments
 */
public function scopeUpcoming($query)
{
    return $query->where('appointment_datetime', '>', now())
                 ->whereIn('status', ['pending', 'confirmed']);
}

/**
 * Scope: Past appointments
 */
public function scopePast($query)
{
    return $query->where('appointment_datetime', '<', now());
}

/**
 * Scope: Today's appointments
 */
public function scopeToday($query)
{
    return $query->whereDate('appointment_datetime', today());
}

/**
 * Scope: Filter by status
 */
public function scopeStatus($query, $status)
{
    return $query->where('status', $status);
}

/**
 * Attribute: Status badge color
 */
public function getStatusBadgeAttribute(): string
{
    return match($this->status) {
        'pending' => 'warning',
        'confirmed' => 'success',
        'completed' => 'primary',
        'cancelled' => 'danger',
        'no_show' => 'secondary',
        'rescheduled' => 'info',
        default => 'secondary'
    };
}

/**
 * Attribute: Formatted date
 */
public function getFormattedDateAttribute(): string
{
    return $this->appointment_datetime->format('d/m/Y');
}

/**
 * Attribute: Formatted time
 */
public function getFormattedTimeAttribute(): string
{
    return $this->appointment_datetime->format('h:i A');
}

/**
 * Check if appointment is upcoming
 */
public function isUpcoming(): bool
{
    return $this->appointment_datetime->isFuture() 
        && in_array($this->status, ['pending', 'confirmed']);
}

/**
 * Check if appointment is overdue
 */
public function isOverdue(): bool
{
    return $this->appointment_datetime->isPast() 
        && in_array($this->status, ['pending', 'confirmed']);
}

/**
 * Check if reminder should be sent
 */
public function shouldSendReminder(): bool
{
    if ($this->reminder_sms_sent || $this->status !== 'confirmed') {
        return false;
    }
    
    $tomorrow = now()->addDay()->startOfDay();
    $endOfTomorrow = now()->addDay()->endOfDay();
    
    return $this->appointment_datetime->between($tomorrow, $endOfTomorrow);
}
```

---

### Update AppointmentConsultant Model

**Add these methods to:** `app/Models/AppointmentConsultant.php`

```php
/**
 * Scope: Active consultants only
 */
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

/**
 * Scope: Filter by calendar type
 */
public function scopeCalendarType($query, $type)
{
    return $query->where('calendar_type', $type);
}

/**
 * Scope: Filter by location
 */
public function scopeLocation($query, $location)
{
    return $query->where('location', $location);
}

/**
 * Get appointments count for this consultant
 */
public function getAppointmentsCountAttribute(): int
{
    return $this->appointments()->count();
}

/**
 * Get upcoming appointments count
 */
public function getUpcomingAppointmentsCountAttribute(): int
{
    return $this->appointments()
                ->where('appointment_datetime', '>', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();
}
```

---

### Update AppointmentSyncLog Model

**Add these methods to:** `app/Models/AppointmentSyncLog.php`

```php
/**
 * Scope: Recent logs (limit)
 */
public function scopeRecent($query, $limit = 20)
{
    return $query->latest('started_at')->limit($limit);
}

/**
 * Scope: Failed syncs only
 */
public function scopeFailed($query)
{
    return $query->where('status', 'failed');
}

/**
 * Scope: Successful syncs only
 */
public function scopeSuccessful($query)
{
    return $query->where('status', 'success');
}

/**
 * Scope: Today's logs
 */
public function scopeToday($query)
{
    return $query->whereDate('started_at', today());
}

/**
 * Attribute: Duration in seconds
 */
public function getDurationAttribute(): ?float
{
    if (!$this->completed_at) {
        return null;
    }
    
    return $this->started_at->diffInSeconds($this->completed_at);
}

/**
 * Attribute: Duration formatted
 */
public function getFormattedDurationAttribute(): string
{
    $duration = $this->duration;
    
    if (!$duration) {
        return 'In progress...';
    }
    
    if ($duration < 60) {
        return round($duration, 1) . 's';
    }
    
    return round($duration / 60, 1) . 'm';
}

/**
 * Check if sync is still running
 */
public function isRunning(): bool
{
    return $this->status === 'running';
}

/**
 * Check if sync was successful
 */
public function wasSuccessful(): bool
{
    return $this->status === 'success';
}
```

---

## 4. CONFIG UPDATES

### Update config/services.php

**Replace existing `appointment_api` section (lines 61-65) with:**

```php
// Bansal Website API for Appointment Sync
'bansal_api' => [
    'url' => env('BANSAL_API_URL', 'https://bansalimmigration.com/api/crm'),
    'token' => env('BANSAL_API_TOKEN'),
    'timeout' => env('BANSAL_API_TIMEOUT', 30),
],
```

**Note:** The existing config uses `appointment_api` but the plan uses `bansal_api`. Update ALL service references:

- In `BansalApiClient.php` line 929: `config('services.bansal_api.url')`
- In `BansalApiClient.php` line 930: `config('services.bansal_api.token')`
- In `BansalApiClient.php` line 931: `config('services.bansal_api.timeout')`

---

### Update config/app.php

**Add to `providers` array:**

```php
'providers' => [
    // ... existing providers
    
    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // ... other providers
    
    // ✨ NEW: Appointment Sync Service Provider
    App\Providers\AppointmentSyncServiceProvider::class,
],
```

---

### Update config/mail.php (if needed)

**Add admin notification email:**

```php
return [
    // ... existing config
    
    // Admin email for error notifications
    'admin_email' => env('ADMIN_EMAIL', 'admin@bansalimmigration.com'),
];
```

---

## 5. .ENV VARIABLES

**Add these to your `.env` file:**

```env
# ============================================
# Bansal Website API Configuration
# ============================================
BANSAL_API_URL=https://bansalimmigration.com/api/crm
BANSAL_API_TOKEN=your_secure_token_here_minimum_32_characters
BANSAL_API_TIMEOUT=30

# Admin notifications
ADMIN_EMAIL=admin@bansalimmigration.com

# ============================================
# Existing SMS Configuration (verify these)
# ============================================
# These should already exist, just documenting
CELLCAST_API_KEY=your_cellcast_key
CELLCAST_SENDER_ID=BANSAL IMMIGRATION
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=your_twilio_phone
```

---

## 6. SERVICE PROVIDER REGISTRATION

### Create AppointmentSyncServiceProvider

**File:** `app/Providers/AppointmentSyncServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BansalAppointmentSync\BansalApiClient;
use App\Services\BansalAppointmentSync\AppointmentSyncService;
use App\Services\BansalAppointmentSync\ClientMatchingService;
use App\Services\BansalAppointmentSync\ConsultantAssignmentService;
use App\Services\BansalAppointmentSync\NotificationService;

class AppointmentSyncServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register as singletons for better performance
        $this->app->singleton(BansalApiClient::class, function ($app) {
            return new BansalApiClient(
                config('services.bansal_api.url'),
                config('services.bansal_api.token'),
                config('services.bansal_api.timeout', 30)
            );
        });

        $this->app->singleton(ClientMatchingService::class);
        $this->app->singleton(ConsultantAssignmentService::class);
        $this->app->singleton(NotificationService::class);
        
        $this->app->singleton(AppointmentSyncService::class, function ($app) {
            return new AppointmentSyncService(
                $app->make(BansalApiClient::class),
                $app->make(ClientMatchingService::class),
                $app->make(ConsultantAssignmentService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register custom gates/policies if needed
        $this->registerPolicies();
    }

    /**
     * Register authorization gates
     */
    protected function registerPolicies(): void
    {
        \Illuminate\Support\Facades\Gate::define('manage-sync', function ($user) {
            // Only admin (role=1) or super admin (role=12) can manage sync
            return in_array($user->role, [1, 12]);
        });
    }
}
```

---

## 7. ADDITIONAL FIXES NEEDED IN MAIN PLAN

### Fix 1: BansalApiClient Constructor

**In main plan, line 929-931, change config keys:**

```php
// FROM:
$this->baseUrl = $baseUrl ?: config('services.bansal_api.url', 'https://bansalimmigration.com/api/crm');
$this->apiToken = $apiToken ?: config('services.bansal_api.token');
$this->timeout = $timeout ?: config('services.bansal_api.timeout', 30);

// TO (keep as is, config is correct):
$this->baseUrl = $baseUrl ?: config('services.bansal_api.url', 'https://bansalimmigration.com/api/crm');
$this->apiToken = $apiToken ?: config('services.bansal_api.token');
$this->timeout = $timeout ?: config('services.bansal_api.timeout', 30);
```

**Note:** Config is correct in plan, no change needed!

---

### Fix 2: ClientMatchingService - Auth Fallback

**Line 1211, improve fallback for system user:**

```php
// CURRENT:
'admin_id' => Auth::id() ?? 1, // System user

// BETTER:
'admin_id' => Auth::id() ?? config('app.system_user_id', 1), // System user
```

**Add to config/app.php:**

```php
// System user ID for automated processes
'system_user_id' => env('SYSTEM_USER_ID', 1),
```

---

### Fix 3: Missing Package Check

**Verify these packages are installed (already in composer.json):**

```bash
composer require yajra/laravel-datatables-oracle:^12.0
composer require spatie/laravel-activitylog:^4.7
```

✅ Both already present in composer.json!

---

## 8. QUICK START CHECKLIST

After creating all files, run these commands:

```bash
# 1. Create console command files
php artisan make:command SyncAppointments
php artisan make:command SendAppointmentReminders
php artisan make:command TestBansalApi
php artisan make:command BackfillAppointments

# 2. Create mail class
php artisan make:mail AppointmentDetailedConfirmation --markdown=emails.appointment-detailed-confirmation

# 3. Update .env with API credentials
nano .env  # Add BANSAL_API_* variables

# 4. Register service provider
# Edit config/app.php and add AppointmentSyncServiceProvider

# 5. Create migrations
php artisan migrate

# 6. Seed consultants
php artisan db:seed --class=AppointmentConsultantSeeder

# 7. Test API connection
php artisan booking:test-api --show-config

# 8. Run first sync
php artisan booking:sync-appointments --minutes=30

# 9. Check results
php artisan tinker
>>> App\Models\BookingAppointment::count()
>>> App\Models\AppointmentSyncLog::latest()->first()
```

---

## 9. TROUBLESHOOTING COMMON ISSUES

### Issue: "Class 'App\Models\AppointmentConsultant' not found"

**Solution:** Model doesn't exist yet. Create it:

```bash
php artisan make:model AppointmentConsultant
```

Then add relationships:

```php
public function appointments()
{
    return $this->hasMany(BookingAppointment::class, 'consultant_id');
}
```

---

### Issue: "Call to undefined method active()"

**Solution:** Add scope to model (see section 3 above).

---

### Issue: API returns 401 Unauthorized

**Solution:** 
1. Check BANSAL_API_TOKEN in .env
2. Verify token on Bansal website
3. Test with: `php artisan booking:test-api --show-config`

---

### Issue: Cron not running

**Solution:**

```bash
# Test scheduler
php artisan schedule:list

# Run manually
php artisan schedule:run

# Check cron is set up
crontab -l
# Should have: * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 10. SUMMARY

**Files Created:**
1. ✅ `app/Console/Commands/SyncAppointments.php`
2. ✅ `app/Console/Commands/SendAppointmentReminders.php`
3. ✅ `app/Console/Commands/TestBansalApi.php`
4. ✅ `app/Console/Commands/BackfillAppointments.php`
5. ✅ `app/Mail/AppointmentDetailedConfirmation.php`
6. ✅ `app/Providers/AppointmentSyncServiceProvider.php`

**Files Updated:**
1. ⚠️ `app/Models/BookingAppointment.php` - Add scopes
2. ⚠️ `app/Models/AppointmentConsultant.php` - Add scopes
3. ⚠️ `app/Models/AppointmentSyncLog.php` - Add scopes
4. ⚠️ `config/services.php` - Verify `bansal_api` section
5. ⚠️ `config/app.php` - Add service provider
6. ⚠️ `.env` - Add API credentials

---

**END OF MISSING COMPONENTS DOCUMENT**

---

**Next Steps:**
1. Copy console commands to your project
2. Copy mail class to your project
3. Add model scopes to existing models
4. Update config files
5. Update .env
6. Test with `php artisan booking:test-api`
7. Run first sync!

🎉 All missing components are now documented and ready to implement!

