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
        Schema::create('work_flow_data', function (Blueprint $table) {
            $table->id();
            $table->integer('from_id')->default(0);
            $table->integer('from_module_id')->default(0);
            $table->integer('to_id')->default(0);
            $table->integer('to_module_id')->default(0);
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
        Schema::dropIfExists('work_flow_data');
    }
};
