<?php

use App\Http\Controllers\User\ProfileController;

Route::controller(ProfileController::class)->middleware(['auth', 'verified'])->group(function () {
    /** @uses ProfileController::index */
    Route::get('/profile', 'index')->name('index');

    /** @uses ProfileController::update */
    Route::patch('/profile/{user}', 'update')->name('update');
});
