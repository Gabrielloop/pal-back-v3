<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-routes', function () {
    return \Route::getRoutes()->getRoutesByName();
});