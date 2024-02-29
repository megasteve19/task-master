<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Show the settings page.
     *
     * @return Response
     */
    public function show()
    {
        return Inertia::render('Settings');
    }
}
