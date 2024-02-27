<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectArchiveRequest;
use App\Http\Requests\ProjectDestroyRequest;
use App\Http\Requests\ProjectPermanentlyDelete as ProjectPermanentlyDestroy;
use App\Http\Requests\ProjectRestoreArchived as ProjectRestoreArchivedRequest;
use App\Http\Requests\ProjectRestoreDeleted;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
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
                    fn (Builder $builder) => $builder->assignedTo(...explode(',', $request->input('filters.assignees')))
                )
                ->when($status === 'active', fn (Builder $builder) => $builder->active()->orderByDesc('due_date'))
                ->when($status === 'archived', fn (Builder $builder) => $builder->archived()->orderByDesc('archived_at'))
                ->when($status === 'trashed', fn (Builder $builder) => $builder->onlyTrashed()->orderByDesc('deleted_at'))
                ->get(),

            'filters' => [
                'search' => $request->input('filters.search', ''),
                'status' => $status,
            ],
        ]);
    }

    public function show(Project $project)
    {
        return Inertia::render('Projects/Show', [
            'project' => $project->load('assignees', 'tasks', 'tasks.assignees'),
        ]);
    }

    public function store(ProjectStoreRequest $request)
    {
        $project = Project::create($request->only('name', 'description', 'due_date'));

        $project->assignees()->sync(array_unique($request->input('assignees')));

        return redirect()->back();
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $project->update($request->only('name', 'description', 'due_date'));

        $project->assignees()->sync(array_unique($request->input('assignees')));

        return redirect()->back();
    }

    public function archive(ProjectArchiveRequest $request, Project $project)
    {
        $project->archive();

        return redirect()->back();
    }

    public function restoreArchive(ProjectRestoreArchivedRequest $request, Project $project)
    {
        $project->restoreFromArchive();

        return redirect()->back();
    }

    public function destroy(ProjectDestroyRequest $request, Project $project)
    {
        $project->delete();

        return redirect()->back();
    }

    public function restoreDelete(ProjectRestoreDeleted $request, Project $project)
    {
        $project->restore();

        return redirect()->back();
    }

    public function destroyPermanently(ProjectPermanentlyDestroy $request, Project $project)
    {
        $project->forceDelete();

        return redirect()->back();
    }
}
