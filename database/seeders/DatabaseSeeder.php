<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Log::info("Start Seeding");
        $this->call(ModuleSeeder::class);
        $this->call(FieldSeeder::class);
        $this->call(RelationshipSeeder::class);
        $this->call(GenerateSeeder::class);
        $this->call(ModuleSubpanelSeeder::class);
        $this->call(ModuleSubpanelGeneratorSeeder::class);
        Log::info("Complete");

    }
}
