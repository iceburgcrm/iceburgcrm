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
        Schema::create('ice_endpoints', function (Blueprint $table) {
            $table->id();
            $table->integer('connector_id');
            $table->string('endpoint', 190)->default('');
            $table->string('class_name', 100)->default('default');
            $table->string('request_type', 10)->default('GET');
            $table->string('params', 190)->default('');
            $table->string('headers', 190)->default('');
            $table->integer('status')->default(1);
            $table->integer('last_run_status')->default(1);
            $table->string('last_run_message', 190)->default('');
            $table->string('last_run_data', 190)->default('');
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
