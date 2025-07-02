<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\User;
use App\Models\UserVerification;
use App\Observers\ModelActivityObserver;
use App\Services\Audit\AuditLogService;
use App\Services\Audit\AuditLogServiceInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Dashboard\DashboardService;
use App\Services\Dashboard\DashboardServiceInterface;
use App\Services\Expense\ExpenseService;
use App\Services\Expense\ExpenseServiceInterface;
use App\Services\Notifications\NotificationService;
use App\Services\Notifications\NotificationServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(ExpenseServiceInterface::class, ExpenseService::class);
        $this->app->bind(AuditLogServiceInterface::class, AuditLogService::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $models = [
            Expense::class,
            User::class,
            UserVerification::class,
            RecurringExpense::class
        ];

        foreach ($models as $model) {
            $model::observe(ModelActivityObserver::class);
        }
    }
}
