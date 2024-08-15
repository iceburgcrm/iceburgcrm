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
        Schema::create('ice_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->default('');
            $table->integer('start_hour');
            $table->integer('start_minute')->default(0);
            $table->integer('start_day')->default(0);
            $table->integer('command_id')->default(0);
            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly', 'yearly'])->default('once');
            $table->integer('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ice_schedules');
    }
};
