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
        Schema::table('documents', function (Blueprint $table) {
            // Ownership & tracking
            $table->unsignedInteger('created_by')->nullable()->after('id');
            $table->string('origin', 20)->default('ad_hoc')->after('created_by'); // ad_hoc|client|lead
            
            // Polymorphic association (nullable for ad-hoc)
            $table->string('documentable_type')->nullable()->after('origin');
            $table->unsignedInteger('documentable_id')->nullable()->after('documentable_type');
            $table->index(['documentable_type', 'documentable_id']);
            
            // Metadata for discoverability
            $table->string('title')->nullable()->after('documentable_id');
            $table->string('document_type', 50)->default('general')->after('title'); // agreement|nda|general|etc
            $table->json('labels')->nullable()->after('document_type');
            $table->timestamp('due_at')->nullable()->after('labels');
            $table->string('priority', 10)->default('normal')->after('due_at'); // low|normal|high
            
            // Activity tracking
            $table->string('primary_signer_email')->nullable()->after('priority');
            $table->unsignedTinyInteger('signer_count')->default(1)->after('primary_signer_email');
            $table->timestamp('last_activity_at')->nullable()->after('signer_count');
            
            // Lifecycle
            $table->timestamp('archived_at')->nullable()->after('last_activity_at');
            
            // Foreign key
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['documentable_type', 'documentable_id']);
            $table->dropColumn([
                'created_by',
                'origin',
                'documentable_type',
                'documentable_id',
                'title',
                'document_type',
                'labels',
                'due_at',
                'priority',
                'primary_signer_email',
                'signer_count',
                'last_activity_at',
                'archived_at'
            ]);
        });
    }
};
