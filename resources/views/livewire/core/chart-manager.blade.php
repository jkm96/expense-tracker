<div class="md:col-span-4 bg-white p-4 rounded shadow">
    <h3 class="text-lg font-semibold mb-2">Spending Overview</h3>

    <!-- 🔹 Filters -->
    <div class="bg-gray-100 p-4 rounded flex flex-wrap gap-4 items-end">

        <!-- View Mode Toggle -->
        <div>
            <label class="block text-sm">View Mode</label>
            <select wire:model="filterMode" class="border rounded p-2">
                <option value="yearly">Yearly (Months)</option>
                <option value="monthly">Monthly (Weeks)</option>
            </select>
        </div>

        <button wire:click="filterExpenses" class="bg-green-500 text-white p-2 rounded">
            Filter
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
        var barChart, lineChart;

        function renderCharts(chartLabels, chartSeries, lineSeries) {
            // 📊 Bar Chart (Monthly Expenses by Category)
            if (!barChart) {
                barChart = new ApexCharts(document.querySelector("#expenseChart"), {
                    chart: { type: 'bar', height: 350 },
                    series: chartSeries,
                    xaxis: { categories: chartLabels },
                    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', '#9C27B0'],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 5,
                            borderRadiusApplication: 'end'
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    fill: {
                        opacity: 1
                    }
                });
                barChart.render();
            } else {
                barChart.updateSeries(chartSeries);
                barChart.updateOptions({ xaxis: { categories: chartLabels } });
            }

            // 📈 Line Chart (Monthly Trends)
            if (!lineChart) {
                lineChart = new ApexCharts(document.querySelector("#lineChart"), {
                    chart: { type: 'line', height: 350 },
                    series: lineSeries,
                    xaxis: { categories: chartLabels },
                    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', '#9C27B0'],
                    stroke: { curve: 'smooth' ,width:2 },
                    markers: { size: 5 }
                });
                lineChart.render();
            } else {
                lineChart.updateSeries(lineSeries);
                lineChart.updateOptions({ xaxis: { categories: chartLabels } });
            }
        }

        // 🔹 Initial render with PHP-generated data
        renderCharts(
            JSON.parse(@json($chartLabels)),
            JSON.parse(@json($chartSeries)),
            JSON.parse(@json($lineSeries))
        );
    });
</script>

