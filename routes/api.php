<?php

use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\DetailedSignsController;
use App\Http\Controllers\Mobile\SignsController;
use App\Http\Controllers\Mobile\SignsGroupsController;
use App\Http\Controllers\PlacesController;
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
        Route::delete('{sign}', 'destroy');
        Route::post('{sign_id}/images', 'addImage');
        Route::delete('{sign_id}/images/{image_id}', 'deleteImage');
    });

    Route::prefix('signs/groups')->controller(SignsGroupsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('{group_id}/images', 'addImage');
        Route::delete('{group_id}/images/{image_id}', 'deleteImage');
    });
});

Route::get('/governorates', [PlacesController::class, 'governoratesList']);
Route::get('/villages', [PlacesController::class, 'villagesList']);
Route::get('/roads', [PlacesController::class, 'roadsList']);
