<?php

use App\Enums\ChecklistSource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cp_doc_checklists') || Schema::hasColumn('cp_doc_checklists', 'portal_template_name')) {
            return;
        }

        Schema::table('cp_doc_checklists', function (Blueprint $table) {
            $table->string('portal_template_name', 255)->nullable()->after('cp_checklist_name');
        });

        if (! Schema::hasColumn('cp_doc_checklists', 'source')) {
            return;
        }

        DB::table('cp_doc_checklists')
            ->where('source', ChecklistSource::Portal->value)
            ->whereNull('user_id')
            ->whereNull('portal_template_name')
            ->update([
                'portal_template_name' => DB::raw('cp_checklist_name'),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('cp_doc_checklists') || ! Schema::hasColumn('cp_doc_checklists', 'portal_template_name')) {
            return;
        }

        Schema::table('cp_doc_checklists', function (Blueprint $table) {
            $table->dropColumn('portal_template_name');
        });
    }
};
