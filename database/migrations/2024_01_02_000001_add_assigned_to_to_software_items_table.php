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
        if (Schema::hasTable('software_items') && !Schema::hasColumn('software_items', 'assigned_to')) {
            Schema::table('software_items', function (Blueprint $table) {
                $table->string('assigned_to')->nullable()->after('installed_on');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('software_items') && Schema::hasColumn('software_items', 'assigned_to')) {
            Schema::table('software_items', function (Blueprint $table) {
                $table->dropColumn('assigned_to');
            });
        }
    }
};
