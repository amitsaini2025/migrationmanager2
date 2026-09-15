<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_day_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('summary_date');
            $table->text('body');
            $table->string('source', 16)->default('copy');
            $table->timestamp('saved_at')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'summary_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_day_summaries');
    }
};
