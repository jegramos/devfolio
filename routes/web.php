<?php

use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class)
    ->middleware('guest')
    ->name('landingPage');

Route::get('/about', AboutPageController::class)
    ->middleware(['auth', 'verified'])
    ->name('aboutPage');

/** Users Routes */
Route::prefix('users')
    ->name('users.')
    ->group(base_path('routes/web/users.routes.php'));

/** Authentication Routes */
Route::name('auth.')
    ->group(base_path('routes/web/auth.routes.php'));

/** OAuth / OpenID Routes */
Route::name('oauth.')
    ->prefix('oauth')
    ->group(base_path('routes/web/oauth.routes.php'));

/** Verify Email Routes */
Route::name('verification.')
    ->group(base_path('routes/web/verify-email.routes.php'));

/** Builder Routes */
Route::prefix('builder')
    ->name('builder.')->group(function () {
        Route::name('resume.')
            ->prefix('resume')
            ->group(base_path('routes/web/resume-builder.routes.php'));
    });
