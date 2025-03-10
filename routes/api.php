<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/', function (Request $request) {
//     return 'API';
// });

Route::apiResource('cars', CarController::class);

Route::apiResource('rentals', RentalController::class);

Route::apiResource('payments', PaymentController::class);
