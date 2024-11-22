<?php

use App\Http\Controllers\User\ProfileController;

Route::controller(ProfileController::class)->middleware(['auth', 'verified'])->group(function () {
    /** @uses ProfileController::index */
    Route::get('/profile', 'index')->name('profile.index');
});
