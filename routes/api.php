<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CarDetailsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware('auth:sanctum')->get('/users/me', [UserController::class, 'me']);

// Cars Routes
Route::apiResource('cars', CarController::class)->parameters(['cars' => 'kennzeichen']);
Route::get('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'details']);
Route::delete('cars', [CarController::class, 'destroyMultiple']);

// Customers Routes
Route::apiResource('customers', CustomerController::class);
Route::get('customer/customerdetails/{id}', [CustomerController::class, 'details']);
Route::delete('customers', [CustomerController::class, 'destroyMultiple']);

// Logout Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');