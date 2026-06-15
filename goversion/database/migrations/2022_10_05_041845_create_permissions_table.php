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
        Schema::create('ice_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id')->default(0);
            $table->integer('role_id')->default(0);
            $table->integer('can_read')->default(1);
            $table->integer('can_write')->default(1);
            $table->integer('can_delete')->default(1);
            $table->integer('can_export')->default(1);
            $table->integer('can_import')->default(1);
            $table->timestamps();
            $table->index('module_id');
            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ice_permissions');
    }
};
