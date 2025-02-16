<?php

use App\Utils\Helpers\CategoryHelper;

if (!function_exists('get_category_color')) {
    function get_category_color(string $category): string
    {
        return CategoryHelper::getCategoryColor($category);
    }
}
