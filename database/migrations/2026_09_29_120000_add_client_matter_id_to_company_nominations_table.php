<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('company_nominations')) {
            return;
        }

        if (Schema::hasColumn('company_nominations', 'client_matter_id')) {
            return;
        }

        Schema::table('company_nominations', function (Blueprint $table) {
            $table->unsignedBigInteger('client_matter_id')
                ->nullable()
                ->after('nominated_person_name')
                ->comment('Optional link to an active client matter for this company client');

            $table->foreign('client_matter_id')
                ->references('id')
                ->on('client_matters')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('company_nominations')
            || ! Schema::hasColumn('company_nominations', 'client_matter_id')) {
            return;
        }

        Schema::table('company_nominations', function (Blueprint $table) {
            $table->dropForeign(['client_matter_id']);
            $table->dropColumn('client_matter_id');
        });
    }
};
