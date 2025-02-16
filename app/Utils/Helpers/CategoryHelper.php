<?php

namespace App\Utils\Helpers;

class CategoryHelper
{
    public static function getCategoryColor(string $category): string
    {
        // Define category color mapping
        $categoryColors = [
            'food' => 'bg-green-400',
            'transport' => 'bg-blue-400',
            'rent' => 'bg-orange-400',
            'utilities' => 'bg-yellow-400',
            'entertainment' => 'bg-red-400',
            'other' => 'bg-gray-400',
        ];

        // Return matching color or default to gray
        return $categoryColors[$category] ?? 'bg-gray-300';
    }
}
