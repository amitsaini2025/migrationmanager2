<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        Schema::table('client_matters', function (Blueprint $table) {
            if (! Schema::hasColumn('client_matters', 'lmt_use_advertisements')) {
                $table->boolean('lmt_use_advertisements')->nullable()->after('lmt_password');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad1_publication')) {
                $table->string('lmt_ad1_publication', 255)->nullable()->after('lmt_use_advertisements');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad1_opened_on')) {
                $table->date('lmt_ad1_opened_on')->nullable()->after('lmt_ad1_publication');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad1_closed_on')) {
                $table->date('lmt_ad1_closed_on')->nullable()->after('lmt_ad1_opened_on');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad1_document_id')) {
                $table->unsignedBigInteger('lmt_ad1_document_id')->nullable()->after('lmt_ad1_closed_on');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad2_publication')) {
                $table->string('lmt_ad2_publication', 255)->nullable()->after('lmt_ad1_document_id');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad2_opened_on')) {
                $table->date('lmt_ad2_opened_on')->nullable()->after('lmt_ad2_publication');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad2_closed_on')) {
                $table->date('lmt_ad2_closed_on')->nullable()->after('lmt_ad2_opened_on');
            }
            if (! Schema::hasColumn('client_matters', 'lmt_ad2_document_id')) {
                $table->unsignedBigInteger('lmt_ad2_document_id')->nullable()->after('lmt_ad2_closed_on');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        Schema::table('client_matters', function (Blueprint $table) {
            $columns = [
                'lmt_use_advertisements',
                'lmt_ad1_publication',
                'lmt_ad1_opened_on',
                'lmt_ad1_closed_on',
                'lmt_ad1_document_id',
                'lmt_ad2_publication',
                'lmt_ad2_opened_on',
                'lmt_ad2_closed_on',
                'lmt_ad2_document_id',
            ];
            $present = array_values(array_filter(
                $columns,
                fn (string $column) => Schema::hasColumn('client_matters', $column)
            ));
            if ($present !== []) {
                $table->dropColumn($present);
            }
        });
    }
};
