<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Projects/Index', [
            'projects' => Project::accessibleBy($request->user())
                ->withCount('tasks')
                ->with('assignees')
                ->when(
                    !empty($request->input('search')),
                    fn (Builder $builder) => $builder->whereIn('id', Project::search($request->input('search'))->keys())
                )
                ->when(
                    in_array($request->input('status'), ['active', 'archived', 'overdue', 'trashed']),
                    fn (Builder $builder) => match ($request->input('status')) {
                        'active' => $builder->active(),
                        'archived' => $builder->archived(),
                        'overdue' => $builder->overdue(),
                        'trashed' => $builder->onlyTrashed(),
                    }
                )
                ->paginate(10),
        ]);
    }
}
