<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CarDetailsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerDetailsController;
use App\Http\Controllers\CustomerSearchController;
use App\Http\Controllers\CarSearchController;


// Auth Routes
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
Route::middleware('auth:sanctum')->get('/users/me', [UserController::class, 'me']);
Route::middleware('auth:sanctum')->get('/users/search', [UserController::class, 'search']);
Route::put('/users/me', [UserController::class, 'update']);

// Cars Routes
Route::get('/cars/search', [CarSearchController::class, 'search']);
Route::apiResource('cars', CarController::class)->parameters(['cars' => 'kennzeichen']);
Route::get('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'details']);
Route::delete('cars', [CarController::class, 'destroyMultiple']);
Route::put('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'update']);
// Image management routes
Route::post('cars/cardetails/{kennzeichen}/images', [CarDetailsController::class, 'uploadImages']);
Route::delete('images/{imageId}', [CarDetailsController::class, 'deleteImage']);
Route::post('cars/{kennzeichen}/images/{imageId}', [CarDetailsController::class, 'replaceImage']);


// Customers Routes
Route::get('/customers/search', [CustomerSearchController::class, 'search']);
Route::apiResource('customers', CustomerController::class)->parameters(['customers' => 'id']);
Route::get('customer/customerdetails/{id}', [CustomerDetailsController::class, 'details']);
Route::delete('customers', [CustomerController::class, 'destroyMultiple']);
Route::put('customer/customerdetails/{id}', [CustomerDetailsController::class, 'update']);
Route::delete('customer/{customerId}/car/{carId}', [CustomerController::class, 'removeCarFromCustomer']);



// Services Routes
Route::apiResource('services', App\Http\Controllers\ServiceController::class);

// Jobs Routes
Route::get('/jobs/search', [App\Http\Controllers\JobController::class, 'search']);
Route::delete('jobs', [App\Http\Controllers\JobController::class, 'destroyMultiple']);
Route::apiResource('jobs', App\Http\Controllers\JobController::class);

// Logout Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');