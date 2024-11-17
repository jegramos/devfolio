<?php

use App\Http\Controllers\Auth\GithubLoginController;
use App\Http\Controllers\Auth\GoogleLoginController;

// Login via Google
Route::middleware('guest')
    ->name('google.')
    ->controller(GoogleLoginController::class)
    ->group(function () {
        /** @uses GoogleLoginController::redirect */
        Route::get('/google/redirect', 'redirect')->name('redirect');

        /** @uses GoogleLoginController::callback */
        Route::get('/google/callback', 'callback')->name('callback');
    });

// Login via Github
Route::middleware('guest')
    ->name('github.')
    ->controller(GithubLoginController::class)
    ->group(function () {
        /** @uses GithubLoginController::redirect */
        Route::get('/github/redirect', 'redirect')->name('redirect');

        /** @uses GithubLoginController::callback */
        Route::get('/github/callback', 'callback')->name('callback');
    });
