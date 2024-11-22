<?php

use App\Http\Controllers\Auth\VerifyEmailController;

Route::controller(VerifyEmailController::class)->group(function () {
    /** @uses VerifyEmailController::showNotice */
    Route::get('/email/verify', 'showNotice')
        ->middleware('auth')
        ->name('notice');

    /** @uses VerifyEmailController::verify */
    Route::get('/email/verify/{id}/{hash}', 'verify')
        ->middleware('signed')
        ->name('verify');

    /** @uses VerifyEmailController::sendVerification */
    Route::post('/email/verification-notification', 'sendVerification')
        ->middleware(['auth', 'throttle:send-email-verification'])
        ->name('send');
});
