<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

// Kunden
Route::get('/kunden', [CustomerController::class, 'index']);
Route::post('/kunden', [CustomerController::class, 'store']);
Route::get('/kunden/{id}', [CustomerController::class, 'edit']);
Route::put('/kunden/{id}', [CustomerController::class, 'update']);
Route::delete('/kunden/{id}', [CustomerController::class, 'destroy']);

Route::get('/fahrzeuge', [CarController::class, 'index']);
Route::post('/fahrzeuge', [CarController::class, 'store']);
Route::get('/fahrzeuge', [CarController::class, 'edit']);
Route::put('/fahrzeuge', [CarController::class, 'update']);
Route::delete('/fahrzeuge', [CarController::class, 'destroy']);


Route::post('/profil', [EmployeeController::class, 'store']);
