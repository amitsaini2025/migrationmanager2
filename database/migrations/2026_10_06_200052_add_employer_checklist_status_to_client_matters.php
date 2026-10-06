<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Checklist status for the Employers / Sponsors sheet, and sheet menu access
     * for staff who already have the employer-sponsored visa sheet.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('client_matters', 'employer_checklist_status')) {
            Schema::table('client_matters', function (Blueprint $table) {
                $table->string('employer_checklist_status', 32)->nullable()
                    ->comment('Employers / Sponsors sheet checklist status: active, hold, convert_to_client, discontinue');
            });
        }

        if (! Schema::hasTable('staff') || ! Schema::hasColumn('staff', 'sheet_access')) {
            return;
        }

        $staffRows = DB::table('staff')
            ->whereNotNull('sheet_access')
            ->where('sheet_access', '!=', '')
            ->get(['id', 'sheet_access']);

        foreach ($staffRows as $row) {
            $list = json_decode((string) $row->sheet_access, true);
            if (! is_array($list) || ! in_array('employer-sponsored', $list, true) || in_array('employer', $list, true)) {
                continue;
            }

            $list[] = 'employer';
            DB::table('staff')->where('id', $row->id)->update([
                'sheet_access' => json_encode(array_values($list)),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('staff') && Schema::hasColumn('staff', 'sheet_access')) {
            $staffRows = DB::table('staff')
                ->whereNotNull('sheet_access')
                ->where('sheet_access', '!=', '')
                ->get(['id', 'sheet_access']);

            foreach ($staffRows as $row) {
                $list = json_decode((string) $row->sheet_access, true);
                if (! is_array($list) || ! in_array('employer', $list, true)) {
                    continue;
                }

                $list = array_values(array_filter($list, static fn ($key) => $key !== 'employer'));
                DB::table('staff')->where('id', $row->id)->update([
                    'sheet_access' => json_encode($list),
                ]);
            }
        }

        if (Schema::hasColumn('client_matters', 'employer_checklist_status')) {
            Schema::table('client_matters', function (Blueprint $table) {
                $table->dropColumn('employer_checklist_status');
            });
        }
    }
};
