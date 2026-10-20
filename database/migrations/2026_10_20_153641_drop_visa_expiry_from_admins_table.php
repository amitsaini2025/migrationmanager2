<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Visa expiry now lives only on client_visa_countries.visa_expiry_date.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('admins', 'visaExpiry')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('visaExpiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('admins', 'visaExpiry')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->date('visaExpiry')->nullable();
        });
    }
};
