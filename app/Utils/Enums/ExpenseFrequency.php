<?php

namespace App\Utils\Enums;

enum ExpenseFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
