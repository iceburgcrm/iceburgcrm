<?php

namespace Database\Seeders\Ai;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;


use Database\Seeders\Ai\GenerateSeeder as AiGenerateSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Log::info('Start Seeding Core');
        #$this->call(CoreModuleSeeder::class);
        #$this->call(CoreFieldSeeder::class);
        $this->call(AiGenerateSeeder::class);
        Log::info('Complete Core');

    }
}
