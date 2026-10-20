<?php

namespace App\Console\Commands;

use App\Models\Staff;
use App\Services\EmployerSpreadsheetImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ImportEmployerSpreadsheets extends Command
{
    protected $signature = 'crm:import-employer-spreadsheets
                            {--path= : Folder containing sponsorship.xlsx, 186 LIST.xlsx, and 482 VIUST.xlsx}
                            {--commit : Write records. Omit this to print a dry run}
                            {--force : Skip the confirmation prompt when using --commit}
                            {--186-stream=direct-entry : 186 migrant stream: direct-entry, trt, or agreement}
                            {--staff-arun= : Staff id for ARUN BANSAL}
                            {--staff-vipul= : Staff id for Vipul Goyal}
                            {--staff-yadwinder= : Staff id for Yadwinder}
                            {--fallback-staff= : Staff id used when a row has no responsible name}';

    protected $description = 'Import new employer-sponsor companies and their 186/482 matters from the three spreadsheets. Dry run unless --commit is passed.';

    public function handle(EmployerSpreadsheetImportService $importer): int
    {
        $stream = strtolower((string) $this->option('186-stream'));
        if (! in_array($stream, ['direct-entry', 'trt', 'agreement'], true)) {
            $this->error('--186-stream must be direct-entry, trt, or agreement.');

            return self::FAILURE;
        }

        $directory = (string) ($this->option('path') ?: public_path());
        if (! is_dir($directory)) {
            $this->error('Folder not found: '.$directory);

            return self::FAILURE;
        }

        $staff = $this->staffIds();
        foreach ($staff['errors'] as $error) {
            $this->error($error);
        }
        if ($staff['errors'] !== []) {
            return self::FAILURE;
        }
        foreach ($staff['missing'] as $missing) {
            $this->warn($missing);
        }

        $fallback = $this->option('fallback-staff') !== null ? (int) $this->option('fallback-staff') : null;
        if ($fallback !== null && ! Staff::query()->whereKey($fallback)->exists()) {
            $this->error('--fallback-staff is not a staff id.');

            return self::FAILURE;
        }

        $this->line('186 migrant matters will use stream: '.$stream.'. The spreadsheet does not say Direct Entry, TRT, or Agreement.');
        $plan = $importer->plan($directory, $staff['ids'], $fallback, $stream);

        $this->table(
            ['Action', 'Count'],
            [
                ['Companies to create', $plan['companies_to_create']],
                ['Companies rejected, already in CRM', $plan['companies_rejected']],
                ['People to create', $plan['clients_to_create']],
                ['People rejected, already in CRM', $plan['clients_rejected']],
                ['Matters to create', $plan['matters_to_create']],
                ['Matters rejected, already in CRM', $plan['matters_rejected']],
                ['Matters skipped, no CRM type', $plan['matters_skipped']],
            ],
        );

        foreach (array_slice($plan['warnings'], 0, 40) as $warning) {
            $this->warn($warning);
        }
        if (count($plan['warnings']) > 40) {
            $this->warn('And '.(count($plan['warnings']) - 40).' more warnings.');
        }
        foreach ($plan['blocking'] as $blocking) {
            $this->error($blocking);
        }

        if (! $this->option('commit')) {
            $this->info('Dry run only. Nothing was written. Pass --commit to import.');

            return $plan['blocking'] === [] ? self::SUCCESS : self::FAILURE;
        }

        if ($plan['blocking'] !== []) {
            $this->error('Nothing was written. Resolve the errors above first.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Write these records to the database?', false)) {
            $this->warn('Nothing was written.');

            return self::SUCCESS;
        }

        $importer->commit($plan);
        $this->info('Import committed.');

        return self::SUCCESS;
    }

    /**
     * @return array{ids: array<string, int>, errors: list<string>, missing: list<string>}
     */
    private function staffIds(): array
    {
        $ids = [];
        $errors = [];
        $missing = [];

        $ids['arun bansal'] = $this->oneStaff(
            $this->option('staff-arun'),
            fn () => Staff::query()->whereRaw('LOWER(first_name) = ?', ['arun'])->whereRaw('LOWER(last_name) = ?', ['bansal'])->get(),
            'ARUN BANSAL',
            $errors,
            $missing,
        );
        $ids['vipul goyal'] = $this->oneStaff(
            $this->option('staff-vipul'),
            fn () => Staff::query()->whereRaw('LOWER(first_name) = ?', ['vipul'])->where('last_name', 'ilike', 'goyal%')->get(),
            'Vipul Goyal',
            $errors,
            $missing,
        );
        $ids['yadwinder'] = $this->oneStaff(
            $this->option('staff-yadwinder'),
            fn () => Staff::query()->whereRaw('LOWER(first_name) LIKE ?', ['yadwinder%'])->get(),
            'Yadwinder',
            $errors,
            $missing,
        );

        return [
            'ids' => array_filter($ids, fn (?int $id): bool => $id !== null),
            'errors' => $errors,
            'missing' => $missing,
        ];
    }

    /**
     * @param  callable(): Collection<int, Staff>  $lookup
     * @param  list<string>  $errors
     * @param  list<string>  $missing
     */
    private function oneStaff(mixed $option, callable $lookup, string $label, array &$errors, array &$missing): ?int
    {
        if ($option !== null && $option !== '') {
            $id = (int) $option;
            if (! Staff::query()->whereKey($id)->exists()) {
                $errors[] = '--staff id '.$id.' for '.$label.' was not found.';

                return null;
            }

            return $id;
        }

        $matches = $lookup();
        if ($matches->count() === 1) {
            return (int) $matches->first()->id;
        }
        if ($matches->count() > 1) {
            $errors[] = $label.' matches more than one staff login. Pass the staff id explicitly.';

            return null;
        }

        $missing[] = $label.' was not found on the staff list. Pass the staff id, or the import cannot assign that person\'s files.';

        return null;
    }
}
