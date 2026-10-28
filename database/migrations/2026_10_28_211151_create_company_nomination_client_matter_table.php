<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('company_nominations') || ! Schema::hasTable('client_matters')) {
            return;
        }

        if (! Schema::hasTable('company_nomination_client_matter')) {
            Schema::create('company_nomination_client_matter', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_nomination_id')
                    ->constrained('company_nominations')
                    ->cascadeOnDelete();
                $table->foreignId('client_matter_id')
                    ->constrained('client_matters')
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(
                    ['company_nomination_id', 'client_matter_id'],
                    'company_nomination_client_matter_unique'
                );
            });
        }

        if (! Schema::hasColumn('company_nominations', 'client_matter_id')) {
            return;
        }

        $now = now();

        DB::table('company_nominations')
            ->whereNotNull('client_matter_id')
            ->orderBy('id')
            ->select(['id', 'client_matter_id'])
            ->chunkById(200, function ($rows) use ($now): void {
                $payload = [];
                foreach ($rows as $row) {
                    $payload[] = [
                        'company_nomination_id' => (int) $row->id,
                        'client_matter_id' => (int) $row->client_matter_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($payload === []) {
                    return;
                }

                DB::table('company_nomination_client_matter')->insertOrIgnore($payload);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_nomination_client_matter');
    }
};
