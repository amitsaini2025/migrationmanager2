<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STATUSES = [
        'draft',
        'submitted',
        'invited',
        'nominated',
        'rejected',
        'withdrawn',
        'expired',
    ];

    private const PREVIOUS_STATUSES = [
        'draft',
        'submitted',
        'invited',
        'nominated',
        'rejected',
        'withdrawn',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_eoi_references') || ! Schema::hasColumn('client_eoi_references', 'eoi_status')) {
            return;
        }

        $this->replaceEoiStatusConstraint(self::STATUSES);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('client_eoi_references') || ! Schema::hasColumn('client_eoi_references', 'eoi_status')) {
            return;
        }

        DB::table('client_eoi_references')
            ->where('eoi_status', 'expired')
            ->update(['eoi_status' => 'submitted']);

        $this->replaceEoiStatusConstraint(self::PREVIOUS_STATUSES);
    }

    /**
     * @param  list<string>  $statuses
     */
    private function replaceEoiStatusConstraint(array $statuses): void
    {
        $inList = collect($statuses)
            ->map(fn (string $status): string => "'".$status."'")
            ->implode(', ');

        if (DB::getDriverName() === 'pgsql') {
            $constraints = DB::select("
                SELECT conname as constraint_name
                FROM pg_constraint
                WHERE conrelid = 'client_eoi_references'::regclass
                AND contype = 'c'
                AND pg_get_constraintdef(oid) LIKE '%eoi_status%'
            ");

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE client_eoi_references DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
            }

            DB::statement("ALTER TABLE client_eoi_references ADD CONSTRAINT client_eoi_references_eoi_status_check CHECK (eoi_status IN ({$inList}))");

            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE client_eoi_references MODIFY COLUMN eoi_status ENUM({$inList}) NOT NULL DEFAULT 'draft'");
        }
    }
};
