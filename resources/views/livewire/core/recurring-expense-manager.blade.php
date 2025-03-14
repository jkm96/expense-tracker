<div class="mx-auto mt-2">

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-2 shadow-md rounded mb-2">
            {{ session('success') }}
        </div>
    @endif

    <!-- Floating Add Button -->
    <button wire:click="$toggle('showForm')"
            class="fixed bottom-16 right-4 bg-green-400 hover:bg-green-700 text-white w-14 h-14 flex items-center justify-center rounded-full shadow-lg transition">
        <i class="fas fa-plus text-xl"></i>
    </button>

    <!-- Expense Form (Modal Style) -->
    @if ($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-gray-700 p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-bold mb-4">{{ $recurring_expense_id ? 'Edit Recurring Expense' : 'Add Recurring Expense' }}</h2>

                <form wire:submit.prevent="upsertRecurringExpense" class="space-y-3">
                    <div class="mb-4">
                        <input type="text" wire:model="name" placeholder="Expense Name"
                               class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="number" wire:model="amount" placeholder="Amount"
                               class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">
                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <select wire:model="category"
                                class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="datetime-local" wire:model="start_date" placeholder="Start date"
                               class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">
                        <p class="text-sm text-gray-400">Select the starting date for the recurring expense.</p>
                        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <select id="frequency" wire:model="frequency" required
                            class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">
                        <option value="">Select Frequency</option>
                        @foreach($frequencies as $freq)
                            <option value="{{ $freq->value }}">{{ ucfirst($freq->value) }}</option>
                        @endforeach
                    </select>

                    <div class="flex justify-between">
                        <button type="button" wire:click="resetFields"
                                class="bg-red-600 text-white px-2 py-1 rounded shadow hover:bg-red-500 transition">
                            Cancel
                        </button>

                        <button type="submit"
                                class="bg-green-500 text-white px-2 py-1 rounded shadow hover:bg-green-600 transition">
                            {{ $recurring_expense_id ? 'Update' : 'Add' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
        <h2 class="text-lg font-bold mb-2 sm:mb-0">Recurring Expenses</h2>

        <div class="flex items-center space-x-4">
            <!-- Category Filter -->
            <select wire:model.live="categoryFilter" wire:change="loadRecurringExpenses"
                    class="bg-gray-800 border rounded-full px-2 py-1">
                <option value="all">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                @endforeach
            </select>

            <!-- Frequency Filter -->
            <select wire:model.live="frequencyFilter" wire:change="loadRecurringExpenses"
                    class="bg-gray-800 border rounded-full px-2 py-1">
                <option value="all">All Frequencies</option>
                @foreach($frequencies as $freq)
                    <option value="{{ $freq->value }}">{{ ucfirst($freq->value) }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($recurringExpenses as $recurringExpense)
            <div class="p-2 mb-2 shadow-md rounded bg-gray-700">
                <div class="flex justify-between mb-1">
                    <div class="flex items-center gap-x-1">
                        <!-- ✅ Category Color Circle -->
                        <span
                            class="w-3 h-3 rounded-full {{ get_category_color($recurringExpense->expense->category->value) }}"></span>
                        <h2 class="font-semibold">{{ $recurringExpense->expense->name }}</h2>
                    </div>
                    <div class="flex items-center gap-x-1">
                        <p class="text-gray-300 text-sm">
                            <i class="fas fa-sync-alt text-green-400"></i>
                            {{ ucfirst($recurringExpense->frequency->value) }}
                        </p>
                        |
                        <p class="text-sm">
                            <i class="fas fa-clock text-blue-400"></i>
                            {{ $recurringExpense->last_processed_at->format('Y-m-d h:i A') }}
                        </p>
                    </div>
                </div>

                <hr class="mt-1 mb-1 border-gray-600">

                <div class="flex items-center">
                    <p class="text-sm mt-1 mb-1 mr-1">
                        <i class="fas fa-folder-open text-yellow-400"></i>
                        {{ ucfirst($recurringExpense->expense->category->value) }}
                    </p>
                    |
                    <p class="text-sm text-green-600 mt-1 mb-1 ml-1 mr-1">
                        KES {{ number_format($recurringExpense->expense->amount, 2) }}
                    </p>
                    |
                    <p class="text-sm mt-1 mb-1 mr-1 ml-1">
                        <i class="fas fa-calendar-alt text-orange-400"></i>
                        {{ $recurringExpense->start_date->format('Y-m-d h:i A') }}
                    </p>
                </div>

                <!-- Content -->
                <p class="text-sm">{{ $recurringExpense->expense->notes }}</p>

                <!-- Actions -->
                <div class="mt-2 text-sm flex justify-end">
                    <button wire:click="editRecurringExpense({{ $recurringExpense->id }})"
                            class="text-blue-500 hover:underline">
                        Edit
                    </button>

                    <!-- Confirmation Modal -->
                    @if($showDeleteModal)
                        <div x-data="{ open: @entangle('showDeleteModal') }" x-show="open"
                             class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

                            <!-- Modal Content -->
                            <div class="bg-gray-700 rounded-lg shadow-lg w-96 p-6">
                                <h2 class="text-lg font-bold">Delete Expense</h2>
                                <p class="mt-4">Are you sure you want to delete this recurring expense?
                                    This action cannot be undone.</p>
                                <div class="mt-6 flex justify-between space-x-4">
                                    <button @click="open = false"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded">
                                        Cancel
                                    </button>
                                    <button wire:click="confirmDelete"
                                            class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button
                        wire:click="showDeleteConfirmation({{ $recurringExpense->id }})"
                        class="ml-1 hover:underline text-red-600"
                    >
                        Delete
                    </button>
                </div>
            </div>
        @empty
            <p>No recurring expenses found</p>
        @endforelse
    </div>
</div>
