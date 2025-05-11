<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AssociationAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssociationController;
use App\Models\Association;

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
        Route::get('/', function (Request $request) {
            return app(UserController::class)->show($request->user());
        });
        Route::put('/', function (Request $request) {
            return app(UserController::class)->update($request, $request->user());
        });
        Route::delete('/', function (Request $request) {
            return $request->user()->association
                ? app(AssociationController::class)->destroySelf($request)
                : app(UserController::class)->destroySelf($request);
        });
    });

    // Association Profile for logged-in user
    Route::prefix('me/association')->group(function () {
        Route::get('/', function (Request $request) {
            // Get association by user_id instead of relation
            $association = Association::where('user_id', $request->user()->id)->first();

            if (!$association) {
                return response()->json(['error' => 'No association found for this user'], 404);
            }

            return app(AssociationController::class)->show($association);
        });

        Route::put('/', function (Request $request) {
            // Get association by user_id instead of relation
            $association = Association::where('user_id', $request->user()->id)->first();

            if (!$association) {
                return response()->json(['error' => 'No association found for this user'], 404);
            }

            return app(AssociationController::class)->update($request, $association);
        });
    });

    // Public Data
    Route::get('associations', [AssociationController::class, 'index']);
    Route::get('associations/{association}', [AssociationController::class, 'show']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Users Management
    Route::get('/users/deleted', [UserController::class, 'deletedUsers']);

    Route::post('/users/{user}/restore', [UserController::class, 'restore']);
    Route::apiResource('users', UserController::class);

    // Associations Management
    Route::get('/associations/trashed/all', [AssociationController::class, 'deletedAssociations']);
    Route::post('/associations/{association}/restore', [AssociationController::class, 'restore']);
    Route::delete('/associations/{association}/force', [AssociationController::class, 'forceDelete']);
    Route::apiResource('associations', AssociationController::class)->except(['index', 'show']);
});
