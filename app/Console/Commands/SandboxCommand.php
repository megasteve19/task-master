<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class SandboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sandbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Project::withTrashed()->whereId('01hqgnkesgvzp6qaywr8xm369e')->forceDelete();
        // Project::withTrashed()->find('01hqgnkesgvzp6qaywr8xm369e')->forceDelete();
    }
}
