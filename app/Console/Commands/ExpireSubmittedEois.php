<?php

namespace App\Console\Commands;

use App\Services\ExpireSubmittedEoisService;
use Illuminate\Console\Command;

class ExpireSubmittedEois extends Command
{
    protected $signature = 'eoi:expire-submitted';

    protected $description = 'Mark submitted EOIs as expired after 2 years from submission date';

    public function handle(ExpireSubmittedEoisService $expireSubmittedEois): int
    {
        $count = $expireSubmittedEois->expire();

        $this->info("Expired {$count} submitted EOI record(s).");

        return self::SUCCESS;
    }
}
