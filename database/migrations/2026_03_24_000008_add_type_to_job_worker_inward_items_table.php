<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('job_worker_inward_items', 'type')) {
            Schema::table('job_worker_inward_items', function (Blueprint $table) {
                $table->string('type', 50)->nullable()->after('shrinkage');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_worker_inward_items', 'type')) {
            Schema::table('job_worker_inward_items', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
