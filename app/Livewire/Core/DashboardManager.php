<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardManager extends Component
{
    public function render()
    {
        $userId = Auth::id();

        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');

        $monthlyTotal = Expense::where('user_id', $userId)
            ->whereMonth('date', Carbon::now()->month)
            ->sum('amount');
        $currentMonth = Carbon::now()->format('F');

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
            'currentMonth',
            'yearlyTotal',
            'topCategory',
            'topCategoryTotal',
            'expenses'
        ));
    }
}
