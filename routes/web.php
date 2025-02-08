<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoreController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [CoreController::class, 'home_page'])->name('home');

    // User Authentication Routes
    Route::get('register', [AuthController::class, 'register'])->name('register.user');
    Route::get('login', [AuthController::class, 'login'])->name('login.user');
    Route::get('verify-account', [AuthController::class, 'verify'])->name('email.verification');
});

Route::group(['middleware' => ['auth:web']], function () {
    Route::get('/my-dashboard', [DashboardController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/manage-expenses', [CoreController::class, 'expense_page'])->name('expense.manage');
});
