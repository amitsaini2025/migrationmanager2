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
        Schema::table('signers', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('cancelled_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signers', function (Blueprint $table) {
            $table->dropIndex(['sent_at']);
            $table->dropColumn('sent_at');
        });
    }
};
