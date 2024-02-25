<?php

namespace App\Console\Commands\Tasks;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = Task::with('project', 'assignees')->get();

        $this->table(
            ['ID', 'Name', 'Description', 'Status', 'Due Date', 'Project', 'Assignees'],
            $tasks->map(function (Task $task) {
                return [
                    $task->id,
                    Str::limit($task->name, 16),
                    Str::limit($task->description, 16),
                    $task->status->prettyName(),
                    $task->due_date ?? 'N/A',
                    Str::limit($task->project?->name ?? 'N/A', 16),
                    $task->assignees->pluck('name')->join("\n") . "\n",
                ];
            })
        );
    }
}
