<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\LandingController;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Authentication routes for participants
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Challenge routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/challenges/{slug}', [ChallengeController::class, 'show'])->name('challenges.show');
    Route::post('/challenges/{challenge}/join', [ChallengeController::class, 'join'])->name('challenges.join');
    Route::post('/challenges/{challenge}/leave', [ChallengeController::class, 'leave'])->name('challenges.leave');

    // Submission routes
    Route::post('/challenges/{challenge}/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
});

// Public submission detail route (for sharing)
Route::get('/challenges/{slug}/submissions/{submission}', [ChallengeController::class, 'showSubmissionDetail'])->name('challenges.submissions.show');

// Note: Filament admin panel routes are automatically registered
// Filament handles all /dashboard/* routes including authentication
// Access the admin panel at /dashboard
