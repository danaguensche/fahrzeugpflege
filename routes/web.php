<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TestController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginPost')->name('login.post');
    Route::get('/signup', 'signup')->name('signup');
    Route::post('/signup', 'signupPost')->name('signup.post');
    Route::post('/logout', 'logout')->name('logout');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::controller(PageController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/kalender', 'calendar');
        Route::get('/fahrzeuge', 'cars');
        Route::get('/kunden', [CustomerController::class, 'index']);
        Route::get('/kunden/hinzufuegen', 'addcustomer');
        Route::get('/auftraege', 'jobs');
        Route::get('/berichte', 'reports');
        Route::get('/chat', 'chat');
        Route::get('/profil', 'profile');
        Route::get('/einstellungen', 'settings');
    });

    Route::post('/fahrzeuge', [CarController::class, 'store']);
    Route::post('/profil', [EmployeeController::class, 'store']);
    Route::post('/kunden/hinzufuegen', [CustomerController::class, 'store']);
});

Route::get('/new', [TestController::class, 'controllerMethod']);
