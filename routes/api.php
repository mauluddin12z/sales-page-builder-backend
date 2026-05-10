<?php

use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\SalesPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

/*
| Auth — Google Only
*/

Route::prefix('auth')->group(function () {
    Route::get('redirect/google', [GoogleController::class, 'redirect']);
    Route::get('callback/google', [GoogleController::class, 'callback']);
});

/*
| Protected Routes
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $r) => $r->user());
    Route::apiResource('sales-pages', SalesPageController::class);
    Route::post('/generate-sales-page', [AiController::class, 'generate']);
    Route::post('/regenerate-sales-page', [AiController::class, 'regenerate']);
    Route::prefix('ai')
        ->middleware([
            'throttle:30,1',
        ])
        ->group(function () {

            /*
            | Full Generation
            */

            Route::post(
                '/generate',
                [AiController::class, 'generate']
            );

            /*
            | Partial Regeneration
            */

            Route::post(
                '/regenerate',
                [AiController::class, 'regenerate']
            );
        });
});
