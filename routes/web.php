<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});


// Wenn die Route nicht existiert, wird man automatisch auf die Login-Seite weitergeleitet
Route::fallback(function () {
    return redirect()->route('login');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'login')->name('login');
    Route::get('/login', 'login')->name('login');
    Route::get('/signup', 'signup')->name('signup');
    Route::get('/reset-password/{token}', function () {
        return view('welcome'); // Or your main app view
    })->name('password.reset');
});

// Auth Routes
Route::controller(AuthController::class)->group(function () {

    Route::post('/login', 'loginPost')->name('login.post');

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


    // Fahrzeug Bilder
    Route::get('/storage/cars/{filename}', function ($filename) {
        $path = storage_path('app/cars/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    })->name('cars.image');


    Route::controller(PageController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/kalender', 'calendar')->name('calendar');
        Route::get('/fahrzeuge', 'cars')->name('fahrzeuge');
        Route::get('/fahrzeuge/fahrzeugdetails/{kennzeichen}', function ($kennzeichen) {
            return view('pages.cardetails', ['kennzeichen' => $kennzeichen]);
        },)->name('cardetails');
        Route::get('/kunden', 'customers')->name('kunden');
        Route::get('/kunden/kundendetails/{id}', function ($id) {
            return view('pages.customerdetails', ['id' => $id]);
        })->name('customerdetails');
        Route::get('/auftraege', 'jobs')->name('jobs');
        Route::get('/auftraege/jobdetails/{id}', 'jobdetails')->name('jobdetails');
        
        Route::get('/berichte', 'reports')->name('reports');
        Route::get('/profil', 'profile')->name('profile');
        Route::get('/einstellungen', 'settings')->name('settings');
});
});
