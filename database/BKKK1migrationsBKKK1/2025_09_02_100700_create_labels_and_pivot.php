<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::connection('second_db')->hasTable('labels')) {
            Schema::connection('second_db')->create('labels', function (Blueprint $table) {
                $table->id();
                $table->foreignId('email_user_id')->constrained('email_users')->cascadeOnDelete();
                $table->string('name');
                $table->string('color')->default('#3B82F6');
                $table->string('type')->default('custom');
                $table->string('icon')->nullable();
                $table->string('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['email_user_id', 'name']);
            });
        }

        if (!Schema::connection('second_db')->hasTable('email_label')) {
            Schema::connection('second_db')->create('email_label', function (Blueprint $table) {
                $table->foreignId('email_id')->constrained('emails')->cascadeOnDelete();
                $table->foreignId('label_id')->constrained('labels')->cascadeOnDelete();
                $table->primary(['email_id', 'label_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::connection('second_db')->dropIfExists('email_label');
        Schema::connection('second_db')->dropIfExists('labels');
    }
};


