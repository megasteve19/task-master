<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\ProjectTaskIndexRequest;
use App\Http\Requests\ProjectTaskStoreRequest;
use App\Http\Requests\ProjectTaskSwapPriorityRequest;
use App\Http\Requests\ProjectTaskUpdateStatusRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectTaskController extends Controller
{
    public function index(ProjectTaskIndexRequest $request, Project $project)
    {
        $status = $project->archived_at ? 'archived' : $request->input('filters.status', 'active');

        return Inertia::render('Projects/Tasks/Index', [
            'project' => $project,
            'tasks' => $project->tasks()
                ->with('assignees')
                ->when(
                    $request->filled('filters.search'),
                    fn (Builder $builder) => $builder->whereIn('id', Task::search($request->input('filters.search'))->keys())
                )
                ->when(
                    $request->filled('filters.assignees'),
                    fn (Builder $builder) => $builder->assignedTo(...$request->input('filters.assignees'))
                )
                ->when($status === 'active', fn (Builder $builder) => $builder->active()->orderByDesc('due_date'))
                ->when($status === 'archived', fn (Builder $builder) => $builder->archived()->orderByDesc('archived_at'))
                ->when(
                    $status === 'trashed',
                    fn (Builder $builder) => $builder->onlyTrashed()
                        ->orderByDesc('deleted_at')
                )
                ->get(),

            'filters' => [
                'search' => $request->input('filters.search', ''),
                'status' => $status,
            ],
        ]);
    }

    public function store(ProjectTaskStoreRequest $request, Project $project)
    {
        $task = $project->tasks()->create([
            ...$request->except('assignees'),
            'status' => TaskStatus::Todo,
        ]);

        $task->assignees()->sync($request->assignees);

        return redirect()->route('projects.tasks.index', $project);
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $task->update($request->except('assignees'));

        $task->assignees()->sync($request->assignees);

        return redirect()->route('projects.tasks.index', $project);
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();

        return redirect()->route('projects.tasks.index', $project);
    }

	public function destroyPermanently(Project $project, Task $task)
	{
		$task->forceDelete();

		return redirect()->route('projects.tasks.index', $project);
	}

    public function restoreDelete(Project $project, Task $task)
    {
        $task->restore();

        return redirect()->route('projects.tasks.index', $project);
    }

    public function permanentlyDelete(Project $project, Task $task)
    {
        $task->forceDelete();

        return redirect()->route('projects.tasks.index', $project);
    }

    public function archive(Project $project, Task $task)
    {
        $task->archive();

        return redirect()->route('projects.tasks.index', $project);
    }

    public function restoreArchive(Project $project, Task $task)
    {
        $task->restoreFromArchive();

        return redirect()->route('projects.tasks.index', $project);
    }

    public function updateStatus(ProjectTaskUpdateStatusRequest $request, Project $project, Task $task)
    {
        $task->update(['status' => $request->status]);

        return redirect()->route('projects.tasks.index', $project);
    }

    public function swapPriority(ProjectTaskSwapPriorityRequest $request, Project $project, Task $task)
    {
        $currentPriority = $task->priority;
        $swapWithTask = $project->tasks()->findOrFail($request->swap_with_task_id);

        $task->update(['priority' => $swapWithTask->priority]);
        $swapWithTask->update(['priority' => $currentPriority]);

        return redirect()->route('projects.tasks.index', $project);
    }
}
