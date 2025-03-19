<?php

namespace App\Models;

use App\Utils\Enums\ExpenseFrequency;
use Illuminate\Database\Eloquent\Model;

class RecurringExpense extends Model
{
    protected $fillable = [
        'expense_id',
        'user_id',
        'start_date',
        'frequency',
        'is_active',
        'last_processed_at',
        'next_process_at',
    ];

    protected $casts = [
        'frequency' => ExpenseFrequency::class,
        'start_date' => 'datetime',
        'last_processed_at' => 'datetime',
        'next_process_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function generatedExpenses()
    {
        return $this->hasMany(Expense::class, 'recurring_expense_id');
    }
}
