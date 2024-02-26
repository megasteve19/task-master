<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => Task::query()
                ->assignedTo($request->user())
				->with('assignees')
                ->with('project')
				->orderByDesc('due_date')
				->get(),
        ]);
    }
}
