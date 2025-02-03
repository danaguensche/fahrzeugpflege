<?php
use App\Http\Controllers\CarController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

// Car API
Route::apiResource('cars', CarController::class);
Route::apiResource('customers', CustomerController::class);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
Route::delete('/cars/{kennzeichen}', [CarController::class, 'destroy']);


