<?php

namespace App\Utils\Helpers;

use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use Carbon\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ExpenseHelper
{
    public static function calculateNextProcessTime(ExpenseFrequency $frequency, Carbon $lastProcessed): Carbon
    {
        return match ($frequency) {
            ExpenseFrequency::DAILY => $lastProcessed->copy()->addDay(),
            ExpenseFrequency::WEEKLY => $lastProcessed->copy()->addWeek(),
            ExpenseFrequency::MONTHLY => $lastProcessed->copy()->addMonth(),
            ExpenseFrequency::YEARLY => $lastProcessed->copy()->addYear(),
            default => throw new InvalidArgumentException("Invalid frequency"),
        };
    }

    public static function generateDefaultNote(string $category, string $name): string
    {
        $formattedName = Str::title($name);

        return match ($category) {
            ExpenseCategory::FOOD->value => "Purchased food items: $formattedName",
            ExpenseCategory::TRANSPORT->value => "Transport expense for: $formattedName",
            ExpenseCategory::CLOTHING->value => "Bought clothing or accessories: $formattedName",
            ExpenseCategory::UTILITIES->value => "Utility payment for: $formattedName",
            ExpenseCategory::KNOWLEDGE->value => "Invested in learning materials: $formattedName",
            ExpenseCategory::LIFESTYLE->value => "Personal purchase: $formattedName",
            ExpenseCategory::OTHER->value => "Miscellaneous expense: $formattedName",
            default => "Expense recorded for $formattedName",
        };
    }
}
