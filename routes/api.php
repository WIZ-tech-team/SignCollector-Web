<?php

use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\DetailedSignsController;
use App\Http\Controllers\Mobile\SignsController;
use Illuminate\Support\Facades\Request;
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
    // Route::delete('/spa/signs/detailed/{sign}', [DetailedSignsController::class, 'destroy']);

    Route::prefix('signs')->controller(SignsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
    });

    Route::prefix('signs/detailed')->controller(DetailedSignsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::delete('{sign}', 'destroy');
    });
});

Route::post('/test-size', function($request) {
    return response()->json([
        'size' => $request->header('Content-Length'),
        'max' => ini_get('post_max_size')
    ]);
});
