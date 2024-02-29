<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Lists users.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Users/Index');
    }
}
