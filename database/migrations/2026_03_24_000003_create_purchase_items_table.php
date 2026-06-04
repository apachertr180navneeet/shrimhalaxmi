<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('item_code', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->string('lot_no', 100)->nullable();
            $table->string('lot_roll_no', 50)->nullable();
            $table->string('quality', 150)->nullable();
            $table->decimal('qty_m', 12, 2)->default(0);
            $table->decimal('fold', 12, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->string('transport', 150)->nullable();
            $table->string('lr_no', 100)->nullable();
            $table->decimal('net_meter', 12, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
