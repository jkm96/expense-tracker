<?php

namespace App\Http\Controllers;


use App\Models\Expense;
use App\Utils\Enums\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('core.dashboard-page');
    }

    public function getMonthlyChartData(Request $request)
    {
        $userId = auth()->id();
        $filterMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $categories = ExpenseCategory::values();
        $weeklyExpenses = [];

        // ✅ Start from the 1st of the selected month
        $startOfMonth = Carbon::parse($filterMonth)->startOfMonth();
        $endOfMonth = Carbon::parse($filterMonth)->endOfMonth();
        $currentWeekStart = $startOfMonth->copy(); //First week starts from the 1st
        $weekNumber = 1;

        while ($currentWeekStart->lte($endOfMonth)) {
            $weekEnd = $currentWeekStart->copy()->addDays(6); //7-day week range
            if ($weekEnd->gt($endOfMonth)) {
                $weekEnd = $endOfMonth; //Ensure last week does not exceed month end
            }

            // Label Example: W1 (1-7), W2 (8-14)
            $weekKey = "W" . str_pad($weekNumber, 2, '0', STR_PAD_LEFT);
            $weekLabel = $weekKey . " (" . $currentWeekStart->format('j') . "-" . $weekEnd->format('j') . ")";

            $weeklyExpenses[$weekKey] = [
                'label' => $weekLabel,
                'data' => array_fill_keys($categories, 0)
            ];

            //Move to next week
            $test = $weekEnd->copy()->addDay();
            $currentWeekStart = $test;
            $weekNumber++;
        }

        // ✅ Fetch expenses grouped by week
        $expenses = Expense::where('user_id', $userId)
            ->whereMonth('date', Carbon::parse($filterMonth)->month)
            ->whereYear('date', Carbon::parse($filterMonth)->year)
            ->orderBy('date', 'asc')
            ->get();

        foreach ($expenses as $expense) {
            $expenseDate = Carbon::parse($expense->date);
            $weekNumber = ceil($expenseDate->day / 7); // ✅ Find week number within the month
            $weekKey = "W" . str_pad($weekNumber, 2, '0', STR_PAD_LEFT);

            if (isset($weeklyExpenses[$weekKey])) {
                $category = $expense->category->value;
                $weeklyExpenses[$weekKey]['data'][$category] += $expense->amount;
            }
        }

        // ✅ Prepare Data for Charts
        $chartLabels = [];
        $chartSeries = [];

        foreach ($categories as $category) {
            $chartSeries[$category] = [];
        }

        foreach ($weeklyExpenses as $week => $data) {
            $chartLabels[] = $data['label']; // ✅ Use new week labels

            foreach ($categories as $category) {
                $chartSeries[$category][] = $data['data'][$category] ?? 0;
            }
        }

        return response()->json([
            'labels' => $chartLabels,
            'series' => array_map(fn($category) => [
                'name' => ucfirst($category),
                'data' => array_pad($chartSeries[$category], count($chartLabels), 0) // ✅ Ensures correct length
            ], $categories),
        ]);
    }

    // 🔹 Fetch Data for Yearly Filter (Months)
    public function getYearlyChartData(Request $request)
    {
        $userId = auth()->id();
        $filterYear = $request->input('year', Carbon::now()->year);
        $categories = ExpenseCategory::values();
        $monthlyExpenses = [];

        // ✅ Generate all months for the year
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = Carbon::create($filterYear, $i, 1)->format('Y-m');
            $monthlyExpenses[$monthKey] = array_fill_keys($categories, 0);
        }

        // ✅ Fetch expenses grouped by month
        $expenses = Expense::where('user_id', $userId)
            ->whereYear('date', $filterYear)
            ->orderBy('date', 'asc')
            ->get();

        foreach ($expenses as $expense) {
            $monthKey = Carbon::parse($expense->date)->format('Y-m');
            $category = $expense->category->value;
            $monthlyExpenses[$monthKey][$category] += $expense->amount;
        }

        // ✅ Prepare Data
        $chartLabels = [];
        $chartSeries = [];

        foreach ($categories as $category) {
            $chartSeries[$category] = [];
        }

        foreach ($monthlyExpenses as $month => $categoryData) {
            $chartLabels[] = Carbon::createFromFormat('Y-m', $month)->format('F');
            foreach ($categories as $category) {
                $chartSeries[$category][] = $categoryData[$category] ?? 0;
            }
        }

        return response()->json([
            'labels' => $chartLabels,
            'series' => array_map(fn($category) => [
                'name' => ucfirst($category),
                'data' => $chartSeries[$category]
            ], $categories),
        ]);
    }

    public function getPieChartData(Request $request)
    {
        $userId = auth()->id();
        $type = $request->input('type', 'monthly'); // Default to Monthly
        $categories = ExpenseCategory::values(); // Get all categories as strings

        if ($type === 'monthly') {
            $filterMonth = $request->input('month', Carbon::now()->format('Y-m'));
            $categoryExpenses = Expense::where('user_id', $userId)
                ->whereMonth('date', Carbon::parse($filterMonth)->month)
                ->whereYear('date', Carbon::parse($filterMonth)->year)
                ->groupBy('category')
                ->selectRaw('category, SUM(amount) as total')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->category instanceof ExpenseCategory ? $item->category->value : (string)$item->category => $item->total];
                });
        } else {
            $filterYear = $request->input('year', Carbon::now()->year);
            $categoryExpenses = Expense::where('user_id', $userId)
                ->whereYear('date', $filterYear)
                ->groupBy('category')
                ->selectRaw('category, SUM(amount) as total')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->category instanceof ExpenseCategory ? $item->category->value : (string)$item->category => $item->total];
                });
        }

        //Prepare data for the pie chart
        $pieLabels = [];
        $pieData = [];
        foreach ($categories as $category) {
            $pieLabels[] = Str::ucfirst($category);
        }

        foreach ($categories as $category) {
            $pieData[] = $categoryExpenses[$category] ?? 0;
        }

        return response()->json([
            'pieLabels' => $pieLabels,
            'pieData' => $pieData
        ]);
    }

}
