<?php

use App\Http\Controllers\AdminDashboard\AuthController;
use App\Http\Controllers\AdminDashboard\DetailedSignsController;
use App\Http\Controllers\AdminDashboard\ExportDetailedSignsController;
use App\Http\Controllers\AdminDashboard\UsersController;
use Illuminate\Support\Facades\Route;

// Need to be updated to only used by Admin users
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('user', 'user');
            Route::get('logout', 'logout');
        });
    });
});

Route::prefix('users')->controller(UsersController::class)->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/', 'index');
    Route::get('/trash', 'trash');
    Route::post('/', 'store');
    Route::get('/{user_id}', 'show');
    Route::post('/{user_id}', 'update');
    Route::delete('/{user_id}', 'destroy');
    Route::delete('/{user_id}/soft', 'moveToTrash');
    Route::get('/{user_id}/restore', 'restore');
});
Route::delete('signs/detailed/{sign}', [DetailedSignsController::class, 'destroy']);

// Route::prefix('signs/detailed')->controller(DetailedSignsController::class)->middleware(['auth:sanctum', 'admin'])->group(function () {
//     Route::get('/', 'index');
//     Route::post('/export', 'export');
// });
Route::prefix('signs/detailed')->controller(DetailedSignsController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/export', 'export');
    Route::post('/{id}',  'update');  // ← allow POST+_method=PATCH

    Route::patch('/{id}', 'update');   // ← add this

    Route::prefix('export')->controller(ExportDetailedSignsController::class)->group(function () {
        Route::post('/excel', 'exportExcel');
        Route::post('/kml', 'exportKML');
        Route::post('/shapefile', 'exportShapefile');
    });
});
