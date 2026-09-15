<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_file_time_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedInteger('client_matter_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->string('kind', 32);
            $table->string('title');
            $table->string('status', 16)->default('doing');
            $table->boolean('is_running')->default(false);
            $table->unsignedInteger('clock_seconds')->default(0);
            $table->unsignedSmallInteger('confirmed_minutes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('activities_log_id')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->foreign('client_matter_id')->references('id')->on('client_matters')->nullOnDelete();
            $table->foreign('activities_log_id')->references('id')->on('activities_logs')->nullOnDelete();

            $table->index(['staff_id', 'created_at']);
            $table->index(['staff_id', 'status']);
            $table->index(['client_matter_id', 'staff_id']);
            $table->unique('activities_log_id');
        });

        // At most one running timer per staff (PostgreSQL + SQLite partial unique).
        DB::statement(
            'CREATE UNIQUE INDEX staff_file_time_entries_one_running_per_staff
             ON staff_file_time_entries (staff_id)
             WHERE is_running = true'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_file_time_entries');
    }
};
