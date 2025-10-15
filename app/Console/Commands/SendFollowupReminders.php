<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeadFollowupService;

class SendFollowupReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'followups:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming follow-ups (scheduled in the next hour)';

    protected $followupService;

    /**
     * Create a new command instance.
     */
    public function __construct(LeadFollowupService $followupService)
    {
        parent::__construct();
        $this->followupService = $followupService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending follow-up reminders...');
        
        $remindersSent = $this->followupService->sendReminders();
        
        if ($remindersSent > 0) {
            $this->info("✓ Sent {$remindersSent} reminder(s) successfully.");
        } else {
            $this->comment('No reminders to send at this time.');
        }
        
        return Command::SUCCESS;
    }
}
