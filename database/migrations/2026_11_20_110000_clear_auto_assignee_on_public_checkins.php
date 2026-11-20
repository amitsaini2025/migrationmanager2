<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public check-ins should wait for a manual assignee. Clears only waiting rows.
     */
    public function up(): void
    {
        if (! Schema::hasTable('front_desk_check_ins') || ! Schema::hasTable('checkin_logs')) {
            return;
        }

        $ids = [];
        foreach (DB::table('front_desk_check_ins')->whereNotNull('metadata')->get(['metadata']) as $row) {
            $meta = json_decode((string) $row->metadata, true);
            if (! is_array($meta) || ($meta['source'] ?? null) !== 'public') {
                continue;
            }

            $id = (int) ($meta['checkin_log_id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        if ($ids === []) {
            return;
        }

        DB::table('checkin_logs')
            ->whereIn('id', $ids)
            ->where('status', 0)
            ->update(['user_id' => null]);
    }

    public function down(): void
    {
        // Previous automatic assignees are not restored.
    }
};
