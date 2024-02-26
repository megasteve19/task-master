<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class SettingController extends Controller
{
    public function show()
    {
        return Inertia::render('Settings');
    }
}
