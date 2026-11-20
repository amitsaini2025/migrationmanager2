<?php

use App\Support\LeadMigrateInviteChannels;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove automatic assignees from open public check-ins.
     * A person already chosen from the named staff list is left in place.
     */
    public function up(): void
    {
        if (! Schema::hasTable('front_desk_check_ins') || ! Schema::hasTable('checkin_logs') || ! Schema::hasTable('staff')) {
            return;
        }

        $allowedIds = LeadMigrateInviteChannels::resolveMigrateAssigneeStaff()
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        if ($allowedIds === []) {
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
            ->whereIn('status', [0, 2])
            ->whereNotNull('user_id')
            ->whereNotIn('user_id', $allowedIds)
            ->update(['user_id' => null]);
    }

    public function down(): void
    {
        // Previous automatic assignees are not restored.
    }
};
