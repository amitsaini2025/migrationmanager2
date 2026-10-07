<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Staff member who starred a sheet row as verified.
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_matter_references') || Schema::hasColumn('client_matter_references', 'verified_by')) {
            return;
        }

        Schema::table('client_matter_references', function (Blueprint $table) {
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('client_matter_references') || ! Schema::hasColumn('client_matter_references', 'verified_by')) {
            return;
        }

        Schema::table('client_matter_references', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
        });
    }
};
