<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PDFController;

Route::get('/', function () {
    return redirect()->route('login');
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

  // PDF- & Report-Controller before PageController so urls with /berichte get checked by them first
    Route::controller(ReportController::class)->group(function () {
        Route::get('/berichte/form/{formType}', 'chooseForm');
    });
    // PDF Generation
    Route::controller(PDFController::class)->group(function () {
        Route::get('/berichte/pdf/{pdfType}', 'choosePDF');
    });


    Route::controller(PageController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/kalender', 'calendar')->name('calendar');
        Route::get('/fahrzeuge', 'cars')->name('fahrzeuge');
        Route::get('/kunden', 'customers')->name('kunden');
        Route::get('/auftraege', 'jobs')->name('jobs');
        Route::get('/berichte', 'reports')->name('reports');
        Route::get('/profil', 'profile')->name('profile');
        Route::get('/einstellungen', 'settings')->name('settings');
    });

    // Car API
    Route::controller(CarController::class)->group(function () {
        Route::get('/api/fahrzeuge', 'index')->name('fahrzeuge.index');
        Route::post('/api/fahrzeuge', 'store')->name('fahrzeuge.store');
        // Route::get('/fahrzeuge/{id}', 'edit')->name('fahrzeuge.edit');
        // Route::put('/fahrzeuge/{id}', 'update')->name('fahrzeuge.update');
        // Route::delete('/fahrzeuge/{id}', 'destroy')->name('fahrzeuge.destroy');
    });

    // Profil
    Route::post('/profil', [EmployeeController::class, 'store'])->name('profil.store');

    // Customer API
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/api/kunden', 'index')->name('kunden.index');
        Route::post('/api/kunden', 'store')->name('kunden.store');
        // Route::get('/kunden/{id}', 'edit')->name('kunden.edit');
        // Route::put('/kunden/{id}', 'update')->name('kunden.update');
        // Route::delete('/kunden/{id}', 'destroy')->name('kunden.destroy');
    });

});

