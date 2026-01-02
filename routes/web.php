<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Note: Filament admin panel routes are automatically registered
// Filament handles all /admin/* routes including authentication
// Access the admin panel at /admin
