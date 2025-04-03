<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Auth Routes

Route::middleware('auth:sanctum')->get('/users/me', [UserController::class, 'me']);

Route::apiResource('cars', CarController::class)->parameters(['cars' => 'kennzeichen']);
Route::delete('cars', [CarController::class, 'destroyMultiple']);
Route::apiResource('customers', CustomerController::class);
Route::delete('customers', [CustomerController::class, 'destroyMultiple']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
