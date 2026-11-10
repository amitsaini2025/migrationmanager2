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
        if (Schema::hasColumn('staff', 'bulk_download_enabled')) {
            return;
        }

        Schema::table('staff', function (Blueprint $table) {
            $table->boolean('bulk_download_enabled')->default(false);
        });

        // Keep current staff able to bulk download when their role already allows it.
        DB::table('staff')->update(['bulk_download_enabled' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('staff', 'bulk_download_enabled')) {
            return;
        }

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('bulk_download_enabled');
        });
    }
};
