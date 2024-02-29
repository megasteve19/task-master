<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\ProjectTaskArchiveRequest;
use App\Http\Requests\ProjectTaskDestroyRequest;
use App\Http\Requests\ProjectTaskIndexRequest;
use App\Http\Requests\ProjectTaskPermanentDestroyRequest;
use App\Http\Requests\ProjectTaskRestoreArchivedRequest;
use App\Http\Requests\ProjectTaskRestoreDeletedRequest;
use App\Http\Requests\ProjectTaskStoreRequest;
use App\Http\Requests\ProjectTaskSwapPriorityRequest;
use App\Http\Requests\ProjectTaskUpdateRequest;
use App\Http\Requests\ProjectTaskUpdateStatusRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectTaskController extends Controller
{
    /**
     * Lists tasks for a project.
     *
     * @param ProjectTaskIndexRequest $request
     * @param Project $project
     *
     * @return Response
     */
    public function index(ProjectTaskIndexRequest $request, Project $project): Response
    {
        $status = $request->input('filters.status', 'active');

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

    /**
     * Stores a task for a project.
     *
     * @param ProjectTaskStoreRequest $request
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function store(ProjectTaskStoreRequest $request, Project $project): RedirectResponse
    {
        $task = $project->tasks()->create([
            ...$request->except('assignees'),
            'status' => TaskStatus::Todo,
        ]);

        $task->assignees()->sync($request->assignees);

        return redirect()->back();
    }

    /**
     * Updates a task for a project.
     *
     * @param ProjectTaskUpdateRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function update(ProjectTaskUpdateRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->update($request->except('assignees'));

        $task->assignees()->sync($request->assignees);

        return redirect()->back();
    }

    /**
     * Destroys a task for a project.
     *
     * @param ProjectTaskDestroyRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function destroy(ProjectTaskDestroyRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->back();
    }

    /**
     * Permanently destroys a task for a project.
     *
     * @param ProjectTaskPermanentDestroyRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function destroyPermanently(ProjectTaskPermanentDestroyRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->forceDelete();

        return redirect()->back();
    }

    /**
     * Restores a deleted task for a project.
     *
     * @param ProjectTaskRestoreDeletedRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function restoreDelete(ProjectTaskRestoreDeletedRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->restore();

        return redirect()->back();
    }

    /**
     * Archives a task for a project.
     *
     * @param ProjectTaskArchiveRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function permanentlyDelete(Project $project, Task $task)
    {
        $task->forceDelete();

        return redirect()->back();
    }

    /**
     * Archives a task for a project.
     *
     * @param ProjectTaskArchiveRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function archive(ProjectTaskArchiveRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->archive();

        return redirect()->back();
    }

    /**
     * Restores an archived task for a project.
     *
     * @param ProjectTaskRestoreArchivedRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function restoreArchive(ProjectTaskRestoreArchivedRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->restoreFromArchive();

        return redirect()->back();
    }

    /**
     * Updates the status of a task for a project.
     *
     * @param ProjectTaskUpdateStatusRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function updateStatus(ProjectTaskUpdateStatusRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->update(['status' => $request->status]);

        return redirect()->back();
    }

    /**
     * Swaps the priority of a task with another task.
     *
     * @param ProjectTaskSwapPriorityRequest $request
     * @param Project $project
     * @param Task $task
     *
     * @return RedirectResponse
     */
    public function swapPriority(ProjectTaskSwapPriorityRequest $request, Project $project, Task $task): RedirectResponse
    {
        $currentPriority = $task->priority;
        $swapWithTask = $project->tasks()->findOrFail($request->swap_with_task_id);

        $task->update(['priority' => $swapWithTask->priority]);
        $swapWithTask->update(['priority' => $currentPriority]);

        return redirect()->back();
    }
}
