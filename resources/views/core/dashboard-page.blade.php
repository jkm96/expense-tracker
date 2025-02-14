@extends('layouts.app')

@section('content')

    <section class="container mx-auto p-2">
        <livewire:core.dashboard-manager/>

        <!-- 🔹 Filters -->
        <div class="bg-gray-100 p-4 rounded shadow mt-5 flex flex-wrap gap-4 items-end">
            <!-- View Mode Selection -->
            <div>
                <label class="block text-sm">Aggregate</label>
                <select id="filterType" class="border rounded p-2">
                    <option value="monthly" selected>Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>

            <!-- Monthly Picker (Initially Visible) -->
            <div id="monthlyPicker">
                <label class="block text-sm">Select Month</label>
                <input type="month" id="monthlyFilter" class="border rounded p-2">
            </div>

            <!-- Yearly Picker (Hidden by Default) -->
            <div id="yearlyPicker" style="display: none;">
                <label class="block text-sm">Select Year</label>
                <input type="number" id="yearlyFilter" class="border rounded p-2" min="2000" max="2099" step="1">
            </div>

            <!-- Fetch Data Button -->
            <button id="fetchData" class="bg-green-500 text-white p-2 rounded">Filter</button>
        </div>


        <div id="chartContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 mb-10">
            <!-- 📊 Column Chart (Daily Trends in Selected Month) -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold mb-3">
                    📊 <span id="pieTitle">Weekly Expenses</span>
                </h2>
                <div id="pieChart"></div>
            </div>

            <!-- 📊 Column Chart (Daily Trends in Selected Month) -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold mb-3">
                    📊 <span id="barTitle">Weekly Expenses</span>
                </h2>
                <div id="barChart"></div>
            </div>

            <!-- 📈 Line Chart (Yearly Trends) -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-lg font-semibold mb-3">
                    📈 <span id="lineTitle">Weekly Expense Trends</span>
                </h2>
                <div id="lineChart"></div>
            </div>
        </div>

    </section>

    <script>
        $(document).ready(function () {
            var barChart, lineChart, pieChart;

            // ✅ Load saved filters from localStorage (Use a common key)
            var savedFilters = JSON.parse(localStorage.getItem("expenseFilter"));

            // ✅ Handle case where localStorage is empty (first page load)
            if (!savedFilters || !savedFilters.type) {
                savedFilters = {
                    type: "monthly", // Default: Monthly
                    month: new Date().toISOString().slice(0, 7), // Default: Current Month (YYYY-MM)
                    year: new Date().getFullYear() // Default: Current Year (YYYY)
                };

                // ✅ Save default filter to localStorage to prevent future empty loads
                localStorage.setItem("expenseFilter", JSON.stringify(savedFilters));
            }

            // ✅ Set the saved values in the dropdowns
            $("#filterType").val(savedFilters.type);
            $("#monthlyFilter").val(savedFilters.month);
            $("#yearlyFilter").val(savedFilters.year);

            // ✅ Ensure the correct picker is shown based on the saved filter type
            function updatePickerVisibility(selectedType) {
                if (selectedType === "yearly") {
                    $("#monthlyPicker").hide();
                    $("#yearlyPicker").show();
                } else {
                    $("#monthlyPicker").show();
                    $("#yearlyPicker").hide();
                }
            }

            // ✅ Function to update chart layout based on filter type
            function updateChartLayout(selectedType) {
                if (selectedType === "yearly") {
                    $("#chartContainer").removeClass("md:grid-cols-2").addClass("grid-cols-1");
                    // $("#monthlyPicker").hide();
                    // $("#yearlyPicker").show();
                } else {
                    $("#chartContainer").removeClass("grid-cols-1").addClass("md:grid-cols-2");
                    // $("#monthlyPicker").show();
                    // $("#yearlyPicker").hide();
                }
            }

            // ✅ Function to update chart titles dynamically
            function updateChartTitles(selectedType) {
                if (selectedType === "monthly") {
                    var selectedMonth = $("#monthlyFilter").val();
                    if (selectedMonth) {
                        var monthName = new Date(selectedMonth + "-01").toLocaleString('default', { month: 'long' });
                        var year = selectedMonth.split("-")[0];
                        $("#barTitle").text(`Weekly Expenses in ${monthName} ${year}`);
                        $("#lineTitle").text(`Monthly Expense Trends for ${monthName} ${year}`);
                        $("#pieTitle").text("Monthly Expense Per Category");
                    }
                } else {
                    var selectedYear = $("#yearlyFilter").val();
                    if (selectedYear) {
                        $("#barTitle").text(`Monthly Expenses in ${selectedYear}`);
                        $("#lineTitle").text(`Yearly Expense Trends for ${selectedYear}`);
                        $("#pieTitle").text("Yearly Expense Per Category");
                    }
                }
            }

            // ✅ Save filter selections to localStorage
            function saveFilters() {
                var filters = {
                    type: $("#filterType").val(),
                    month: $("#monthlyFilter").val(),
                    year: $("#yearlyFilter").val()
                };
                localStorage.setItem("expenseFilter", JSON.stringify(filters));
            }

            // ✅ Apply saved settings on page load
            updatePickerVisibility(savedFilters.type);
            updateChartLayout(savedFilters.type);
            updateChartTitles(savedFilters.type);

            // 📊 Initialize Charts with Empty Data (Avoids first-time errors)
            initializeCharts();

            // ✅ Handle filter selection change
            $("#filterType").on("change", function () {
                var selectedType = $("#filterType").val();
                updatePickerVisibility(selectedType);
                saveFilters(); // ✅ Save selection
            });

            $("#monthlyFilter, #yearlyFilter").on("change", function () {
                var selectedType = $("#filterType").val();
                // updateChartTitles(selectedType);
                // updateChartLayout(selectedType);
                saveFilters(); // ✅ Save selection
            });

            // ✅ Handle fetch data button click
            $("#fetchData").on("click", function () {
                var selectedType = $("#filterType").val();
                updateChartTitles(selectedType);
                updateChartLayout(selectedType);
                saveFilters(); // ✅ Save selection before fetching

                var requestData = {};
                if (selectedType === "monthly") {
                    requestData.month = $("#monthlyFilter").val();
                    fetchChartData("{{ route('chart.data.monthly') }}", requestData);
                } else {
                    requestData.year = $("#yearlyFilter").val();
                    fetchChartData("{{ route('chart.data.yearly') }}", requestData);
                }

                fetchPieChartData();
            });

            // 📊 Initialize Empty Charts (Ensures charts exist before data loads)
            function initializeCharts() {
                barChart = new ApexCharts(document.querySelector("#barChart"), {
                    chart: {
                        type: 'bar',
                        height: 350,
                        id: 'barChart',
                        zoom: { enabled: false },
                    },
                    series: [],
                    xaxis: { categories: [], tickPlacement: 'on' },
                    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', '#9C27B0'],
                    plotOptions: { bar: { columnWidth: '55%', borderRadius: 5 } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] }
                });
                barChart.render();

                lineChart = new ApexCharts(document.querySelector("#lineChart"), {
                    chart: {
                        type: 'line',
                        height: 350,
                        id: 'lineChart',
                        zoom: { enabled: false },
                    },
                    series: [],
                    xaxis: { categories: [] },
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5 },
                    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', '#9C27B0'],
                    dataLabels: { enabled: false }
                });
                lineChart.render();

                pieChart = new ApexCharts(document.querySelector("#pieChart"), {
                    chart: { type: 'pie', height: 350, id: 'pieChart' },
                    series: [],
                    labels: [],
                    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', '#9C27B0']
                });
                pieChart.render();
            }

            // 📊 Fetch Data via AJAX & Debug Response
            function fetchChartData(url, data) {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: data,
                    success: function (response) {
                        if (!response.labels && !response.series) {
                            console.warn("Invalid response format:", response);
                            return;
                        }

                        // ✅ Ensure response data is numeric
                        response.series.forEach(series => {
                            series.data = series.data.map(value => isNaN(value) ? 0 : Number(value));
                        });

                        updateCharts(response.labels, response.series);
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                    }
                });
            }

            // 🥧 Fetch Data for Pie Chart
            function fetchPieChartData() {
                let filters = JSON.parse(localStorage.getItem("expenseFilter")) || {};
                let requestData = { type: filters.type || "monthly" };

                if (requestData.type === "monthly") {
                    requestData.month = filters.month || new Date().toISOString().slice(0, 7);
                } else {
                    requestData.year = filters.year || new Date().getFullYear();
                }

                $.ajax({
                    url: "/chart-data/pie",
                    type: "GET",
                    data: filters,
                    success: function (response) {
                        if (!response.pieLabels || !response.pieData) {
                            console.warn("Invalid pie chart response format:", response);
                            return;
                        }

                        updatePieChart(response.pieLabels, response.pieData);
                    },
                    error: function (xhr, status, error) {
                        console.error("Pie Chart AJAX Error:", error);
                    }
                });
            }

            function updatePieChart(pieLabels, pieData) {
                // ✅ Convert all values to numbers
                pieData = pieData.map(value => parseFloat(value) || 0);

                // ✅ Prevent empty chart issue
                if (pieData.every(value => value === 0)) {
                    pieLabels = ['No Data'];
                    pieData = [1]; // Fake data to force rendering
                }

                pieChart.updateSeries(pieData);
                pieChart.updateOptions({ labels: pieLabels });
            }


            // 📊 Update Charts Dynamically
            function updateCharts(labels, series) {
                series.forEach(s => {
                    while (s.data.length < labels.length) {
                        s.data.push(0); // ✅ Fill missing values with 0
                    }
                    while (s.data.length > labels.length) {
                        s.data.pop(); // ✅ Trim extra values
                    }
                });

                if (barChart && lineChart) {
                    barChart.updateSeries(series);
                    barChart.updateOptions({ xaxis: { categories: labels } });

                    lineChart.updateSeries(series);
                    lineChart.updateOptions({ xaxis: { categories: labels } });
                } else {
                    console.warn("Charts not initialized yet!");
                }
            }

            // ✅ Load default data on page load based on saved filter
            if (savedFilters.type === "monthly") {
                fetchChartData("{{ route('chart.data.monthly') }}", { month: savedFilters.month });
            } else {
                fetchChartData("{{ route('chart.data.yearly') }}", { year: savedFilters.year });
            }

            fetchPieChartData()
        });
    </script>


@endsection
