<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_worker_inward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_worker_inward_id')->constrained('job_worker_inwards')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('lot_no', 100);
            $table->string('quality', 150)->nullable();
            $table->decimal('meter', 12, 2)->default(0);
            $table->decimal('fold', 12, 2)->default(0);
            $table->decimal('total_meter', 12, 2)->default(0);
            $table->string('shrinkage', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_worker_inward_items');
    }
};
