<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('app');
});

Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
Route::post('/signup', [AuthController::class, 'signupPost'])->name('signup.post');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth:sanctum');

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');