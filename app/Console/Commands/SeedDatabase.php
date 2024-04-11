<?php

namespace App\Console\Commands;

use App\Models\AICreate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'iceburg:create {--type=default} {--prompt=} {--model=gpt-3.5-turbo} {--logo=} {--seed_amount=} {--seed_type=} {--module_id=} {--connection_host=} {--connection_port=} {--connection_database=} {--connection_username=} {--connection_password=} {--connection_charset=} {--connection_collation=}';
    protected $description = 'Create your crm';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */


    public function handle()
    {
        $type = $this->option('type');
        $prompt = $this->option('prompt');
        $model = $this->option('model');
        $logo = $this->option('logo');
        $seed_amount = $this->option('seed_amount');
        $seed_type = $this->option('seed_type');
        $module_id = $this->option('module_id');
        $connection_host = $this->option('connection_host');
        $connection_port = $this->option('connection_port');
        $connection_database = $this->option('connection_database');
        $connection_username = $this->option('connection_username');
        $connection_password = $this->option('connection_password');
        $connection_charset = $this->option('connection_charset');
        $connection_collation = $this->option('connection_collation');


        Config::set('database.connections.custom', [
            'driver' => 'mysql',
            'host' => !empty($connection_host) ? $connection_host : env('DB_HOST'),
            'port' => !empty($connection_port) ? $connection_port : env('DB_PORT'),
            'database' => !empty($connection_database) ? $connection_database : env('DB_DATABASE'),
            'username' => !empty($connection_username) ? $connection_username : env('DB_USERNAME'),
            'password' => !empty($connection_password) ? $connection_password : env('DB_PASSWORD'),
            'unix_socket' => '',
            'charset' => !empty($connection_charset) ? $connection_charset : 'utf8mb4',
            'collation' =>  !empty($connection_collation) ? $connection_collation : 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ]);
        Config::set('database.default', 'custom');

        if (!Schema::connection('custom')->hasTable('migrations')) {
            Artisan::call('migrate', [
                '--force' => true,
                '--database' => 'custom',
            ]);
        }


        if ($type == 'ai' && is_null($prompt)) {
            $this->error('The --prompt option is required when --type is ai.');
            return 1;
        }

        switch ($type) {
            case 'adminpanel':
                $output = new \Symfony\Component\Console\Output\BufferedOutput();
                Artisan::call('db:seed', [
                    '--database' => 'custom',
                    '--class' =>  \Database\Seeders\AdminPanel\DatabaseSeeder::class,
                    '--force' => true,
                ], $output);
                $errorOutput = $output->fetch();
                \Log::error("Seeder error output: " . $errorOutput);

                break;
            case 'populate':
                AICreate::generateAIRecords($seed_amount, $module_id);
                break;
            case 'core':
                Artisan::call('db:seed', [
                    '--database' => 'custom',
                    '--class' =>  \Database\Seeders\Core\DatabaseSeeder::class,
                    '--force' => true,
                ]);
                break;
            case 'custom':
                Artisan::call('db:seed', [
                    '--database' => 'custom',
                    '--class' =>  \Database\Seeders\Custom\DatabaseSeeder::class,
                    '--force' => true,
                ]);
                break;
            case 'ai':
                $output = new \Symfony\Component\Console\Output\BufferedOutput();
                $returnValue=Artisan::call('db:seed', [
                    '--database' => 'custom',
                    '--class' =>  \Database\Seeders\Core\DatabaseSeeder::class,
                    '--force' => true,
                ], $output);
                $errorOutput = $output->fetch();
                \Log::error("Seeder error output: " . $errorOutput);
                \Log::info("First seeder: " . $returnValue);
                $returnValue=Artisan::call('db:seed', [
                    '--database' => 'custom',
                    '--class' =>  \Database\Seeders\Ai\ModuleSeeder::class,
                    '--force' => true,
                ], $output);
                $errorOutput = $output->fetch();
                \Log::error("Seeder2 error output: " . $errorOutput);
                \Log::info("Second seeder: " . $returnValue);
                AICreate::process($prompt, $model, $logo, $seed_amount, $seed_type);
                $returnValue=Artisan::call('db:seed', [
                    '--database' => 'custom', // Use the custom connection for seeding
                    '--class' =>  \Database\Seeders\Ai\DatabaseSeeder::class,
                    '--force' => true,
                ], $output);
                $errorOutput = $output->fetch();
                \Log::error("Seeder3 error output: " . $errorOutput);
                \Log::info("Last seeder: " . $returnValue);
                break;
            default:
                Artisan::call('db:seed', [
                    '--database' => 'custom', // Use the custom connection for seeding
                    '--class' =>  \Database\Seeders\Default\DatabaseSeeder::class
                ]);
                break;
        }
    }
}
