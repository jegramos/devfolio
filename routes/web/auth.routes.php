<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')
    ->controller(LoginController::class)
    ->name('login.')
    ->group(function () {
        /** @uses LoginController::showForm */
        Route::get('/login', 'showForm')->name('showForm');

        /** @uses LoginController::authenticate */
        Route::post('/login', 'authenticate')
            ->middleware('throttle:login')
            ->name('authenticate')
            ->middleware(HandlePrecognitiveRequests::class);
    });

Route::middleware('auth')
    ->controller(LogoutController::class)
    ->name('logout.')
    ->group(function () {
        /** @uses LogoutController::logoutCurrent */
        Route::post('/logout', 'logoutCurrent')->name('current');

        /** @uses LogoutController::logoutOtherDevices */
        Route::post('/logout/other-devices', 'logoutOtherDevices')->name('otherDevices');
    });

Route::middleware('guest')
    ->controller(RegisterController::class)
    ->name('register.')
    ->group(function () {
        /** @uses RegisterController::showForm */
        Route::get('/register', 'showForm')->name('showForm');

        /** @uses RegisterController::processRegistration */
        Route::post('/register', 'processRegistration')->name('processRegistration');
    });
