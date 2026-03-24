<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        DB::table('processes')->insert([
            ['name' => 'Grey', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Printed', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dyed', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tie Die', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'R&D', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
