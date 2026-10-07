<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair schema drift: recreate client_art_references when migration history
     * exists but the table was dropped or never persisted. Backfills rows from
     * client_matter_references (type = art-matters) without touching other tables.
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_art_references')) {
            Schema::create('client_art_references', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->index();
                $table->unsignedBigInteger('client_matter_id')->index();

                $table->date('submission_last_date')->nullable();
                $table->string('status_of_file', 50)->default('submission_pending');
                $table->string('hearing_time')->nullable();
                $table->string('member_name')->nullable();
                $table->string('outcome')->nullable();
                $table->text('comments')->nullable();
                $table->boolean('is_pinned')->default(false)->index();

                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('client_id')->references('id')->on('admins')->onDelete('cascade');
                $table->foreign('client_matter_id')->references('id')->on('client_matters')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
                $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');

                $table->index(['client_id', 'status_of_file'], 'idx_art_client_status');
                $table->index('submission_last_date', 'idx_art_submission_date');
                $table->index('status_of_file', 'idx_art_status');
            });
        }

        $this->backfillFromClientMatterReferences();
    }

    /**
     * Intentionally irreversible: rolling back would remove the repaired table.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_art_references');
    }

    private function backfillFromClientMatterReferences(): void
    {
        if (! Schema::hasTable('client_art_references') || ! Schema::hasTable('client_matter_references')) {
            return;
        }

        $rows = DB::table('client_matter_references')
            ->where('type', 'art-matters')
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            $exists = DB::table('client_art_references')
                ->where('client_id', $row->client_id)
                ->where('client_matter_id', $row->client_matter_id)
                ->exists();

            if ($exists) {
                continue;
            }

            if (! DB::table('admins')->where('id', $row->client_id)->exists()) {
                continue;
            }

            if (! DB::table('client_matters')->where('id', $row->client_matter_id)->exists()) {
                continue;
            }

            DB::table('client_art_references')->insert([
                'client_id' => $row->client_id,
                'client_matter_id' => $row->client_matter_id,
                'submission_last_date' => $row->checklist_sent_at,
                'status_of_file' => $row->current_status ?? 'submission_pending',
                'hearing_time' => null,
                'member_name' => null,
                'outcome' => null,
                'comments' => $row->comments,
                'is_pinned' => (bool) ($row->is_pinned ?? false),
                'created_by' => $this->resolveAdminForeignKey($row->created_by),
                'updated_by' => $this->resolveAdminForeignKey($row->updated_by),
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now(),
            ]);
        }
    }

    private function resolveAdminForeignKey(mixed $id): ?int
    {
        if ($id === null || $id === '') {
            return null;
        }

        $id = (int) $id;

        return DB::table('admins')->where('id', $id)->exists() ? $id : null;
    }
};
