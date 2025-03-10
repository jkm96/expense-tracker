<div class="mx-auto mt-5">

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
                    <input type="text" wire:model="name" placeholder="Expense Name"
                           class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">

                    <input type="number" wire:model="amount" placeholder="Amount"
                           class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">

                    <select wire:model="category"
                            class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                        @endforeach
                    </select>

                    <input type="date" wire:model="start_date" placeholder="Start date"
                           class="w-full p-2 bg-gray-700 border rounded focus:ring focus:ring-blue-300">

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
</div>
