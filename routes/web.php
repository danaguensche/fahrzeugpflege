<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ReportController;

// Route::get('/', function () {
//     return redirect()->route('login');
// });

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
        Route::get('/fahrzeuge/fahrzeugdetails', 'cars')->name('fahrzeugdetails');
        Route::get('/kunden', 'customers')->name('kunden');
        Route::get('/kunden/kundendetails', 'customers')->name('fahrzeugdetails');
        Route::get('/auftraege', 'jobs')->name('jobs');
        Route::get('/berichte', 'reports')->name('reports');
        Route::get('/profil', 'profile')->name('profile');
        Route::get('/einstellungen', 'settings')->name('settings');
    });
});
