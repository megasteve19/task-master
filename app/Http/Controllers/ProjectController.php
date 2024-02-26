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
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Projects/Index', [
            'projects' => fn () =>Project::accessibleBy($request->user())
                ->withCount('tasks')
                ->with('assignees')
                ->when(
                    !empty($request->input('search')),
                    fn (Builder $builder) => $builder->whereIn('id', Project::search($request->input('search'))->keys())
                )
                ->where(
                    fn (Builder $builder) => match ($request->input('status', 'active'))
                    {
                        'active' => $builder->active(),
                        'archived' => $builder->archived(),
                        'trashed' => $builder->onlyTrashed(),
                        default => $builder,
                    }
                )
                ->paginate(10),

            'users' => Inertia::lazy(fn () => $request->whenFilled(
                key: 'userSearch',
                callback: fn (string $search) => User::query()
                    ->whereIn('id', User::search($search)->keys())
                    ->whereNotIn('id', explode(',', $request->input('userExcept', [])))
                    ->limit(5)
                    ->get(),
                default: fn () => []
            )),
        ]);
    }

    public function store(ProjectStoreRequest $request)
    {
        $project = Project::create($request->only('name', 'description', 'due_date'));

        $project->assignees()->sync($request->input('assignees'));

        return redirect()->back();
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $project->update($request->only('name', 'description', 'due_date'));

        $project->assignees()->sync($request->input('assignees'));

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
