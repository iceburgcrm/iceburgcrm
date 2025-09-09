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
        Schema::create('ice_connectors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // allow longer names for templates
            $table->text('description')->nullable(); // only used for template connectors
            $table->string('auth_type', 100)->default('None');
            $table->string('auth_key', 255)->nullable();
            $table->string('base_url', 255)->default('');
            $table->string('token_url', 255)->nullable();
            $table->string('client_id', 255)->nullable();
            $table->string('client_secret', 255)->nullable();
            $table->string('username', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->integer('status')->default(1); // 1 = active, 0 = inactive
            $table->tinyInteger('type')->default(1)->comment('1 = template, 2 = custom');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ice_connectors');
    }
};
