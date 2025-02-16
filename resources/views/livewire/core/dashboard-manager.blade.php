<div class="grid grid-cols-1 md:grid-cols-4 gap-4">

    {{-- 🔹 Quick Stats Cards --}}
    <div class="md:col-span-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- 📅 This Month -->
        <div class="bg-white shadow rounded p-4 flex items-center gap-3">
            <i class="fas fa-calendar-alt text-gray-500 text-2xl"></i>
            <div>
                <p class="text-sm text-gray-500">This Month</p>
                <h2 class="text-xl font-bold">KES {{ number_format($monthlyTotal, 2) }}</h2>
            </div>
        </div>

        <!-- 🔝 Top Category -->
        <div class="bg-white shadow rounded p-4 flex items-center gap-3">
            <i class="fas fa-chart-pie text-blue-500 text-2xl"></i>
            <div>
                <p class="text-sm text-gray-500">Top Category</p>
                <h2 class="text-xl font-bold">
                    @if($topCategory != null)
                        {{ ucfirst($topCategory->value) }} -
                        KES {{ number_format($topCategoryTotal, 2) }}
                    @else
                        None
                    @endif
                </h2>
            </div>
        </div>

        <!-- 📆 This Year -->
        <div class="bg-white shadow rounded p-4 flex items-center gap-3">
            <i class="fas fa-calendar text-green-500 text-2xl"></i>
            <div>
                <p class="text-sm text-gray-500">This Year</p>
                <h2 class="text-xl font-bold">KES {{ number_format($yearlyTotal, 2) }}</h2>
            </div>
        </div>

        <!-- 💰 Total Expenses -->
        <div class="bg-white shadow rounded p-4 flex items-center gap-3">
            <i class="fas fa-wallet text-red-500 text-2xl"></i>
            <div>
                <p class="text-sm text-gray-500">Total Expenses</p>
                <h2 class="text-xl font-bold">KES {{ number_format($totalExpenses, 2) }}</h2>
            </div>
        </div>

    </div>


    {{-- Recent Expenses --}}
{{--    <div class="md:col-span-4 bg-white p-1 rounded shadow mt-4">--}}
{{--        <h3 class="text-lg font-semibold mb-2">Recent Expenses</h3>--}}
{{--        <table class="w-full border-collapse border">--}}
{{--            <thead>--}}
{{--            <tr class="bg-gray-200">--}}
{{--                <th class="border p-2">Item</th>--}}
{{--                <th class="border p-2">Amount(KES)</th>--}}
{{--                <th class="border p-2">Date</th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--            @forelse($expenses as $expense)--}}
{{--                <tr>--}}
{{--                    <td class="border p-2">{{ ucfirst($expense->name) }}</td>--}}
{{--                    <td class="border p-2 text-green-600">{{ number_format($expense->amount, 2) }}</td>--}}
{{--                    <td class="border p-2">{{ $expense->date->format('d-m-Y') }}</td>--}}
{{--                </tr>--}}
{{--            @empty--}}
{{--                <tr>--}}
{{--                    <td colspan="5" class="text-center p-4">No expenses found.</td>--}}
{{--                </tr>--}}
{{--            @endforelse--}}
{{--            </tbody>--}}
{{--        </table>--}}
{{--    </div>--}}

</div>
