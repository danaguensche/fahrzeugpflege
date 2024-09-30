<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Login Routes
Route::get('/login', [AuthController::class, "login" ]);
Route::post("/login", [AuthController::class, "loginPost"])
->name("login.post");


//User-Pages
Route::get('/dashboard', function () {
    return view('pages/dashboard');
});

Route::get('/kalender', function () {
    return view('pages/calendar');
});

Route::get('/fahrzeuge', function () {
    return view('pages/cars');
});

Route::get('/auftraege', function () {
    return view('pages/jobs');
});

Route::get('/berichte', function () {
    return view('pages/reports');
});

Route::get('/chat', function () {
    return view('/pageschat');
});

Route::get('/profil', function () {
    return view('pages/profile');
});

Route::get('/einstellungen', function () {
    return view('pages/settings');
});

Route::get('/abmelden', function () {
    return view('logout');
});

Route::get('/new', 'TestController@controllerMethod');


