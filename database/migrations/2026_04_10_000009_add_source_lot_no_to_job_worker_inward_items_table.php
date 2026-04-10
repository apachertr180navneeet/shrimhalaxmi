<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('job_worker_inward_items', 'source_lot_no')) {
            Schema::table('job_worker_inward_items', function (Blueprint $table) {
                $table->string('source_lot_no', 100)->nullable()->after('lot_no');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_worker_inward_items', 'source_lot_no')) {
            Schema::table('job_worker_inward_items', function (Blueprint $table) {
                $table->dropColumn('source_lot_no');
            });
        }
    }
};
