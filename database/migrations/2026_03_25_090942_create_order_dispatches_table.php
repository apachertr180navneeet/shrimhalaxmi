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
        Schema::create('order_dispatches', function (Blueprint $table) {
            $table->id();
            $table->date('dispatch_date');
            $table->string('dispatch_no', 50)->unique();
            $table->string('bill_no', 50)->nullable();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('mobile_number', 25)->nullable();
            $table->string('transport', 150)->nullable();
            $table->enum('status', ['Pending', 'In Transit', 'Complete', 'Cancelled'])->default('Pending');
            $table->decimal('total_meter', 12, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_dispatches');
    }
};
