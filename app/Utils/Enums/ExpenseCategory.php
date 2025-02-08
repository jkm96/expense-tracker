<?php

namespace App\Utils\Enums;

enum ExpenseCategory: string
{
    case FOOD = 'food';
    case TRANSPORT = 'transport';
    case RENT = 'rent';
    case UTILITIES = 'utilities';
    case ENTERTAINMENT = 'entertainment';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
