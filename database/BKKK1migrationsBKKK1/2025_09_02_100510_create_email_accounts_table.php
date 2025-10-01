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
        if (!Schema::connection('second_db')->hasTable('email_accounts')) {
            Schema::connection('second_db')->create('email_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('email_user_id')->constrained('email_users')->onDelete('cascade');
                $table->string('provider'); // 'zoho'
                $table->string('email');
                $table->string('access_token');
                $table->string('refresh_token')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('second_db')->dropIfExists('email_accounts');
    }
};
