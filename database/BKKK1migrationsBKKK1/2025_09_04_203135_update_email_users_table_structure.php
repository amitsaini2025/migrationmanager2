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
        Schema::connection('second_db')->table('email_users', function (Blueprint $table) {
            // Add user_type column if it doesn't exist
            if (!Schema::connection('second_db')->hasColumn('email_users', 'user_type')) {
                $table->integer('user_type')->nullable()->comment('1=>Admin_users,2=>Email_users')->after('id');
            }
            
            // Add decrypt_password column if it doesn't exist
            if (!Schema::connection('second_db')->hasColumn('email_users', 'decrypt_password')) {
                $table->string('decrypt_password', 255)->nullable()->after('password');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::connection('second_db')->hasColumn('email_users', 'status')) {
                $table->string('status', 50)->default('1')->comment("'0'=>'Disable', '1'=>'Enable'")->after('remember_token');
            }
            
            // Add first_name column if it doesn't exist
            if (!Schema::connection('second_db')->hasColumn('email_users', 'first_name')) {
                $table->string('first_name', 255)->nullable()->after('status');
            }
            
            // Add last_name column if it doesn't exist
            if (!Schema::connection('second_db')->hasColumn('email_users', 'last_name')) {
                $table->string('last_name', 255)->nullable()->after('first_name');
            }
            
            // Add phone column if it doesn't exist
            if (!Schema::connection('second_db')->hasColumn('email_users', 'phone')) {
                $table->string('phone', 40)->nullable()->after('last_name');
            }
            
            // Remove name column if it exists (since we're using first_name and last_name)
            if (Schema::connection('second_db')->hasColumn('email_users', 'name')) {
                $table->dropColumn('name');
            }
            
            // Remove email_verified_at column if it exists
            if (Schema::connection('second_db')->hasColumn('email_users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('second_db')->table('email_users', function (Blueprint $table) {
            // Remove added columns
            if (Schema::connection('second_db')->hasColumn('email_users', 'user_type')) {
                $table->dropColumn('user_type');
            }
            if (Schema::connection('second_db')->hasColumn('email_users', 'decrypt_password')) {
                $table->dropColumn('decrypt_password');
            }
            if (Schema::connection('second_db')->hasColumn('email_users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::connection('second_db')->hasColumn('email_users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::connection('second_db')->hasColumn('email_users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::connection('second_db')->hasColumn('email_users', 'phone')) {
                $table->dropColumn('phone');
            }
            
            // Restore original columns
            if (!Schema::connection('second_db')->hasColumn('email_users', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::connection('second_db')->hasColumn('email_users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
        });
    }
};
