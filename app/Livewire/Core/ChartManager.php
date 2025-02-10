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
            $this->customDate = null;
        }
        $this->filterExpenses(); // Ensure filters apply immediately
    }

    public function updatedFilterCategory()
    {
        $this->filterExpenses(); // Ensure expenses update when category changes
    }

    public function filterExpenses()
    {
        $this->dispatch('expensesUpdated', $this->generateChartData());
    }

    public function resetFilters()
    {
        $this->filterDate = 'this_month';
        $this->filterCategory = 'all';
        $this->customDate = null;
        $this->dispatch('expensesUpdated', $this->generateChartData());
    }

    private function generateChartData()
    {
        $userId = Auth::id();
        $expenses = Expense::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->get();

        $categories = ExpenseCategory::values(); // ['food', 'transport', 'rent', 'utilities', 'entertainment', 'other']
        $monthlyCategories = [];

        // 1️⃣ Group expenses by Month & Category
        foreach ($expenses as $expense) {
            $month = Carbon::parse($expense->date)->format('Y-m');
            $category = $expense->category->value; // Convert enum to string

            if (!isset($monthlyCategories[$month][$category])) {
                $monthlyCategories[$month][$category] = 0;
            }
            $monthlyCategories[$month][$category] += $expense->amount;
        }

        // 2️⃣ Prepare chart labels (X-axis) and data structure
        $chartLabels = []; // X-axis months
        $chartSeries = []; // Bar chart series
        $lineSeries = [];  // Line chart series

        // Initialize each category with an empty array
        foreach ($categories as $category) {
            $chartSeries[$category] = [];
            $lineSeries[$category] = [];
        }

        // 3️⃣ Populate data for each month
        foreach ($monthlyCategories as $month => $categoryData) {
            $chartLabels[] = Carbon::createFromFormat('Y-m', $month)->format('F Y');

            // Ensure each category has a value (default to 0 if missing)
            foreach ($categories as $category) {
                $chartSeries[$category][] = $categoryData[$category] ?? 0;
                $lineSeries[$category][] = $categoryData[$category] ?? 0;
            }
        }

        // 4️⃣ Convert `chartSeries` into ApexCharts format
        $formattedChartSeries = array_map(fn ($category) => [
            'name' => ucfirst($category),
            'data' => $chartSeries[$category]
        ], $categories);

        // 5️⃣ Convert `lineSeries` into ApexCharts format
        $formattedLineSeries = array_map(fn ($category) => [
            'name' => ucfirst($category),
            'data' => $lineSeries[$category]
        ], $categories);

        // 6️⃣ Return structured data for ApexCharts
        return [
            'chartLabels' => json_encode($chartLabels, JSON_UNESCAPED_UNICODE),
            'chartSeries' => json_encode($formattedChartSeries, JSON_UNESCAPED_UNICODE),
            'lineSeries' => json_encode($formattedLineSeries, JSON_UNESCAPED_UNICODE),
        ];
    }

    public function render()
    {
        return view('livewire.core.chart-manager', $this->generateChartData());
    }
}
