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
    ];

    protected $casts = [
        'frequency' => ExpenseFrequency::class,
        'start_date' => 'datetime',
        'last_processed_at' => 'datetime',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
