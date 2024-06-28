<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\PasswordController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\AuthTokenIsValid;
use App\Http\Middleware\IsAdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::middleware([AuthTokenIsValid::class, IsAdminMiddleware::class])->group(function () {
            Route::post('/create', [AdminAuthController::class, 'register']);
            Route::get('/logout', [AdminAuthController::class, 'logout']);
        });
    });

    Route::prefix('user')->group(function () {
        Route::post('/create', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('forgot-password', [PasswordController::class, 'sendPasswordResetLink']);
        Route::post('reset-password-token', [PasswordController::class, 'resetPassword']);
        Route::middleware([AuthTokenIsValid::class])->group(function () {
            Route::get('/logout', [AuthController::class, 'logout']);
            Route::get('/', [UserController::class, 'getUser']);
            Route::put('/edit', [UserController::class, 'editUser']);
            Route::delete('/', [UserController::class, 'deleteUser']);
            Route::get('orders', [OrderController::class, 'getUserOrders']);
        });
    });
});
