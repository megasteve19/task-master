<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard', [
            'projectCount' => Auth::user()->projects()->count(),
            'taskCount' => Auth::user()->tasks()->count(),
        ]);
    }
}
