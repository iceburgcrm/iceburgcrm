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
        Schema::create('datalets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('label')->default('');
            $table->integer('type')->default(1);
            $table->integer('role_id')->default(0);
            $table->integer('field_id')->default(0);
            $table->integer('module_id')->default(0);
            $table->integer('relationship_id')->default(0);
            $table->integer('size')->default(6);
            $table->integer('display_order')->default(0);
            $table->integer('active')->default(1);
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
        Schema::dropIfExists('datalets');
    }
};
