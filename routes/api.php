<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CarDetailsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerDetailsController;

// Auth Routes
Route::middleware('auth:sanctum')->get('/users/me', [UserController::class, 'me']);
Route::put('/users/me', [UserController::class, 'update']);
// Cars Routes
Route::apiResource('cars', CarController::class)->parameters(['cars' => 'kennzeichen']);
Route::get('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'details']);
Route::delete('cars', [CarController::class, 'destroyMultiple']);
Route::put('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'update']);

// Customers Routes
Route::apiResource('customers', CustomerController::class)->parameters(['customers' => 'id']);
Route::get('customer/customerdetails/{id}', [CustomerDetailsController::class, 'details']);
Route::delete('customers', [CustomerController::class, 'destroyMultiple']);
Route::put('customer/customerdetails/{id}', [CustomerDetailsController::class, 'update']);

// Logout Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');