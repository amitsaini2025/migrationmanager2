<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeadFollowupService;

class MarkOverdueFollowups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'followups:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark pending follow-ups that are past their scheduled date as overdue';

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
        $this->info('Checking for overdue follow-ups...');
        
        $overdueCount = $this->followupService->markOverdueFollowups();
        
        if ($overdueCount > 0) {
            $this->warn("⚠ Marked {$overdueCount} follow-up(s) as overdue.");
        } else {
            $this->info('✓ No overdue follow-ups found. All on track!');
        }
        
        return Command::SUCCESS;
    }
}
