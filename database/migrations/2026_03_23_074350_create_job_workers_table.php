<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('job_workers', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('abbr')->nullable();

            // Contact
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();

            // Address
            $table->text('address')->nullable();

            // Business Info
            $table->string('firm_name')->nullable();

            // Location
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            // Status ENUM
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Soft Delete
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_workers');
    }
};
