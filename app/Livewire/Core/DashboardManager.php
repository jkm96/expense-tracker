<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Utils\Enums\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardManager extends Component
{
    public function render()
    {
        $userId = Auth::id();

        // Get total expenses
        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');

        // Get this month expenses
        $monthlyTotal = Expense::where('user_id', $userId)
            ->whereMonth('date', Carbon::now()->month)
            ->sum('amount');

        // Get this year expenses
        $yearlyTotal = Expense::where('user_id', $userId)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');

        // Get top spending category
        $topCategoryData = Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $topCategory = $topCategoryData ? $topCategoryData->category : null;
        $topCategoryTotal = $topCategoryData ? $topCategoryData->total : 0;

        // Get expenses based on filters
        $query = Expense::where('user_id', $userId);
        $expenses = $query->orderBy('date', 'desc')->take(5)->get();

        return view('livewire.core.dashboard-manager', compact(
            'totalExpenses',
            'monthlyTotal',
            'yearlyTotal',
            'topCategory',
            'topCategoryTotal',
            'expenses'
        ));
    }
}
