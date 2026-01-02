<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Public routes
Route::get('/', function () {
    // Redirect to login page
    return redirect('/login');
});

// Authentication routes for participants
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});


// Note: Filament admin panel routes are automatically registered
// Filament handles all /dashboard/* routes including authentication
// Access the admin panel at /dashboard
