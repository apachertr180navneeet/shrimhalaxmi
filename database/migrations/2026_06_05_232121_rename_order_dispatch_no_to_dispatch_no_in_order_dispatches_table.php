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
        if (Schema::hasColumn('order_dispatches', 'order_dispatch_no')) {
            Schema::table('order_dispatches', function (Blueprint $table) {
                $table->renameColumn('order_dispatch_no', 'dispatch_no');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_dispatches', 'dispatch_no')) {
            Schema::table('order_dispatches', function (Blueprint $table) {
                $table->renameColumn('dispatch_no', 'order_dispatch_no');
            });
        }
    }
};
