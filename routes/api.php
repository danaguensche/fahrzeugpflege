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
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CommentController;


// Auth Routes
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'update']);

    // Users Routes (restricted for trainee)
    Route::middleware(CheckRole::class . ':trainer,admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/search', [UserController::class, 'search']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::get('/users/trainees', [UserController::class, 'getTrainees']);
    });

    Route::middleware(CheckRole::class . ':admin')->group(function () {
        Route::delete('users/{id}', [UserController::class, 'destroy']);
        Route::delete('users', [UserController::class, 'destroyMultiple']);
    });

    // Cars Routes (View only for trainee, full access for trainer/admin)
    Route::get('/cars/search', [CarSearchController::class, 'search']);
    Route::get('cars', [CarController::class, 'index']);
    Route::get('cars/{kennzeichen}', [CarController::class, 'show']);
    Route::get('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'details']);

    Route::middleware(CheckRole::class . ':trainer,admin')->group(function () {
        Route::post('cars', [CarController::class, 'store']);
        Route::put('cars/{kennzeichen}', [CarController::class, 'update']);
        Route::delete('cars/{kennzeichen}', [CarController::class, 'destroy']);
        Route::delete('cars', [CarController::class, 'destroyMultiple']);
        Route::put('cars/cardetails/{kennzeichen}', [CarDetailsController::class, 'update']);
        Route::post('cars/cardetails/{kennzeichen}/images', [CarDetailsController::class, 'uploadImages']);
        Route::delete('images/{imageId}', [CarDetailsController::class, 'deleteImage']);
        Route::post('cars/{kennzeichen}/images/{imageId}', [CarDetailsController::class, 'replaceImage']);
    });

    // Customers Routes
    Route::get('/customers/search', [CustomerSearchController::class, 'search']);
    Route::get('customers', [CustomerController::class, 'index']);
    Route::get('customers/{id}', [CustomerController::class, 'show']);
    Route::get('customer/customerdetails/{id}', [CustomerDetailsController::class, 'details']);

    Route::middleware(CheckRole::class . ':trainer,admin')->group(function () {
        Route::post('customers', [CustomerController::class, 'store']);
        Route::put('customers/{id}', [CustomerController::class, 'update']);
        Route::delete('customers/{id}', [CustomerController::class, 'destroy']);
        Route::delete('customers', [CustomerController::class, 'destroyMultiple']);
        Route::put('customer/customerdetails/{id}', [CustomerDetailsController::class, 'update']);
        Route::delete('customer/{customerId}/car/{carId}', [CustomerController::class, 'removeCarFromCustomer']);
    });

    // Jobs Routes

    Route::middleware(CheckRole::class . ':trainer,admin,trainee')->group(function () {
        Route::get('/jobs/search', [JobController::class, 'search']);
        Route::get('/jobs', [JobController::class, 'index']);
        Route::get('/jobs/{job}', [JobController::class, 'show']);
        Route::put('/jobs/{job}', [JobController::class, 'update']); // Moved outside of role middleware
        Route::get('/jobs/jobdetails/{id}', [JobDetailsController::class, 'details']);
        Route::put('/jobs/jobdetails/{id}', [JobDetailsController::class, 'update']);
        Route::delete('jobs/{job}/images/{imageId}', [JobController::class, 'deleteImage']);
        Route::post('jobs/{job}/images', [JobController::class, 'addImages']);
    });


    //User Routes
    Route::get('/users/search', [UserController::class, 'search']);
    Route::put('/users/{id}', [UserController::class, 'update']);

    // Comment Routes
    Route::get('/orders/{order}/comments', [App\Http\Controllers\CommentController::class, 'index']);
    Route::post('/orders/{order}/comments', [App\Http\Controllers\CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy']);


    // Image Report Routes
    Route::get('/tasks/{taskId}/images', [ImageReportController::class, 'index']);
    Route::post('/tasks/{taskId}/images', [ImageReportController::class, 'upload']);
    Route::delete('/images/{imageId}', [ImageReportController::class, 'destroy']);

    Route::get('/services', [ServiceController::class, 'index']); // Added route for services

    Route::middleware(CheckRole::class . ':trainer,admin,trainee')->group(function () {
        Route::post('/jobs', [JobController::class, 'store']);
        // Route::put('/jobs/{job}', [App\Http\Controllers\JobController::class, 'update']); // Moved outside
        Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
        Route::delete('jobs', [JobController::class, 'destroyMultiple']);
    });
});

// Logout Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
