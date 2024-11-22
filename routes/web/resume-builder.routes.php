<?php

use App\Http\Controllers\Builder\ResumeBuilderController;

Route::middleware(['auth', 'verified'])
    ->controller(ResumeBuilderController::class)
    ->group(function () {
        /** @uses ResumeBuilderController::index */
        Route::get('', 'index')->name('index');
    });
