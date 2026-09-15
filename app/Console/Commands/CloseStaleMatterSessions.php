<?php

namespace App\Console\Commands;

use App\Services\StaffMatterSessionService;
use Illuminate\Console\Command;

class CloseStaleMatterSessions extends Command
{
    protected $signature = 'my-day:close-stale-sessions';

    protected $description = 'Close auto file-time sessions whose heartbeat is older than 3 minutes';

    public function handle(StaffMatterSessionService $sessions): int
    {
        $closed = $sessions->closeStale(now());
        $this->info("Closed {$closed} stale matter session(s).");

        return self::SUCCESS;
    }
}
