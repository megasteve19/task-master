<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'projectCount' => $request->user()
                ->projects()
                ->active()
                ->count(),

            'taskCount' => $request->user()
                ->tasks()
                ->count(),

            'projects' => $request->user()
                ->projects()
                ->withCount([
                    'tasks' => fn (Builder $builder) => $builder->assignedTo($request->user())->active(),
                ])
                ->with('assignees')
                ->active()
                ->orderByDesc('due_date')
                ->get(),
        ]);
    }
}
