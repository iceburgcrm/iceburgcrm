<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

use Database\Seeders\Core\ModuleSeeder as CoreModuleSeeder;
use Database\Seeders\Core\FieldSeeder as CoreFieldSeeder;
use Database\Seeders\Core\GenerateSeeder as CoreGenerateSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        print "Seeding core";
        Log::info('Start Seeding Core');
        $this->call(CoreModuleSeeder::class);
        $this->call(CoreFieldSeeder::class);
        $this->call(CoreGenerateSeeder::class);
        Log::info('Complete Core');

    }
}
