<?php

namespace App\Console\Commands;

use App\Services\CompanyContactBackfillService;
use Illuminate\Console\Command;

class BackfillCompanyContacts extends Command
{
    protected $signature = 'crm:backfill-company-contacts
                            {--path= : Workbook of sponsor matters with contact columns. Defaults to public/sponsorship_with_contacts.xlsx}
                            {--commit : Write blank fields on matched companies. Omit this to print a dry run}
                            {--force : Skip the confirmation prompt when using --commit}';

    protected $description = 'Match sponsor rows to existing CRM companies and fill only the company details that are still blank.';

    public function handle(CompanyContactBackfillService $backfill): int
    {
        $path = (string) ($this->option('path') ?: public_path('sponsorship_with_contacts.xlsx'));
        if (! is_file($path)) {
            $this->error('Workbook not found: '.$path);

            return self::FAILURE;
        }

        $plan = $backfill->plan($path);
        $this->table(
            ['Result', 'Count'],
            [
                ['Spreadsheet rows', $plan['rows']],
                ['Companies matched', $plan['matched_companies']],
                ['Matched by matter number', $plan['matched_by_matter']],
                ['Matched by name only', $plan['matched_by_name']],
                ['Companies with blank fields to fill', $plan['companies_to_update']],
                ['Matched companies already complete', $plan['companies_unchanged']],
                ['Rows not found', $plan['not_found']],
                ['Rows skipped, name disagrees with matter', $plan['conflicts']],
                ['Rows skipped, more than one company', $plan['ambiguous']],
            ],
        );

        foreach (array_slice($plan['actions'], 0, 15) as $action) {
            $fields = implode(', ', array_keys($action['changes']));
            $this->line($action['name'].' ('.$action['via'].'): '.$fields);
        }
        foreach (array_slice($plan['warnings'], 0, 15) as $warning) {
            $this->warn($warning);
        }
        if (count($plan['actions']) > 15) {
            $this->line('And '.(count($plan['actions']) - 15).' more companies.');
        }

        foreach (array_slice($plan['skipped'], 0, 20) as $skipped) {
            $this->warn(($skipped['matter_id'] ?: 'no matter').' '.($skipped['business'] ?: 'no name').': '.$skipped['reason']);
        }
        if (count($plan['skipped']) > 20) {
            $this->warn('And '.(count($plan['skipped']) - 20).' more unmatched rows.');
        }

        if (! $this->option('commit')) {
            $this->info('Dry run only. Nothing was written. Pass --commit to fill blank fields on the matched companies.');

            return self::SUCCESS;
        }

        if ($plan['companies_to_update'] === 0) {
            $this->info('Nothing to write.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Fill blank fields on '.$plan['companies_to_update'].' existing companies?', false)) {
            $this->warn('Nothing was written.');

            return self::SUCCESS;
        }

        $backfill->commit($plan);
        $this->info('Blank company details were filled. Existing values were left as they are.');

        return self::SUCCESS;
    }
}
