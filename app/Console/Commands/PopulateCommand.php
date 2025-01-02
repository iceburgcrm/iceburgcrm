<?php

namespace App\Console\Commands;

use App\Models\AICreate;
use Illuminate\Console\Command;

class PopulateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iceburg:populate {--amount=} {--module_id=} {--start_at=}';
    protected $description = 'AI Populate an existing CRM';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = $this->option('amount');
        $startAt = $this->option('start_at');
        $moduleId = $this->option('module_id');

        AICreate::generateAIRecords($seedAmount=1, $moduleId=0, $startAt=0);

    }
}
