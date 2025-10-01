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
        // Add S3 path columns to emails table if they don't exist
        if (!Schema::connection('second_db')->hasColumn('emails', 's3_email_path')) {
            Schema::connection('second_db')->table('emails', function (Blueprint $table) {
                $table->string('s3_email_path')->nullable()->after('file_path');
            });
        }
        
        if (!Schema::connection('second_db')->hasColumn('emails', 's3_attachment_paths')) {
            Schema::connection('second_db')->table('emails', function (Blueprint $table) {
                $table->string('s3_attachment_paths')->nullable()->after('s3_email_path');
            });
        }

        // Add S3 path columns to email_attachments table if they don't exist
        if (!Schema::connection('second_db')->hasColumn('email_attachments', 's3_file_path')) {
            Schema::connection('second_db')->table('email_attachments', function (Blueprint $table) {
                $table->string('s3_file_path')->nullable()->after('file_path');
            });
        }
        
        if (!Schema::connection('second_db')->hasColumn('email_attachments', 's3_url')) {
            Schema::connection('second_db')->table('email_attachments', function (Blueprint $table) {
                $table->string('s3_url')->nullable()->after('s3_file_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove S3 path columns from emails table
        if (Schema::connection('second_db')->hasColumn('emails', 's3_email_path')) {
            Schema::connection('second_db')->table('emails', function (Blueprint $table) {
                $table->dropColumn('s3_email_path');
            });
        }
        
        if (Schema::connection('second_db')->hasColumn('emails', 's3_attachment_paths')) {
            Schema::connection('second_db')->table('emails', function (Blueprint $table) {
                $table->dropColumn('s3_attachment_paths');
            });
        }

        // Remove S3 path columns from email_attachments table
        if (Schema::connection('second_db')->hasColumn('email_attachments', 's3_file_path')) {
            Schema::connection('second_db')->table('email_attachments', function (Blueprint $table) {
                $table->dropColumn('s3_file_path');
            });
        }
        
        if (Schema::connection('second_db')->hasColumn('email_attachments', 's3_url')) {
            Schema::connection('second_db')->table('email_attachments', function (Blueprint $table) {
                $table->dropColumn('s3_url');
            });
        }
    }
};
