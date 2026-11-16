<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// The home address just sends people to the dashboard
// (and the "auth" gatekeeper sends visitors who aren't logged in to the login page)
Route::get('/', fn () => redirect()->route('dashboard'));

// Only for people who are NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Only for people who ARE logged in
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});