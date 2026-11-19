<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_migrate_invites', function (Blueprint $table) {
            $table->id();
            $table->string('token_hash', 64)->unique();
            $table->string('channel', 16)->index();
            $table->string('sent_to_email', 255)->nullable()->index();
            $table->string('sent_to_phone', 32)->nullable()->index();
            $table->string('sent_to_phone_e164', 32)->nullable();
            $table->unsignedInteger('sent_by')->nullable()->index();
            $table->unsignedInteger('lead_id')->nullable()->index();
            $table->string('source', 64)->index();
            $table->json('submitted_payload')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('used_at')->nullable()->index();
            $table->timestamp('invalidated_at')->nullable()->index();
            $table->timestamp('submitted_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['channel', 'used_at', 'invalidated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_migrate_invites');
    }
};
