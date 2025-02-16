<?php

namespace App\Utils\Enums;

enum ExpenseCategory: string
{
    case FOOD = 'food';
    case TRANSPORT = 'transport';
    case CLOTHING = 'clothing';
    case UTILITIES = 'utilities';
    case KNOWLEDGE = 'knowledge';
    case LIFESTYLE = 'lifestyle';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
