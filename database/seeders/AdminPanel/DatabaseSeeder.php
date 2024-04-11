<?php

namespace Database\Seeders\AdminPanel;

use Database\Seeders\Core\FieldSeeder as CoreFieldSeeder;
use Database\Seeders\Core\GenerateSeeder as CoreGenerateSeeder;
use Database\Seeders\Core\ModuleSeeder as CoreModuleSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

use Database\Seeders\AdminPanel\TableSeeder as TableSeeder;

use Database\Seeders\Core\DatabaseSeeder as CoreDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Log::info('Start Seeding');
        $this->call(CoreModuleSeeder::class);
        $this->call(CoreFieldSeeder::class);
        $this->call(TableSeeder::class);
        $this->call(CoreGenerateSeeder::class);
        Log::info('Complete');

    }
}
