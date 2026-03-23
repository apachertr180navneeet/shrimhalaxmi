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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Basic
            $table->string('name');
            $table->string('abbr')->nullable();

            // Contact
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();

            // Business
            $table->string('firm_name')->nullable();
            $table->string('gst_no')->nullable();

            // Location
            $table->string('location')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Soft delete
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
