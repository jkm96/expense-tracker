<?php

namespace App\Utils\Helpers;

use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ExpenseHelper
{
    public static function calculateNextProcessTime(string $expenseName,ExpenseFrequency $frequency, string $startDate, string $lastProcessed, array $scheduleConfig = []): Carbon
    {
        $lastProcessed = Carbon::parse($lastProcessed);
        $originalTime = Carbon::parse($startDate)->format('H:i:s');
        return match ($frequency) {
            ExpenseFrequency::DAILY => self::getNextDailyProcessTime($lastProcessed, $scheduleConfig, $originalTime),
            ExpenseFrequency::WEEKLY => self::getNextWeeklyProcessTime($lastProcessed, $scheduleConfig, $originalTime),
            ExpenseFrequency::MONTHLY => self::getNextMonthlyProcessTime($lastProcessed, $scheduleConfig, $originalTime),
            default => throw new InvalidArgumentException("Invalid frequency"),
        };
    }

    private static function getNextDailyProcessTime(Carbon $lastProcessed, array $scheduleConfig,string $timeOfDay): Carbon
    {
        $days = $scheduleConfig['days'] ?? [];
        $validDays = collect($days)->map(fn($d) => strtolower($d))->toArray();

        $next = $lastProcessed->copy()->addDay()->setTimeFromTimeString($timeOfDay);

        for ($i = 0; $i < 7; $i++) {
            if (in_array(strtolower($next->format('l')), $validDays)) {
                return $next;
            }

            $next->addDay();
        }

        return $lastProcessed->copy()->addDay()->setTimeFromTimeString($timeOfDay);
    }

    private static function getNextWeeklyProcessTime(Carbon $lastProcessed, array $scheduleConfig, string $timeOfDay): Carbon
    {
        $dayOfWeek = $scheduleConfig['day_of_week'] ?? null;

        if ($dayOfWeek) {
            $next = Carbon::parse("next $dayOfWeek", $lastProcessed->timezone)->setTimeFromTimeString($timeOfDay);
            return $next;
        }

        return $lastProcessed->copy()->addWeek()->setTimeFromTimeString($timeOfDay);
    }

    private static function getNextMonthlyProcessTime(Carbon $lastProcessed, array $scheduleConfig, string $timeOfDay): Carbon
    {
        $dayOfMonth = $scheduleConfig['day_of_month'] ?? null;

        if ($dayOfMonth) {
            $next = $lastProcessed->copy()->addMonth()->setDay($dayOfMonth)->setTimeFromTimeString($timeOfDay);
            return $next;
        }

        return $lastProcessed->copy()->addMonth()->setTimeFromTimeString($timeOfDay);
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
