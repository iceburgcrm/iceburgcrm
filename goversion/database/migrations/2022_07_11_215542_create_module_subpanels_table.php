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
        Schema::create('ice_module_subpanels', function (Blueprint $table) {
            $table->id();
            $table->string('subpanel_filter')->nullable();
            $table->string('name', 245);
            $table->string('label', 245);
            $table->string('module_id');
            $table->integer('list_size')->default(10);
            $table->string('list_order_column')->default('id');
            $table->string('list_order')->default('desc');
            $table->integer('relationship_id')->default(0);
            $table->integer('status')->default(1);
            $table->integer('saved_search_id')->default(0);
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
        Schema::dropIfExists('ice_module_subpanels');
    }
};
