<?php

namespace Database\Seeders\Default;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

use Database\Seeders\Default\ModuleSeeder as DefaultModuleSeeder;
use Database\Seeders\Default\FieldSeeder as DefaultFieldSeeder;
use Database\Seeders\Default\GenerateSeeder as DefaultGenerateSeeder;
use Database\Seeders\Default\RelationshipSeeder as DefaultRelationshipSeeder;
use Database\Seeders\Default\ModuleSubpanelSeeder as DefaultModuleSubpanelSeeder;

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
        $this->call(CoreDatabaseSeeder::class);
        $this->call(DefaultModuleSeeder::class);
        $this->call(DefaultFieldSeeder::class);
        $this->call(DefaultRelationshipSeeder::class);
        $this->call(DefaultGenerateSeeder::class);
        $this->call(DefaultModuleSubpanelSeeder::class);
        Log::info('Complete');

    }
}
