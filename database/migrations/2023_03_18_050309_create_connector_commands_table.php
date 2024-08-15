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
        Schema::create('ice_connector_commands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connector_id');
            $table->string('name', 190)->default('');
            $table->string('description', 500)->default('');
            $table->string('method_name', 100)->default('default');
            $table->integer('status')->default(1);
            $table->json('last_run_data')->nullable(); // Storing data as JSON
            $table->timestamp('last_ran')->nullable(); // Changed from integer to timestamp
            $table->integer('retry_count')->default(3); // Number of retry attempts
            $table->timestamps(); // Adds created_at and updated_at columns

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ice_connector_commands');
    }
};
