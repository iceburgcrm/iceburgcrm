<?php

namespace Database\Seeders\Custom;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

use Database\Seeders\Custom\ModuleSeeder as CustomModuleSeeder;
use Database\Seeders\Custom\FieldSeeder as CustomFieldSeeder;
use Database\Seeders\Custom\GenerateSeeder as CustomGenerateSeeder;
use Database\Seeders\Custom\RelationshipSeeder as CustomRelationshipSeeder;
use Database\Seeders\Custom\ModuleSubpanelSeeder as CustomModuleSubpanelSeeder;

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
        $this->call(CustomModuleSeeder::class);
        $this->call(CustomFieldSeeder::class);
        $this->call(CustomRelationshipSeeder::class);
        $this->call(CustomGenerateSeeder::class);
        $this->call(CustomModuleSubpanelSeeder::class);
        Log::info('Complete');

    }
}
