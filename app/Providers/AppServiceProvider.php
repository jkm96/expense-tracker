<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\User;
use App\Models\UserVerification;
use App\Observers\ModelActivityObserver;
use App\Services\Audit\AuditLogService;
use App\Services\Audit\AuditLogServiceInterface;
use App\Services\Expense\ExpenseService;
use App\Services\Expense\ExpenseServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExpenseServiceInterface::class, ExpenseService::class);
        $this->app->bind(AuditLogServiceInterface::class, AuditLogService::class);
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
