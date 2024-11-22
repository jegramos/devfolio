<?php

use App\Http\Controllers\User\UserController;

Route::middleware(['auth', 'verified'])
    ->controller(UserController::class)
    ->group(function () {
        Route::post('/create', 'create')->name('create');
    });
