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
        Schema::create('endpoints', function (Blueprint $table) {
            $table->id();
            $table->integer('connector_id');
            $table->string('endpoint', 200)->default('');
            $table->string('class_name', 100)->default('default');
            $table->string('request_type', 10)->default('GET');
            $table->string('params', 5000)->default('');
            $table->string('headers', 5000)->default('');
            $table->integer('status')->default(1);
            $table->integer('last_run_status')->default(1);
            $table->string('last_run_message', 1000)->default('');
            $table->string('last_run_data', 10000)->default('');
            $table->integer('last_ran')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endpoints');
    }
};
