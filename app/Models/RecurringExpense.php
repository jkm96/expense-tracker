<?php

namespace App\Models;

use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use Illuminate\Database\Eloquent\Model;

class RecurringExpense extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'category',
        'notes',
        'start_date',
        'frequency',
        'schedule_config',
        'is_active',
        'last_processed_at',
        'next_process_at',
    ];

    protected $casts = [
        'frequency' => ExpenseFrequency::class,
        'schedule_config' => 'array',
        'category' => ExpenseCategory::class,
        'start_date' => 'datetime',
        'last_processed_at' => 'datetime',
        'next_process_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function generatedExpenses()
    {
        return $this->hasMany(Expense::class, 'recurring_expense_id');
    }
}
