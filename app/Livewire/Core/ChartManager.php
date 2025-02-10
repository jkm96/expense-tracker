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
    public $filterMonth;

    public function mount()
    {
        $this->filterMonth = Carbon::now()->format('Y-m'); // ✅ Default: Current Month
    }

    public function filterExpenses()
    {
        $this->dispatch('expensesUpdated', $this->generateChartData($this->filterMonth));
    }

    private function generateChartData($filterMonth = null)
    {
        $userId = Auth::id();
        $expenses = Expense::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->get();

        $categories = ExpenseCategory::values();
        $monthlyExpenses = []; // For Line Chart (Yearly Trends)
        $dailyExpenses = [];   // For Bar Chart (Daily Trends in Selected Month)

        foreach ($expenses as $expense) {
            $date = Carbon::parse($expense->date);
            $monthKey = $date->format('Y-m'); // Group for Line Chart
            $dayKey = $date->format('Y-m-d'); // Group for Bar Chart
            $category = $expense->category->value;

            // **Group by Month (For Yearly Line Chart - No Filter)**
            if (!isset($monthlyExpenses[$monthKey][$category])) {
                $monthlyExpenses[$monthKey][$category] = 0;
            }
            $monthlyExpenses[$monthKey][$category] += $expense->amount;

            // **Group by Day (For Monthly Bar Chart - Strict Filter)**
            if ($filterMonth && $date->format('Y-m') === $filterMonth) {
                if (!isset($dailyExpenses[$dayKey][$category])) {
                    $dailyExpenses[$dayKey][$category] = 0;
                }
                $dailyExpenses[$dayKey][$category] += $expense->amount;
            }
        }

        // ✅ Prepare Line Chart Data (Yearly Trends)
        $lineLabels = [];
        $lineSeries = [];

        foreach ($categories as $category) {
            $lineSeries[$category] = [];
        }

        foreach ($monthlyExpenses as $month => $categoryData) {
            $lineLabels[] = Carbon::createFromFormat('Y-m', $month)->format('F Y');

            foreach ($categories as $category) {
                $lineSeries[$category][] = $categoryData[$category] ?? 0;
            }
        }

        $formattedLineSeries = array_map(fn ($category) => [
            'name' => ucfirst($category),
            'data' => $lineSeries[$category]
        ], $categories);

        // ✅ Prepare Bar Chart Data (Strictly Within Selected Month)
        $barLabels = [];
        $barSeries = [];

        foreach ($categories as $category) {
            $barSeries[$category] = [];
        }

        foreach ($dailyExpenses as $day => $categoryData) {
            $barLabels[] = Carbon::createFromFormat('Y-m-d', $day)->format('j M');

            foreach ($categories as $category) {
                $barSeries[$category][] = $categoryData[$category] ?? 0;
            }
        }

        $formattedBarSeries = array_map(fn ($category) => [
            'name' => ucfirst($category),
            'data' => $barSeries[$category]
        ], $categories);

        return [
            'barLabels' => json_encode($barLabels, JSON_UNESCAPED_UNICODE),
            'barSeries' => json_encode($formattedBarSeries, JSON_UNESCAPED_UNICODE),
            'lineLabels' => json_encode($lineLabels, JSON_UNESCAPED_UNICODE),
            'lineSeries' => json_encode($formattedLineSeries, JSON_UNESCAPED_UNICODE),
        ];
    }

    public function render()
    {
        return view('livewire.core.chart-manager', $this->generateChartData($this->filterMonth));
    }
}
