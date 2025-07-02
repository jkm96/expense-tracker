<?php

namespace App\Services\Dashboard;

use App\Models\Expense;
use Carbon\Carbon;

class DashboardService implements DashboardServiceInterface
{
    public function getDashboardData(int $userId): array
    {
        $now = Carbon::now();

        $totalExpenses = Expense::where('user_id', $userId)
            ->sum('amount');

        $monthlyTotal = Expense::where('user_id', $userId)
            ->whereMonth('date', $now->month)
            ->sum('amount');

        $yearlyTotal = Expense::where('user_id', $userId)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $topCategoryData = Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $topCategory = $topCategoryData->category ?? null;
        $topCategoryTotal = $topCategoryData->total ?? 0;

        $expenses = Expense::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return [
            'totalExpenses' => $totalExpenses,
            'monthlyTotal' => $monthlyTotal,
            'currentMonth' => $now->format('F'),
            'yearlyTotal' => $yearlyTotal,
            'topCategory' => $topCategory,
            'topCategoryTotal' => $topCategoryTotal,
            'expenses' => $expenses,
        ];
    }
}
