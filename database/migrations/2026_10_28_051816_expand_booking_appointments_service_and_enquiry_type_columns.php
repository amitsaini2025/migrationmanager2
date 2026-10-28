<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Website display labels (especially service_type) can exceed the original
     * varchar(100) limit and break Bansal webhook inserts on PostgreSQL.
     */
    private const NEW_LENGTH = 255;

    private const PREVIOUS_LENGTH = 100;

    /**
     * @var list<string>
     */
    private const COLUMNS = ['service_type', 'enquiry_type'];

    public function up(): void
    {
        if (! Schema::hasTable('booking_appointments')) {
            return;
        }

        foreach (self::COLUMNS as $column) {
            $this->widenColumn($column);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('booking_appointments')) {
            return;
        }

        foreach (self::COLUMNS as $column) {
            $this->narrowColumn($column);
        }
    }

    private function widenColumn(string $column): void
    {
        if (! Schema::hasColumn('booking_appointments', $column)) {
            return;
        }

        $driver = DB::getDriverName();

        // SQLite does not enforce varchar length; skip to avoid change() / DBAL needs in tests.
        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE booking_appointments ALTER COLUMN '.$column.' TYPE VARCHAR('.self::NEW_LENGTH.') USING '.$column.'::VARCHAR('.self::NEW_LENGTH.')'
            );

            return;
        }

        if ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE booking_appointments MODIFY '.$column.' VARCHAR('.self::NEW_LENGTH.') NULL'
            );

            return;
        }

        Schema::table('booking_appointments', function (Blueprint $table) use ($column) {
            $table->string($column, self::NEW_LENGTH)->nullable()->change();
        });
    }

    private function narrowColumn(string $column): void
    {
        if (! Schema::hasColumn('booking_appointments', $column)) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        $hasLongValue = DB::table('booking_appointments')
            ->whereNotNull($column)
            ->whereRaw('LENGTH('.$column.') > ?', [self::PREVIOUS_LENGTH])
            ->exists();

        if ($hasLongValue) {
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE booking_appointments ALTER COLUMN '.$column.' TYPE VARCHAR('.self::PREVIOUS_LENGTH.') USING '.$column.'::VARCHAR('.self::PREVIOUS_LENGTH.')'
            );

            return;
        }

        if ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE booking_appointments MODIFY '.$column.' VARCHAR('.self::PREVIOUS_LENGTH.') NULL'
            );

            return;
        }

        Schema::table('booking_appointments', function (Blueprint $table) use ($column) {
            $table->string($column, self::PREVIOUS_LENGTH)->nullable()->change();
        });
    }
};
