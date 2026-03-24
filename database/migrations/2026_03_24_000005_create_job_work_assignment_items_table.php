<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_work_assignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_work_assignment_id')->constrained('job_work_assignments')->cascadeOnDelete();
            $table->foreignId('purchase_item_id')->nullable()->constrained('purchase_items')->nullOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(1);
            $table->string('lot_no', 100);
            $table->string('quality', 150)->nullable();
            $table->decimal('meter', 12, 2)->default(0);
            $table->decimal('fold', 12, 2)->default(0);
            $table->decimal('net_meter', 12, 2)->default(0);
            $table->string('process', 100);
            $table->string('lr_no', 100)->nullable();
            $table->string('transport', 150)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_work_assignment_items');
    }
};
