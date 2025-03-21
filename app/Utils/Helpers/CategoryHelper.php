<?php

namespace App\Utils\Helpers;

use App\Utils\Enums\ExpenseCategory;

class CategoryHelper
{
    public static function getCategoryColor(ExpenseCategory $category): array
    {
        $categoryColors = [
            ExpenseCategory::FOOD->value => ['bg-green-400', 'bg-green-500'],
            ExpenseCategory::TRANSPORT->value => ['bg-blue-400', 'bg-blue-500'],
            ExpenseCategory::CLOTHING->value => ['bg-orange-400', 'bg-orange-500'],
            ExpenseCategory::UTILITIES->value => ['bg-yellow-400', 'bg-yellow-500'],
            ExpenseCategory::KNOWLEDGE->value => ['bg-red-400', 'bg-red-500'],
            ExpenseCategory::LIFESTYLE->value => ['bg-gray-800', 'bg-gray-900'],
            ExpenseCategory::OTHER->value => ['bg-gray-400', 'bg-gray-500']
        ];

        return $categoryColors[$category->value] ?? ['bg-gray-300', 'bg-gray-400'];
    }
}
