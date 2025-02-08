<div class="md:col-span-4 bg-white p-4 rounded shadow">
    <h3 class="text-lg font-semibold mb-2">Spending Overview</h3>

    <!-- 🔹 Filters -->
    <div class="bg-gray-100 p-4 rounded flex flex-wrap gap-4 items-end">

        <div>
            <label class="block text-sm">Date Range</label>
            <select wire:model="filterDate" class="border rounded p-2">
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        <div>
            <label class="block text-sm">Category</label>
            <select wire:model="filterCategory" class="border rounded p-2">
                <option value="all">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                @endforeach
            </select>
        </div>

        @if ($filterDate === 'custom')
            <div>
                <label class="block text-sm">Pick a Date</label>
                <input type="date" wire:model="customDate" class="border rounded p-2">
            </div>
        @endif

        <button wire:click="resetFilters" class="bg-red-500 text-white p-2 rounded">
            Reset Filters
        </button>
    </div>

    <!-- 🔹 Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
        <!-- 📊 Bar Chart (Monthly Expenses) -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold mb-3">📊 Monthly Expenses</h2>
            <div id="expenseChart"></div>
        </div>

        <!-- 🥧 Pie Chart (Category Distribution) -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold mb-3">🥧 Expense by Category</h2>
            <div id="pieChart"></div>
        </div>

        <!-- 📈 Line Chart (Monthly Trends) -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold mb-3">📈 Monthly Trends</h2>
            <div id="lineChart"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var chartLabels = JSON.parse(@json($chartLabels));
        var chartData = JSON.parse(@json($chartData)).map(Number);
        var pieLabels = JSON.parse(@json($pieLabels));
        var pieData = JSON.parse(@json($pieData)).map(Number);

        // 📊 Bar Chart (Monthly Expenses)
        new ApexCharts(document.querySelector("#expenseChart"), {
            chart: { type: 'bar', height: 350 },
            series: [{ name: 'Total Expenses (KES)', data: chartData }],
            xaxis: { categories: chartLabels },
            colors: ['#1E88E5'],
            plotOptions: { bar: { borderRadius: 4, horizontal: false } },
            dataLabels: { enabled: false }
        }).render();

        // 🥧 Pie Chart (Category Distribution)
        new ApexCharts(document.querySelector("#pieChart"), {
            chart: { type: 'pie', height: 350 },
            series: pieData,
            labels: pieLabels,
            colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800']
        }).render();

        // 📈 Line Chart (Monthly Trends)
        new ApexCharts(document.querySelector("#lineChart"), {
            chart: { type: 'line', height: 350 },
            series: [{ name: 'Expenses', data: chartData }],
            xaxis: { categories: chartLabels },
            colors: ['#FF5722'],
            stroke: { curve: 'smooth' },
            markers: { size: 5 }
        }).render();
    });
</script>
