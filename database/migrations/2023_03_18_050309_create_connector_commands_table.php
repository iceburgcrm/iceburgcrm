<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ice_connector_commands', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // Display name
            $table->string('class_name');   // Maps to method in Connector class
            $table->string('method_name');   // Maps to method in Connector class
            $table->text('description')->nullable();
            $table->text('endpoint_id')->default(0);
            $table->text('last_run_data')->nullable();
            $table->string('last_run_status')->nullable();  // success / fail
            $table->string('last_run_message')->nullable();
            $table->enum('source', ['manual', 'ai'])->default('manual');
            $table->longText('ai_code')->nullable();
            $table->timestamp('last_ran')->nullable();
            $table->unsignedInteger('retry_count')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ice_connector_commands');
    }
};

