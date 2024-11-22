<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Inertia\Response;

class UserController
{
    public function create(): Response
    {
        return Inertia::render('User/Create');
    }
}
