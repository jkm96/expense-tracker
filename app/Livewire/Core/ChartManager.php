<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Utils\Enums\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class ChartManager extends Component
{
    public $filterDate = 'this_month';
    #[Url]
    public $filterCategory = 'all';
    public $customDate;

    public $categories = [];

    public function mount()
    {
        $this->categories = ExpenseCategory::cases();
    }
    public function updatedFilterDate()
    {
        if ($this->filterDate !== 'custom') {
            $this->customDate = null; // Reset custom date when other options are selected
        }
    }

    public function resetFilters()
    {
        $this->filterDate = 'this_month';
        $this->filterCategory = 'all';
    }

    public function render()
    {
        $userId = Auth::id();
        $query = Expense::where('user_id', $userId);

        // Apply date filters
        if ($this->filterDate == 'this_month') {
            $query->whereMonth('date', Carbon::now()->month);
        } elseif ($this->filterDate == 'last_month') {
            $query->whereMonth('date', Carbon::now()->subMonth()->month);
        } elseif ($this->filterDate == 'this_year') {
            $query->whereYear('date', Carbon::now()->year);
        } elseif ($this->filterDate == 'custom' && $this->customDate) {
            $query->whereDate('date', Carbon::parse($this->customDate));
        }

        // Apply category filter
        if ($this->filterCategory !== 'all') {
            $query->where('category', $this->filterCategory);
        }

        $expenses = $query->orderBy('date', 'asc')->get();

        // Prepare data for charts
        $monthlyExpenses = $expenses->groupBy(fn ($expense) => Carbon::parse($expense->date)->format('Y-m'));

        $chartLabels = [];
        $chartData = [];

        foreach ($monthlyExpenses as $month => $monthExpenses) {
            $chartLabels[] = Carbon::createFromFormat('Y-m', $month)->format('F Y');
            $chartData[] = (float) $monthExpenses->sum('amount');
        }

        // Group by Category
        $categoryExpenses = $expenses->groupBy(fn ($expense) => ucfirst($expense->category->value ?? $expense->category));

        $pieLabels = [];
        $pieData = [];

        foreach ($categoryExpenses as $category => $categoryExpense) {
            $pieLabels[] = $category;
            $pieData[] = (float) $categoryExpense->sum('amount');
        }

        return view('livewire.core.chart-manager', [
            'chartLabels' => json_encode($chartLabels, JSON_UNESCAPED_UNICODE),
            'chartData' => json_encode($chartData, JSON_UNESCAPED_UNICODE),
            'pieLabels' => json_encode($pieLabels, JSON_UNESCAPED_UNICODE),
            'pieData' => json_encode($pieData, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
