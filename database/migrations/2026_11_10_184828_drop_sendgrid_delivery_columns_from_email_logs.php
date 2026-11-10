<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop unused provider message ids left from the old outbound webhook.
     */
    public function up(): void
    {
        if (Schema::hasColumn('email_logs', 'sendgrid_message_id')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->dropColumn('sendgrid_message_id');
            });
        }

        if (Schema::hasColumn('email_log_events', 'sendgrid_event_id')) {
            Schema::table('email_log_events', function (Blueprint $table) {
                $table->dropUnique('email_log_events_sg_event_id_unique');
            });

            Schema::table('email_log_events', function (Blueprint $table) {
                $table->dropColumn('sendgrid_event_id');
            });
        }
    }

    /**
     * Restore the provider id columns.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('email_logs', 'sendgrid_message_id')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->string('sendgrid_message_id', 255)->nullable()->after('message_id');
            });
        }

        if (! Schema::hasColumn('email_log_events', 'sendgrid_event_id')) {
            Schema::table('email_log_events', function (Blueprint $table) {
                $table->string('sendgrid_event_id', 64)->nullable()->unique('email_log_events_sg_event_id_unique');
            });
        }
    }
};
