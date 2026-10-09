<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Staff who can open the Employers / Sponsors sheet can open Labour Market Testing.
     */
    public function up(): void
    {
        if (! Schema::hasTable('staff') || ! Schema::hasColumn('staff', 'sheet_access')) {
            return;
        }

        $staffRows = DB::table('staff')
            ->whereNotNull('sheet_access')
            ->where('sheet_access', '!=', '')
            ->get(['id', 'sheet_access']);

        foreach ($staffRows as $row) {
            $list = json_decode((string) $row->sheet_access, true);
            if (! is_array($list) || ! in_array('employer', $list, true) || in_array('lmt', $list, true)) {
                continue;
            }

            $list[] = 'lmt';
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
        if (! Schema::hasTable('staff') || ! Schema::hasColumn('staff', 'sheet_access')) {
            return;
        }

        $staffRows = DB::table('staff')
            ->whereNotNull('sheet_access')
            ->where('sheet_access', '!=', '')
            ->get(['id', 'sheet_access']);

        foreach ($staffRows as $row) {
            $list = json_decode((string) $row->sheet_access, true);
            if (! is_array($list) || ! in_array('lmt', $list, true)) {
                continue;
            }

            $list = array_values(array_filter($list, static fn ($key) => $key !== 'lmt'));
            DB::table('staff')->where('id', $row->id)->update([
                'sheet_access' => json_encode($list),
            ]);
        }
    }
};
