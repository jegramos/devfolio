<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Inertia\Response;

class ProfileController
{
    public function index(): Response
    {
        return Inertia::render('Account/ProfileIndexPage');
    }
}
