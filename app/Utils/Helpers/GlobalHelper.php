<?php

use App\Utils\Enums\ExpenseCategory;
use App\Utils\Helpers\CategoryHelper;

if (!function_exists('get_category_color')) {
    function get_category_color(ExpenseCategory $category): array
    {
        return CategoryHelper::getCategoryColor($category);
    }
}
