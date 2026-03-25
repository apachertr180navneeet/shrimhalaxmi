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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('stock_qty_m', 12, 2)->default(0)->after('status');
            $table->decimal('stock_net_meter', 12, 2)->default(0)->after('stock_qty_m');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['stock_qty_m', 'stock_net_meter']);
        });
    }
};
