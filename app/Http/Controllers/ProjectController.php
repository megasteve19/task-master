<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectArchiveRequest;
use App\Http\Requests\ProjectDestroyRequest;
use App\Http\Requests\ProjectIndexRequest;
use App\Http\Requests\ProjectPermanentlyDelete as ProjectPermanentlyDestroy;
use App\Http\Requests\ProjectRestoreArchived as ProjectRestoreArchivedRequest;
use App\Http\Requests\ProjectRestoreDeleted;
use App\Http\Requests\ProjectShowRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Lists projects.
     *
     * @param ProjectIndexRequest $request
     *
     * @return Response
     */
    public function index(ProjectIndexRequest $request): Response
    {
        $status = $request->input('filters.status', 'active');

        return Inertia::render('Projects/Index', [
            'projects' => fn () => Project::accessibleBy($request->user())
                ->withCount('tasks')
                ->with('assignees')
                ->when(
                    $request->filled('filters.search'),
                    fn (Builder $builder) => $builder->whereIn('id', Project::search($request->input('filters.search'))->keys())
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
                        ->withCount(['tasks' => fn (Builder $builder) => $builder->withTrashed()])
                )
                ->get(),

            'filters' => [
                'search' => $request->input('filters.search', ''),
                'status' => $status,
            ],
        ]);
    }

    /**
     * Shows a project.
     *
     * @param ProjectShowRequest $request
     * @param Project $project
     *
     * @return Response
     */
    public function show(ProjectShowRequest $request, Project $project): Response
    {
        return Inertia::render('Projects/Show', [
            'project' => $project
                ->load('assignees')
                ->loadCount([
                    'assignees',
                    'tasks' => fn (Builder $builder) => $builder->active(),
                    'tasks as completed_tasks_count' => fn (Builder $builder) => $builder->active()->completed(),
                    'tasks as assignee_tasks_count' => fn (Builder $builder) => $builder->active()->assignedTo($request->user()),
                ]),
        ]);
    }

    /**
     * Stores a project.
     *
     * @param ProjectStoreRequest $request
     *
     * @return RedirectResponse
     */
    public function store(ProjectStoreRequest $request): RedirectResponse
    {
        $project = Project::create($request->only('name', 'description', 'due_date'));

        $project->assignees()->sync(array_unique($request->input('assignees')));

        return redirect()->back();
    }

    /**
     * Updates a project.
     *
     * @param ProjectUpdateRequest $request
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function update(ProjectUpdateRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->only('name', 'description', 'due_date'));

        $project->assignees()->sync(array_unique($request->input('assignees')));

        return redirect()->back();
    }

    /**
     * Archives a project.
     *
     * @param ProjectArchiveRequest $request
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function archive(ProjectArchiveRequest $request, Project $project): RedirectResponse
    {
        $project->archive();

        return redirect()->back();
    }

    /**
     * Restores an archived project.
     *
     * @param ProjectRestoreArchivedRequest $request
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function restoreArchive(ProjectRestoreArchivedRequest $request, Project $project): RedirectResponse
    {
        $project->restoreFromArchive();

        return redirect()->back();
    }

    /**
     * Deletes a project.
     *
     * @param ProjectDestroyRequest $request
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function destroy(ProjectDestroyRequest $request, Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->back();
    }

    /**
     * Restores a deleted project.
     *
     * @param ProjectRestoreDeleted $request
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function restoreDelete(ProjectRestoreDeleted $request, Project $project): RedirectResponse
    {
        $project->restore();

        return redirect()->back();
    }

	/**
	 * Permanently deletes a project.
	 *
	 * @param ProjectPermanentlyDestroy $request
	 * @param Project $project
	 *
	 * @return RedirectResponse
	 */
    public function destroyPermanently(ProjectPermanentlyDestroy $request, Project $project): RedirectResponse
    {
        $project->forceDelete();

        return redirect()->back();
    }
}
