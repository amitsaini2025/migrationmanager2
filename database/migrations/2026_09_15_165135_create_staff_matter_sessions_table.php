<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_matter_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedInteger('client_matter_id')->nullable();
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('matter_key')->default(0);
            $table->date('session_date');
            $table->string('status', 16)->default('accessed');
            $table->unsignedInteger('focused_seconds')->default(0);
            $table->unsignedInteger('idle_cut_seconds')->default(0);
            $table->unsignedSmallInteger('confirmed_minutes')->nullable();
            $table->unsignedSmallInteger('event_count')->nullable();
            $table->boolean('is_reviewed_only')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('activities_log_id')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->foreign('client_matter_id')->references('id')->on('client_matters')->nullOnDelete();
            $table->foreign('activities_log_id')->references('id')->on('activities_logs')->nullOnDelete();

            $table->unique(['staff_id', 'client_id', 'matter_key', 'session_date'], 'staff_matter_sessions_staff_record_day');
            $table->index(['staff_id', 'session_date']);
            $table->index('last_heartbeat_at');
            $table->unique('activities_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_matter_sessions');
    }
};
