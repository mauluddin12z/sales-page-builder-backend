<?php

use App\Http\Controllers\Api\AiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SalesPageController;

// PUBLIC ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // SALES PAGES CRUD (API)
    Route::apiResource('sales-pages', SalesPageController::class);

    Route::post('/generate-sales-page', [AiController::class, 'generate']);
    Route::post('/regenerate-sales-page', [AiController::class, 'regenerate']);
});
