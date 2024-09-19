<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return view('login');
});


Route::get('/test', function () {
    return view('test');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/kalender', function () {
    return view('calendar');
});

Route::get('/fahrzeuge', function () {
    return view('cars');
});

Route::get('/auftraege', function () {
    return view('jobs');
});

Route::get('/berichte', function () {
    return view('reports');
});

Route::get('/chat', function () {
    return view('chat');
});

Route::get('/profil', function () {
    return view('profile');
});

Route::get('/einstellungen', function () {
    return view('settings');
});

Route::get('/abmelden', function () {
    return view('logout');
});




Route::get('/new', 'TestController@controllerMethod');
