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
            $table->unsignedBigInteger('connector_id');
            $table->string('name', 255);              
            $table->text('description')->nullable();  

            // REST-specific fields
            $table->string('endpoint', 255)->nullable(); 
            $table->string('class_name', 100)->nullable(); 
            $table->string('request_type', 20)->default('GET'); 
            $table->text('params')->nullable();  
            $table->text('headers')->nullable(); 
            $table->text('response_mapping')->nullable(); 

            // Runtime tracking
            $table->tinyInteger('status')->default(1); 
            $table->integer('retry_count')->default(3); 
            $table->text('last_run_message')->nullable(); 
            $table->text('last_run_data')->nullable(); 
            $table->text('last_run_status')->nullable(); 
            $table->timestamp('last_ran')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ice_endpoints');
    }
};
