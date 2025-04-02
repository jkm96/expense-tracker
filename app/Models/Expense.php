<?php

namespace App\Models;

use App\Utils\Enums\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recurring_expense_id',
        'name',
        'amount',
        'date',
        'category',
        'notes'
    ];

    /**
     * Get the user owning the expense
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'amount' => 'decimal:2',
        'category' => ExpenseCategory::class,
    ];

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->format('D, jS M Y');
    }
}
