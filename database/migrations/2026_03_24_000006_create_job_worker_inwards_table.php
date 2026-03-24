<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_worker_inwards', function (Blueprint $table) {
            $table->id();
            $table->date('inward_date');
            $table->string('ch_no', 30)->unique();
            $table->foreignId('job_worker_id')->constrained('job_workers')->cascadeOnDelete();
            $table->decimal('total_meter', 12, 2)->default(0);
            $table->decimal('total_net_meter', 12, 2)->default(0);
            $table->text('remark')->nullable();
            $table->string('status', 20)->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_worker_inwards');
    }
};
