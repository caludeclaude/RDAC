<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;


Route::get('/', fn () => redirect()->route('dashboard'));

// Only for people who are NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
    ->middleware('throttle:10,1')      // also limits each visitor to 10 clicks a minute
    ->name('password.email');
});

// Only for people who ARE logged in
Route::middleware('auth')->group(function () {

    // Always reachable, even with a temporary password
    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [PasswordController::class, 'update'])
    ->middleware('throttle:5,15')       // at most 5 tries every 15 minutes
    ->name('password.update');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Everything below needs the password to have been changed first
    Route::middleware('force.password.change')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    
    // Super Admin only
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::view('/archive', 'placeholder', ['title' => 'Archive'])->name('archive.index');
        });
    });
});