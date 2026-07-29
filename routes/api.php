<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('', [ProductController::class, 'index']);
            Route::post('', [ProductController::class, 'store']);
            Route::get('trashed', [ProductController::class, 'trashed']); // biarkan di posisi ini agar tidak tabrakan dengan route GET /{product}
            Route::get('{product}', [ProductController::class, 'show']);
            Route::put('{product}', [ProductController::class, 'update']);
            Route::delete('{product}', [ProductController::class, 'destroy']);
            Route::patch('{product}/restore', [ProductController::class, 'restore']);
            Route::delete('{product}/delete-permanent', [ProductController::class, 'permanentlyDelete']);
        });
    });
