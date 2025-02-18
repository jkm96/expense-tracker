<div class="mx-auto mt-5">

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-2">
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

    <div class="mt-2 mb-10">
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
            <h3 class="text-lg font-semibold mt-2 mb-2 underline">{{ $group }}</h3> <!-- Year - Month Header -->

            <!-- Total Amount for the Group -->
            <p class="text-green-600 text-sm mb-1">Spent: KES {{ number_format($totals[$group], 2) }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($groupedExpenses as $expense)
                    <div class="border p-2 mb-2 rounded bg-gray-200">
                        <div class="flex justify-between mb-1">
                            <div class="flex items-center gap-x-1">
                                <!-- ✅ Category Color Circle -->
                                <span
                                    class="w-3 h-3 rounded-full {{ get_category_color($expense->category->value) }}"></span>
                                <h2 class="font-semibold">{{ $expense->name }}</h2>
                            </div>
                            <p class="text-gray-700 text-sm">{{ Carbon\Carbon::parse($expense->date)->format('jS M Y') }}</p>
                        </div>

                        <hr class="mt-1 mb-1 border-gray-50">

                        <div class="flex items-center">
                            <p class="text-md text-gray-700 mt-1 mb-1 mr-1">{{ ucfirst($expense->category->value) }}</p>
                            |
                            <p class="text-md text-green-600 mt-1 mb-1 ml-1">
                                KES {{ number_format($expense->amount, 2) }}</p>
                        </div>

                        <!-- Content -->
                        <p class="text-sm">{{ $expense->notes }}</p>

                        <!-- Actions -->
                        <div class="mt-2 text-sm flex justify-end">
                            <button wire:click="editExpense({{ $expense->id }})" class="text-blue-500 hover:underline">
                                Edit
                            </button>

                            <!-- Confirmation Modal -->
                            @if($showDeleteModal)
                                <div x-data="{ open: @entangle('showDeleteModal') }" x-show="open"
                                     class="fixed inset-0 z-50 flex items-center justify-center  bg-opacity-60 backdrop-blur-sm">

                                    <!-- Modal Content -->
                                    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
                                        <h2 class="text-lg font-bold text-gray-800">Delete Expense</h2>
                                        <p class="mt-4 text-gray-600">Are you sure you want to delete this expense?
                                            This action cannot be undone.</p>
                                        <div class="mt-6 flex justify-end space-x-4">
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
                                wire:click="showDeleteConfirmation({{ $expense->id }})"
                                class="ml-1 hover:underline text-red-600"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Horizontal line after each group -->
            @if (!$loop->last)
                <hr class="my-4 border-gray-300">
            @endif

        @empty
            <p>No expenses found</p>
        @endforelse

    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $pagination->links() }}
    </div>
</div>
