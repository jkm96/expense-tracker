<?php

namespace App\Utils\Helpers;

use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use Carbon\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ExpenseHelper
{
    public static function calculateNextProcessTime(ExpenseFrequency $frequency, Carbon $lastProcessed, array $scheduleConfig = []): Carbon
    {
        return match ($frequency) {
            ExpenseFrequency::DAILY => self::getNextDailyProcessTime($lastProcessed, $scheduleConfig),
            ExpenseFrequency::WEEKLY => self::getNextWeeklyProcessTime($lastProcessed, $scheduleConfig),
            ExpenseFrequency::MONTHLY => self::getNextMonthlyProcessTime($lastProcessed, $scheduleConfig),
            ExpenseFrequency::YEARLY => $lastProcessed->copy()->addYear(),
            default => throw new InvalidArgumentException("Invalid frequency"),
        };
    }

    private static function getNextDailyProcessTime(Carbon $lastProcessed, array $scheduleConfig): Carbon
    {
        $days = $scheduleConfig['days'] ?? [];
        if (empty($days)) {
            return $lastProcessed->copy()->addDay(); // Default to the next day
        }

        $nextDay = collect($days)
            ->map(fn($day) => Carbon::parse($day)) // Convert days to Carbon instances
            ->first(fn($date) => $date->isFuture()); // Find the next available date

        return $nextDay ? $nextDay->setTimeFrom($lastProcessed) : $lastProcessed->copy()->addDay();
    }

    private static function getNextWeeklyProcessTime(Carbon $lastProcessed, array $scheduleConfig): Carbon
    {
        $dayOfWeek = $scheduleConfig['day_of_week'] ?? null;
        return $dayOfWeek ? Carbon::parse("next $dayOfWeek")->setTimeFrom($lastProcessed) : $lastProcessed->copy()->addWeek();
    }

    private static function getNextMonthlyProcessTime(Carbon $lastProcessed, array $scheduleConfig): Carbon
    {
        $dayOfMonth = $scheduleConfig['day_of_month'] ?? null;
        return $dayOfMonth ? $lastProcessed->copy()->addMonth()->day($dayOfMonth) : $lastProcessed->copy()->addMonth();
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
