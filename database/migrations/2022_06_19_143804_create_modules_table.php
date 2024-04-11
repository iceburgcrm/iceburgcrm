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
        Schema::create('ice_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->default('');
            $table->string('label')->default('');
            $table->string('description', 245)->default('');
            $table->integer('status')->default(1);
            $table->integer('faker_seed')->default(1);
            $table->integer('create_table')->default(1);
            $table->integer('view_order')->default(0);
            $table->integer('admin')->default(0);
            $table->integer('parent_id')->default(0);
            $table->integer('primary')->default(0);
            $table->string('primary_field', 64)->default('id');
            $table->string('icon', 128)->default('CircleStackIcon');
            $table->integer('module_group_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ice_modules');
    }
};
