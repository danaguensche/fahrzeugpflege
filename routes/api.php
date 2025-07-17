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
use App\Http\Controllers\JobDetailsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ImageReportController;


// Auth Routes
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'index']);

    // Cars Routes (View only for trainee, full access for trainer/admin)
    Route::get('/cars/search', [CarSearchController::class, 'search']);
    Route::get('cars', [CarController::class, 'index']);
    Route::get('cars/{kennzeichen}', [CarController::class, 'show']);
    Route::get('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'details']);

    Route::middleware(\App\Http\Middleware\CheckRole::class . ':trainer,admin')->group(function () {
        Route::post('cars', [CarController::class, 'store']);
        Route::put('cars/{kennzeichen}', [CarController::class, 'update']);
        Route::delete('cars/{kennzeichen}', [CarController::class, 'destroy']);
        Route::delete('cars', [CarController::class, 'destroyMultiple']);
        Route::put('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'update']);
        Route::post('cars/cardetails/{kennzeichen}/images', [CarDetailsController::class, 'uploadImages']);
        Route::delete('images/{imageId}', [CarDetailsController::class, 'deleteImage']);
        Route::post('cars/{kennzeichen}/images/{imageId}', [CarDetailsController::class, 'replaceImage']);
    });
});

Route::middleware('auth:sanctum')->get('/users/search', [UserController::class, 'search']);
Route::put('/users/me', [UserController::class, 'update']);


// Customers Routes
Route::get('/customers/search', [CustomerSearchController::class, 'search']);
Route::apiResource('customers', CustomerController::class)->parameters(['customers' => 'id']);
Route::get('customer/customerdetails/{id}', [CustomerDetailsController::class, 'details']);
Route::delete('customers', [CustomerController::class, 'destroyMultiple']);
Route::put('customer/customerdetails/{id}', [CustomerDetailsController::class, 'update']);
Route::delete('customer/{customerId}/car/{carId}', [CustomerController::class, 'removeCarFromCustomer']);



// Services Routes
Route::apiResource('services', ServiceController::class);
Route::get('/services/search', [ServiceController::class, 'search']);

// Jobs Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs/search', [App\Http\Controllers\JobController::class, 'search']);
    Route::get('/jobs', [App\Http\Controllers\JobController::class, 'index']);
    Route::get('/jobs/{job}', [App\Http\Controllers\JobController::class, 'show']);
    Route::get('/jobs/jobdetails/{id}', [JobDetailsController::class, 'details']);
    Route::put('/jobs/jobdetails/{id}', [JobDetailsController::class, 'update']);

    //User Routes
    Route::get('/users/search', [UserController::class, 'search']);
    
    // Comment Routes
    Route::post('/comments', [App\Http\Controllers\CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy']);

    // Image Report Routes
    Route::get('/tasks/{taskId}/images', [ImageReportController::class, 'index']);
    Route::post('/tasks/{taskId}/images', [ImageReportController::class, 'upload']);
    Route::delete('/images/{imageId}', [ImageReportController::class, 'destroy']);

    Route::middleware(\App\Http\Middleware\CheckRole::class . ':trainer,admin')->group(function () {
        Route::post('/jobs', [App\Http\Controllers\JobController::class, 'store']);
        Route::put('/jobs/{job}', [App\Http\Controllers\JobController::class, 'update']);
        Route::delete('/jobs/{job}', [App\Http\Controllers\JobController::class, 'destroy']);
        Route::delete('jobs', [App\Http\Controllers\JobController::class, 'destroyMultiple']);
    });
});

// Logout Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');