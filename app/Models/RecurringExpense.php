<?php

namespace App\Models;

use App\Utils\Enums\ExpenseFrequency;
use Illuminate\Database\Eloquent\Model;

class RecurringExpense extends Model
{
    protected $fillable = ['expense_id', 'start_date', 'frequency', 'is_active'];

    protected $casts = [
        'frequency' => ExpenseFrequency::class,
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
