<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AssociationAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssociationController;

// Public Auth Routes
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('association/register', [AssociationAuthController::class, 'register']);
Route::post('association/login', [AssociationAuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // Shared
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);

    // User Profile
    Route::prefix('me')->group(function () {
        Route::put('/', function (Request $request) {
            return app(UserController::class)->update($request, $request->user());
        });
        Route::get('/', function (Request $request) {
            return app(UserController::class)->show($request->user());
        });
        Route::delete('/', [UserController::class, 'destroySelf']);
    });

    // Association Profile
    Route::prefix('my-association')->group(function () {
        Route::put('/', [AssociationController::class, 'apiUpdate']);
        Route::delete('/', [AssociationController::class, 'apiDestroy']);
    });

    // Public Data
    Route::get('associations', [AssociationController::class, 'apiIndex']);
    Route::get('associations/{association}', [AssociationController::class, 'apiShow']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/users/deleted', [UserController::class, 'deletedUsers']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('associations', AssociationController::class)->except(['index', 'show']);
    Route::post('/users/{user}/restore', [UserController::class, 'restore']);
});
