<?php

use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\DetailedSignsController;
use App\Http\Controllers\Mobile\SignsController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('user', 'user');
            Route::get('logout', 'logout');
        });
    });

    Route::prefix('signs')->controller(SignsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
    });

    Route::prefix('signs/detailed')->controller(DetailedSignsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
    });
});
