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
            <button id="fetchData" class="bg-green-500 text-white p-2 rounded">Fetch Data</button>
        </div>


        <div id="chartContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 mb-4">
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
            var barChart, lineChart;
            var today = new Date();
            var currentMonth = today.toISOString().slice(0, 7); // Format: YYYY-MM
            $("#monthlyFilter").val(currentMonth);

            // ✅ Ensure the correct field is shown based on the default "Monthly" selection
            $("#monthlyPicker").show();
            $("#yearlyPicker").hide();

            function updateChartTitles(selectedType) {
                if (selectedType === "monthly") {
                    $("#chartContainer").removeClass("grid-cols-1").addClass("md:grid-cols-2"); // ✅ Display charts in two columns for monthly view

                    var selectedMonth = $("#monthlyFilter").val(); // Format: YYYY-MM

                    if (selectedMonth) {
                        var monthName = new Date(selectedMonth + "-01").toLocaleString('default', {month: 'long'}); // Convert to full month name
                        var year = selectedMonth.split("-")[0]; // Extract year
                        $("#barTitle").text(`Weekly Expenses in ${monthName} ${year}`);
                        $("#lineTitle").text(`Monthly Expense Trends for ${monthName} ${year}`);
                    } else {
                        $("#barTitle").text("Weekly Expenses");
                        $("#lineTitle").text("Weekly Expense Trends");
                    }
                } else {
                    $("#chartContainer").removeClass("md:grid-cols-2").addClass("grid-cols-1"); // ✅ Stack charts in a single column

                    var selectedYear = $("#yearlyFilter").val();
                    if (selectedYear) {
                        $("#barTitle").text(`Yearly Expenses in ${selectedYear}`);
                        $("#lineTitle").text(`Yearly Expense Trends for ${selectedYear}`);
                    } else {
                        $("#barTitle").text("Monthly Expenses in Selected Year");
                        $("#lineTitle").text("Yearly Expense Trends");
                    }
                }
            }

            // 📊 Initialize Charts with Empty Data (Avoids first-time errors)
            initializeCharts();

            // Handle dropdown selection to toggle filters
            $("#filterType").on("change", function () {
                var selectedType = $(this).val();
                updateChartTitles(selectedType);

                if (selectedType === "monthly") {
                    $("#monthlyPicker").show();
                    $("#yearlyPicker").hide();
                } else {
                    $("#monthlyPicker").hide();
                    $("#yearlyPicker").show();
                }
            });

            // Fetch Data Based on Selection
            $("#fetchData").on("click", function () {
                var selectedType = $("#filterType").val();
                var requestData = {};

                updateChartTitles(selectedType);

                if (selectedType === "monthly") {
                    requestData.month = $("#monthlyFilter").val();
                    fetchChartData("{{ route('chart.data.monthly') }}", requestData);
                } else {
                    requestData.year = $("#yearlyFilter").val();
                    fetchChartData("{{ route('chart.data.yearly') }}", requestData);
                }
            });

            // 📊 Initialize Empty Charts (So they exist before AJAX updates them)
            function initializeCharts() {
                barChart = new ApexCharts(document.querySelector("#barChart"), {
                    chart: {
                        type: 'bar',
                        height: 350,
                        id: 'barChart',
                        zoom: {enabled: false},
                    },
                    series: [],
                    xaxis: {categories: [], tickPlacement: 'on'},
                    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', '#9C27B0'],
                    plotOptions: {bar: {columnWidth: '55%', borderRadius: 5}},
                    dataLabels: {enabled: false},
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    }
                });
                barChart.render();

                lineChart = new ApexCharts(document.querySelector("#lineChart"), {
                    chart: {
                        type: 'line',
                        height: 350,
                        id: 'lineChart',
                        zoom: {enabled: false},
                    },
                    series: [],
                    xaxis: {categories: []},
                    stroke: {curve: 'smooth', width: 3},
                    markers: {size: 5},
                    colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', '#9C27B0'],
                    dataLabels: {enabled: false}
                });
                lineChart.render();
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
                    barChart.updateOptions({xaxis: {categories: labels}});

                    lineChart.updateSeries(series);
                    lineChart.updateOptions({xaxis: {categories: labels}});
                } else {
                    console.warn("Charts not initialized yet!");
                }
            }

            fetchChartData("{{ route('chart.data.monthly') }}", {month: currentMonth});
        });
    </script>

@endsection
