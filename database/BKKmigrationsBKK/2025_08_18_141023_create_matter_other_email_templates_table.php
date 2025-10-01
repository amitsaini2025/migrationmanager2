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
        Schema::create('matter_other_email_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matter_id');
            $table->string('name');
            $table->string('subject');
            $table->longText('description');
            $table->timestamps();
            
            $table->foreign('matter_id')->references('id')->on('matters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matter_other_email_templates');
    }
};
