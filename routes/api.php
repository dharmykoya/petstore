<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\AuthTokenIsValid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('/create', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware([AuthTokenIsValid::class])->group(function () {
            Route::get('/', [UserController::class, 'getUser']);
        });
    });
});
