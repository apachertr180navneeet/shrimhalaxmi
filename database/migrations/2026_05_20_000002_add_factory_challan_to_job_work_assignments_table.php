<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_work_assignments', function (Blueprint $table) {
            $table->string('factory_challan', 100)->nullable()->after('freight');
        });
    }

    public function down(): void
    {
        Schema::table('job_work_assignments', function (Blueprint $table) {
            $table->dropColumn('factory_challan');
        });
    }
};
