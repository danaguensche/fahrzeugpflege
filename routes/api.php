<?php
use App\Http\Controllers\CarController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

// Car API
Route::apiResource('cars', CarController::class)->parameters([
    'cars' => 'kennzeichen'
]);
Route::delete('cars', [CarController::class, 'destroyMultiple']);


Route::apiResource('customers', CustomerController::class);
Route::delete('customers', [CustomerController::class, 'destroyMultiple']);



