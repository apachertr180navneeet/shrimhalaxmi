<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('purchase_date')->nullable();
            $table->string('pch_no', 20)->unique();
            $table->string('bno', 50)->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('vendor_abbr', 20)->nullable();
            $table->text('remark')->nullable();
            $table->enum('freight', ['Paid', 'To be Paid', 'To be Shiped'])->nullable();
            $table->decimal('total_qty_m', 12, 2)->default(0);
            $table->decimal('total_net_meter', 12, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
