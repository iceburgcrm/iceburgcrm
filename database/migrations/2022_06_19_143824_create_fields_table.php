<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ice_fields', function (Blueprint $table) {
            $table->string('name', 245);
            $table->string('label', 245);
            $table->integer('module_id');
            $table->string('validation', 245)->nullable();
            $table->string('input_type', 245)->default('text');
            $table->string('data_type', 100);
            $table->integer('field_length')->nullable();
            $table->integer('required')->default(0);
            $table->tinyInteger('is_nullable')->default(0);
            $table->string('default_value', 245)->default('');
            $table->tinyInteger('read_only')->default(0);
            $table->integer('related_module_id')->default(0);
            $table->string('related_field_id')->default('');
            $table->string('related_value_id')->default('');
            $table->integer('decimal_places')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->id();
            $table->timestamps();
            $table->index('module_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ice_fields');
    }
};
