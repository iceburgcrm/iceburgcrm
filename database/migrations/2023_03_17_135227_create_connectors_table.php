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
            $table->string('name', 200);
            $table->string('class', 50);
            $table->string('auth_type', 50)->default('None'); // For OAuth2, API Key, Basic Auth, etc.
            $table->string('auth_key')->default('')->nullable();
            $table->string('base_url', 200)->default('');
            $table->string('token_url', 200)->nullable(); // URL to obtain or refresh tokens
            $table->string('client_id', 200)->nullable(); // For OAuth2
            $table->string('client_secret', 200)->nullable(); // For OAuth2
            $table->string('username', 200)->nullable(); // For Basic Auth
            $table->string('password', 200)->nullable(); // For Basic Auth
            $table->string('access_token', 500)->nullable(); // For storing the access token
            $table->string('refresh_token', 500)->nullable(); // For storing the refresh token
            $table->timestamp('token_expires_at')->nullable(); // For token expiration time
            $table->integer('status')->default(1);
            $table->timestamps(); // Adds created_at and updated_at columns
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
