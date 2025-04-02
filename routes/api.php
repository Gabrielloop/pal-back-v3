<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

Route::apiResource('users', UserController::class);

Route::get('/test', function () {
    return response()->json(['message' => 'API OK']);
})->name('api.test');