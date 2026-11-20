<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public visitors have no staff login. Staff check-ins still store admin_id.
     */
    public function up(): void
    {
        Schema::table('front_desk_check_ins', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('front_desk_check_ins', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable(false)->change();
        });
    }
};
