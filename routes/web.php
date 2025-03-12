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
    Route::get('/chart-data/monthly', [DashboardController::class, 'getMonthlyChartData'])->name('chart.data.monthly');
    Route::get('/chart-data/yearly', [DashboardController::class, 'getYearlyChartData'])->name('chart.data.yearly');
    Route::get('/chart-data/pie', [DashboardController::class, 'getPieChartData'])->name('chart.data.pie');

    Route::get('/manage-expenses', [CoreController::class, 'expense_page'])->name('expense.manage');
    Route::get('/manage-recurring-expenses', [CoreController::class, 'recurring_expense_page'])->name('recurring.expense.manage');
    Route::get('/manage/settings', [CoreController::class, 'settings_page'])->name('settings.manage');
});
