<div class="mx-auto mt-5">

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-2">
            {{ session('success') }}
        </div>
    @endif

    <!-- Floating Add Button -->
    <button wire:click="$toggle('showForm')"
            class="fixed bottom-16 right-4 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-full shadow-lg transition">
        + Add Expense
    </button>

    <!-- Expense Form (Modal Style) -->
    @if ($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-bold mb-4">{{ $expense_id ? 'Edit Expense' : 'Add Expense' }}</h2>

                <form wire:submit.prevent="addExpense" class="space-y-3">
                    <input type="text" wire:model="name" placeholder="Expense Name"
                           class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                    <input type="number" wire:model="amount" placeholder="Amount"
                           class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                    <input type="date" wire:model="date"
                           class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                    <select wire:model="category"
                            class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                        @endforeach
                    </select>
                    <textarea wire:model="notes" placeholder="Notes"
                              class="w-full p-2 border rounded focus:ring focus:ring-blue-300"></textarea>

                    <div class="flex justify-between">
                        <button type="button" wire:click="resetFields"
                                class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-500 transition">
                            Cancel
                        </button>

                        <button type="submit"
                                class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 transition">
                            {{ $expense_id ? 'Update' : 'Add' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="mt-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold mb-2">Current Expenses</h2>
            <div class="flex items-center">
                <select wire:model.live="filter" wire:change="loadExpenses"
                        class="bg-gray-100 border rounded-full px-2 py-1">
                    <option value="all">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Expense Cards -->
        @forelse($expenses as $group => $groupedExpenses)
            <h3 class="text-lg font-semibold mt-2 mb-2">{{ $group }}</h3> <!-- Year - Month Header -->

            <!-- Total Amount for the Group -->
            <p class="text-green-600 text-sm mb-1">Spent: KES {{ number_format($totals[$group], 2) }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($groupedExpenses as $expense)
                    <div class="border p-2 mb-2 rounded bg-gray-100">
                        <div class="flex justify-between mb-1">
                            <h2 class="font-semibold">{{ $expense->name }}</h2>
                            <p class="text-gray-700 text-sm">{{ Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</p>
                        </div>

                        <div class="flex items-center">
                            <p class="text-md text-gray-700 mt-1 mb-1 mr-1">{{ ucfirst($expense->category->value) }}</p>
                            <p class="text-md text-green-600 mt-1 mb-1">KES {{ number_format($expense->amount, 2) }}</p>
                        </div>

                        <!-- Content -->
                        <p class="text-sm">{{ $expense->notes }}</p>

                        <!-- Actions -->
                        <div class="mt-2 text-sm flex justify-end">
                            <button wire:click="editExpense({{ $expense->id }})" class="text-blue-500">Edit</button>
                            <button wire:click="deleteExpense({{ $expense->id }})" class="text-red-500 ml-2">Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <p>No expenses found</p>
        @endforelse

    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $pagination->links() }}
    </div>
</div>
