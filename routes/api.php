<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RealEstateOfficeController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Emlak Ofisi rotaları
Route::prefix('offices')->group(function () {
    Route::get('/', [RealEstateOfficeController::class, 'index']);
    Route::post('/', [RealEstateOfficeController::class, 'store']);
    Route::get('/{office}', [RealEstateOfficeController::class, 'show']);
    Route::put('/{office}', [RealEstateOfficeController::class, 'update']);
    Route::delete('/{office}', [RealEstateOfficeController::class, 'destroy']);
    Route::get('/cities', [RealEstateOfficeController::class, 'cities']);
    Route::get('/districts', [RealEstateOfficeController::class, 'districts']);
});

// Emlakçı rotaları
Route::prefix('agents')->group(function () {
    Route::get('/', [AgentController::class, 'index']);
    Route::post('/', [AgentController::class, 'store']);
    Route::get('/{agent}', [AgentController::class, 'show']);
    Route::put('/{agent}', [AgentController::class, 'update']);
    Route::delete('/{agent}', [AgentController::class, 'destroy']);
});

// İlan rotaları
Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'index']);
    Route::post('/', [PropertyController::class, 'store']);
    Route::get('/{property}', [PropertyController::class, 'show']);
    Route::put('/{property}', [PropertyController::class, 'update']);
    Route::delete('/{property}', [PropertyController::class, 'destroy']);
    Route::post('/{property}/view', [PropertyController::class, 'incrementView']);
    Route::post('/{property}/favorite', [PropertyController::class, 'toggleFavorite']);
});

// Müşteri rotaları
Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::get('/{customer}', [CustomerController::class, 'show']);
    Route::put('/{customer}', [CustomerController::class, 'update']);
    Route::delete('/{customer}', [CustomerController::class, 'destroy']);
    Route::post('/{customer}/viewed-properties', [CustomerController::class, 'addViewedProperty']);
    Route::post('/{customer}/favorite-properties', [CustomerController::class, 'toggleFavoriteProperty']);
}); 