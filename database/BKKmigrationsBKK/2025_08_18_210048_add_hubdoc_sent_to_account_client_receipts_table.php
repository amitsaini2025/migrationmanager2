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
        Schema::table('account_client_receipts', function (Blueprint $table) {
            $table->boolean('hubdoc_sent')->default(false)->after('invoice_status');
            $table->timestamp('hubdoc_sent_at')->nullable()->after('hubdoc_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_client_receipts', function (Blueprint $table) {
            $table->dropColumn(['hubdoc_sent', 'hubdoc_sent_at']);
        });
    }
};
