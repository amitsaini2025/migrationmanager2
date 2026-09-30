<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Auto file-time sessions stay on the dashboard. Remove the activity-feed rows they created.
     */
    public function up(): void
    {
        if (! Schema::hasTable('activities_logs') || ! Schema::hasTable('staff_matter_sessions')) {
            return;
        }

        $protected = collect();
        if (Schema::hasTable('staff_file_time_entries')) {
            $protected = DB::table('staff_file_time_entries')
                ->whereNotNull('activities_log_id')
                ->pluck('activities_log_id');
        }

        $linked = DB::table('staff_matter_sessions')
            ->whereNotNull('activities_log_id')
            ->pluck('activities_log_id');

        $orphans = DB::table('activities_logs')
            ->where('activity_type', 'file_time')
            ->where(function ($query): void {
                $query->where('subject', 'like', '% · % activities')
                    ->orWhere('subject', 'like', '% · reviewed file');
            })
            ->pluck('id');

        $ids = $linked->merge($orphans)->unique()->diff($protected)->values();

        foreach ($ids->chunk(500) as $chunk) {
            $chunkIds = $chunk->all();
            DB::table('staff_matter_sessions')
                ->whereIn('activities_log_id', $chunkIds)
                ->update(['activities_log_id' => null]);
            DB::table('activities_logs')->whereIn('id', $chunkIds)->delete();
        }
    }

    /**
     * Removed feed rows are not restored. The sessions remain on the dashboard.
     */
    public function down(): void
    {
        //
    }
};
