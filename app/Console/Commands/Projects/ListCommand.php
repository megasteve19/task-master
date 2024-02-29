<?php

namespace App\Console\Commands\Projects;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\table;

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
        $projects = Project::query()
            ->withCount('tasks')
            ->with('assignees')
            ->withTrashed()
            ->latest()
            ->get();

        table(
            ['ID', 'Name', 'Description', 'Due Date', 'Tasks', 'Assignees', 'Archived', 'Trashed'],
            $projects->map(function(Project $project) {
                return [
                    $project->id,
                    Str::limit($project->name, 32),
                    Str::limit($project->description, 32),
                    $project->due_date ?? 'N/A',
                    $project->tasks->count(),
                    $project->assignees->pluck('name')->join("\n") . "\n",
                    $project->archived_at ? 'Yes' : 'No',
                    $project->trashed() ? 'Yes' : 'No',
                ];
            })
        );
    }
}
