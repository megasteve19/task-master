<?php

namespace App\Console\Commands\Projects;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all projects.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projects = Project::withCount('tasks')->with('assignees')->get();

        $this->table(
            ['ID', 'Name', 'Description', 'Due Date', 'Archived', 'Tasks', 'Assignees'],
            $projects->map(function (Project $project) {
                return [
                    $project->id,
                    Str::limit($project->name, 32),
                    Str::limit($project->description, 32),
                    $project->due_date ?? 'N/A',
                    $project->is_archived ? 'Yes' : 'No',
                    $project->tasks->count(),
                    $project->assignees->pluck('name')->join("\n") . "\n",
                ];
            })
        );
    }
}
