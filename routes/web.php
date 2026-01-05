<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MinioTestController;
use App\Http\Controllers\FileProxyController;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// File proxy route - generates fresh presigned URL on every access
Route::get('/file/{path}', [FileProxyController::class, 'stream'])
    ->where('path', '.*')
    ->name('file.proxy');

// Alternative: redirect route (if you prefer redirect instead of streaming)
// Route::get('/file-redirect/{path}', [FileProxyController::class, 'show'])
//     ->where('path', '.*')
//     ->name('file.proxy.redirect');

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

// Minio Test Routes (for testing file upload to Minio)
Route::prefix('minio-test')->group(function () {
    Route::get('/', [MinioTestController::class, 'index'])->name('minio-test.index');
    Route::post('/upload', [MinioTestController::class, 'upload'])->name('minio-test.upload');
    Route::get('/files', [MinioTestController::class, 'listFiles'])->name('minio-test.files');
    Route::delete('/delete', [MinioTestController::class, 'delete'])->name('minio-test.delete');
    Route::get('/test-connection', [MinioTestController::class, 'testConnection'])->name('minio-test.connection');
});

// Note: Filament admin panel routes are automatically registered
// Filament handles all /dashboard/* routes including authentication
// Access the admin panel at /dashboard
