<?php

namespace App\Http\Controllers\Builder;

use Inertia\Inertia;
use Inertia\Response;

class ResumeBuilderController
{
    public function index(): Response
    {
        return  Inertia::render('Builder/Resume/ResumeIndexPage');
    }
}
