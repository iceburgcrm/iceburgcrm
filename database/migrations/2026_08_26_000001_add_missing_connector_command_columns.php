<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ice_connector_commands', function (Blueprint $table) {
            if (! Schema::hasColumn('ice_connector_commands', 'connector_id')) {
                $table->unsignedBigInteger('connector_id')->default(0)->after('id');
            }

            if (! Schema::hasColumn('ice_connector_commands', 'status')) {
                $table->tinyInteger('status')->default(1)->after('endpoint_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ice_connector_commands', function (Blueprint $table) {
            if (Schema::hasColumn('ice_connector_commands', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('ice_connector_commands', 'connector_id')) {
                $table->dropColumn('connector_id');
            }
        });
    }
};
