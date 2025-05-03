<?php

use App\Http\Controllers\Mobile\SubscriptionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/spa');
});

Route::get('/spa', function () {
    return view('vue-spa-index');
})->where('any', '.*');
