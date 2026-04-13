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
        Schema::table('order_dispatch_items', function (Blueprint $table) {
            $table->decimal('gst', 14, 2)->default(0)->after('amount');
            $table->decimal('total_amount', 14, 2)->default(0)->after('gst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_dispatch_items', function (Blueprint $table) {
            $table->dropColumn(['gst', 'total_amount']);
        });
    }
};

