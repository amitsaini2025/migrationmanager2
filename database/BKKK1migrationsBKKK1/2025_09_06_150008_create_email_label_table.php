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
        if (!Schema::connection('second_db')->hasTable('email_label')) {
            Schema::connection('second_db')->create('email_label', function (Blueprint $table) {
                $table->foreignId('email_id')->constrained('emails')->cascadeOnDelete();
                $table->foreignId('label_id')->constrained('labels')->cascadeOnDelete();
                $table->primary(['email_id', 'label_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('second_db')->dropIfExists('email_label');
    }
};
