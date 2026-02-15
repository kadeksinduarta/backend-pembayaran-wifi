<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin Routes
    Route::prefix('admin')->middleware('can:admin')->group(function () {
        // User management
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

        // Bill management
        Route::get('/bills', [AdminController::class, 'getBills']);
        Route::post('/bills', [AdminController::class, 'createBill']);
        Route::put('/bills/{id}', [AdminController::class, 'updateBill']);
        Route::delete('/bills/{id}', [AdminController::class, 'deleteBill']);

        // Payment management
        Route::get('/payments', [AdminController::class, 'getPayments']);
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment']);

        // Settings
        Route::get('/settings', [AdminController::class, 'getSettings']);
        Route::put('/settings', [AdminController::class, 'updateSettings']);
    });

    // User Routes
    Route::prefix('user')->group(function () {
        Route::get('/dashboard', [UserController::class, 'getDashboard']);
        Route::post('/payments/{id}/proof', [UserController::class, 'uploadProof']);
        Route::get('/bills', [UserController::class, 'getBills']);
    });
});
