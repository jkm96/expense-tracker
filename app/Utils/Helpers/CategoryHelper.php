<?php

namespace App\Utils\Helpers;

class CategoryHelper
{
    public static function getCategoryColor(string $category): string
    {
        $categoryColors = [
            'food' => 'bg-green-400',        // #4ade80
            'transport' => 'bg-blue-400',    // #60a5fa
            'clothing' => 'bg-orange-400',   // #fb923c
            'utilities' => 'bg-yellow-400',  // #facc15
            'knowledge' => 'bg-red-400',     // #f87171
            'lifestyle' => 'bg-gray-900',       // #0b0c0c
            'other' => 'bg-gray-400'         // #9ca3af
        ];

        return $categoryColors[$category] ?? 'bg-gray-300';
    }
}
