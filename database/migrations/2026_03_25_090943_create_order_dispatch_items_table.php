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
        Schema::create('order_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_dispatch_id')->constrained('order_dispatches')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('lot_no', 100)->nullable();
            $table->string('item_code', 100)->nullable();
            $table->decimal('meter', 12, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->integer('sort_order')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_dispatch_items');
    }
};
