<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AssociationAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\ChatController;
use App\Models\Association;

// Public Auth Routes
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('association/register', [AssociationAuthController::class, 'register']);
Route::post('association/login', [AssociationAuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // Donations Management
    Route::post('/offers', [OffreController::class, 'store']);
    Route::get('/association/{associationId}/offers', [OffreController::class, 'getAssociationOffers']);
    Route::patch('/offers/{offerId}/status', [OffreController::class, 'updateOfferStatus']);

    // Chat Routes (User side)
    Route::get('/chat/association/{associationId}', [ChatController::class, 'getConversation']);
    Route::post('/chat/association/{associationId}/send', [ChatController::class, 'sendToAssociation']);
    Route::post('/chat/mark-read/{senderId}', [ChatController::class, 'markAsRead']);
    Route::get('/user/messages', [ChatController::class, 'getUserReceivedMessages']);

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
            $association = Association::where('user_id', $request->user()->id)->first();
            if (!$association) {
                return response()->json(['error' => 'No association found for this user'], 404);
            }
            return app(AssociationController::class)->show($association);
        });

        Route::put('/', function (Request $request) {
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

// Association-specific Routes
Route::middleware(['auth:sanctum', 'association'])->group(function () {
    // Association can send messages to users
    Route::post('/chat/user/{userId}/send', [ChatController::class, 'sendToUser']);
    Route::get('/association/messages', [ChatController::class, 'getAssociationReceivedMessages']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Users Management
    Route::get('/users/deleted', [UserController::class, 'deletedUsers']);
    Route::post('/users/{user}/restore', [UserController::class, 'restore']);
    Route::delete('/users/{user}/force', [UserController::class, 'forceDestroy']);
    Route::apiResource('users', UserController::class);

    // Associations Management
    Route::get('/associations/trashed/all', [AssociationController::class, 'deletedAssociations']);
    Route::post('/associations/{association}/restore', [AssociationController::class, 'restore']);
    Route::delete('/associations/{association}/force', [AssociationController::class, 'forceDelete']);
    Route::apiResource('associations', AssociationController::class)->except(['index', 'show']);
});
