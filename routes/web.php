<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\AuthController;

/*Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard');
});
*/

// Route pour la connexion d'un utilisateur
// Route::post('/user/login', [AuthController::class, 'userLogin'])->name('user.login');

// // Route pour la connexion d'une association
// Route::post('/association/login', [AuthController::class, 'associationLogin'])->name('association.login');

// Route::post('/register', [AuthController::class, 'register']);

// app/Http/Middleware/VerifyCsrfToken.php


// User Routes
Route::get('/', function () {
    return redirect()->route('users.index');
});

Route::resource('users', UserController::class);

// Association Routes
Route::resource('associations', AssociationController::class);

// Additional Association Routes
Route::prefix('associations/{association}')->group(function () {
    Route::post('verify', [AssociationController::class, 'verify'])->name('associations.verify');
    Route::post('reject', [AssociationController::class, 'reject'])->name('associations.reject');
    Route::post('upload-documents', [AssociationController::class, 'uploadDocuments'])
        ->name('associations.upload-documents');
});

// Authentication Routes (if needed)
//Auth::routes();
