<div class="mx-auto mt-2">
    <!-- Floating Add Button -->
    <button wire:click="toggleForm"
            class="fixed bottom-16 right-4 bg-green-400 hover:bg-green-700 text-white w-14 h-14 flex items-center justify-center rounded-full shadow-lg transition">
        <i class="fas fa-plus text-xl"></i>
    </button>

    <!-- Expense Form (Modal Style) -->
    @if ($showForm)
        <div wire:click.self="closeModal('form-modal')"
             class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">
            <div
                class="max-h-[400px] overflow-y-auto bg-gray-700 rounded-lg shadow-lg w-96 p-3 md:mr-0 md:ml-0 mr-1 ml-1">
                <h2 class="text-lg font-bold mb-4">{{ $recurring_expense_id ? 'Edit Recurring Expense' : 'Add Recurring Expense' }}</h2>

                <form wire:submit.prevent="upsertRecurringExpense" class="space-y-3">
                    <div class="mb-4">
                        <input type="text" wire:model="name" placeholder="Expense Name"
                               class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="number" wire:model="amount" placeholder="Amount"
                               class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <select wire:model="category"
                                class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <input type="datetime-local" wire:model="start_date" placeholder="Start date"
                               class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                        <p class="text-xs text-gray-400">Select the starting date for the recurring expense.</p>
                        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <select id="frequency" wire:model.live="frequency" required
                                class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                            <option value="">Select Frequency</option>
                            @foreach($frequencies as $freq)
                                <option value="{{ $freq->value }}">{{ ucfirst($freq->value) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Show when Daily is selected -->
                    @if($frequency == 'daily')
                        <div class="mb-2">
                            <label class="text-xs text-gray-400">Select Days:</label>
                            <div class="grid grid-cols-7 gap-2 justify-center">
                                @foreach($daysOfWeek as $key => $day)
                                    <label class="flex flex-col items-center space-y-1">
                                        <input type="checkbox" wire:model="days" value="{{ $key }}"
                                               class="rounded-full w-3 h-3 text-blue-500 border-gray-400 focus:ring-2 focus:ring-blue-300">
                                        <span class="text-xs">{{ ucfirst($key) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('days') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Show when Weekly is selected -->
                    @if($frequency === 'weekly')
                        <div class="mb-2">
                            <label class="text-xs text-gray-400">Select Day:</label>
                            <select wire:model="dayOfWeek"
                                    class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                                <option value="">Select a Day</option>
                                @foreach($daysOfWeek as $key => $day)
                                    <option value="{{ strtolower($day) }}">{{ $day }}</option>
                                @endforeach
                            </select>
                            @error('dayOfWeek') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Show when Monthly is selected -->
                    @if($frequency === 'monthly')
                        <div class="mb-2">
                            <label class="text-xs text-gray-400">Select Date:</label>
                            <input type="datetime-local" wire:model="dayOfMonth" placeholder="Select date"
                                   class="w-full p-2 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                            @error('dayOfMonth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <button type="button" wire:click="closeModal('form-modal')"
                                class="bg-red-600 text-white text-sm px-2 py-1 rounded shadow hover:bg-red-500 transition">
                            Cancel
                        </button>

                        <button type="submit"
                                class="bg-green-500 text-white text-sm px-2 py-1 rounded shadow hover:bg-green-600 transition">
                            {{ $recurring_expense_id ? 'Update' : 'Add' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
        <h2 class="text-md font-bold mb-2 sm:mb-0">Recurring Expenses</h2>

        <div class="flex items-center space-x-2">
            <!-- Category Filter -->
            <select wire:model.live="categoryFilter" wire:change="loadRecurringExpenses"
                    class="w-full px-1.5 py-0.5 bg-gray-800 text-sm border rounded  border-gray-500 focus:outline-none
                              focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                <option value="all">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                @endforeach
            </select>

            <!-- Frequency Filter -->
            <select wire:model.live="frequencyFilter" wire:change="loadRecurringExpenses"
                    class="w-full px-1.5 py-0.5 bg-gray-800 text-sm border rounded  border-gray-500 focus:outline-none
                              focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                <option value="all">All Frequencies</option>
                @foreach($frequencies as $freq)
                    <option value="{{ $freq->value }}">{{ ucfirst($freq->value) }}</option>
                @endforeach
            </select>

            <button wire:click="toggleExportModal"
                    class="bg-green-400 hover:bg-green-600 text-white text-sm px-1.5 py-0.5 rounded ml-1">
                Export
            </button>
        </div>
    </div>

    @if($showExportModal)
        <div x-show="open"
             x-transition.opacity
             class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

            <div @click.outside="toggleExportModal"
                 class="bg-gray-700 rounded-md shadow-lg w-96 p-3 md:mr-0 md:ml-0 mr-1 ml-1">
                <h2 class="text-md font-bold mb-4">Export Recurring Expenses</h2>

                <div class="flex flex-row gap-4">
                    <div class="mb-4 w-1/2">
                        <label class="text-sm">Start date:</label>
                        <input type="date" wire:model="exportFields.startDate"
                               class="w-full p-1 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                        @error('exportFields.startDate') <span
                            class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4 w-1/2">
                        <label class="text-sm">End date:</label>
                        <input type="date" wire:model="exportFields.endDate"
                               class="w-full p-1 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                        @error('exportFields.endDate') <span
                            class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-sm">Category:</label>
                    <select wire:model="exportFields.category"
                            class="w-full p-1 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}">{{ ucfirst($category->value) }}</option>
                        @endforeach
                    </select>
                    @error('exportFields.category') <span
                        class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="text-sm">Frequency:</label>
                    <select wire:model="exportFields.frequency"
                            class="w-full p-1 text-sm bg-gray-700 border border-gray-500 rounded focus:outline-none
                                focus:border-gray-400 focus:ring-1 focus:ring-gray-400 focus:ring-opacity-50">
                        <option value="">Select Frequency</option>
                        @foreach($frequencies as $frequency)
                            <option value="{{ $frequency->value }}">{{ ucfirst($frequency->value) }}</option>
                        @endforeach
                    </select>
                    @error('exportFields.frequency') <span
                        class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mt-4 flex justify-between space-x-4">
                    <button wire:click="toggleExportModal"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm px-1.5 py-0.5 rounded">
                        Cancel
                    </button>
                    <button wire:click="exportRecurringExpenses"
                            class="bg-green-400 hover:bg-green-500 text-white text-sm px-1.5 py-0.5 rounded">
                        Export
                    </button>
                </div>

            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
        @forelse($recurringExpenses as $recurringExpense)
            <div class="p-2 shadow-md rounded bg-gray-700 hover:bg-gray-600">
                <div class="flex justify-between mb-1">
                    <div class="flex items-center gap-x-1">
                        @if($recurringExpense->is_active)
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full {{ get_category_color($recurringExpense->category)[0] }} opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-3 w-3 {{ get_category_color($recurringExpense->category)[1] }}"></span>
                            </span>
                        @else
                            <span
                                class="w-3 h-3 rounded-full {{ get_category_color($recurringExpense->category)[0] }}"></span>
                        @endif
                        <h2 class="font-semibold text-md">{{ $recurringExpense->name }}</h2>
                    </div>
                    <div class="flex items-center gap-x-1">
                        <p class="text-gray-300 text-xs">
                            <i class="fas fa-sync-alt text-green-400"></i>
                            {{ ucfirst($recurringExpense->frequency->value) }}
                        </p>
                        |
                        <p class="text-xs">
                            <i class="fas fa-clock text-blue-400"></i>
                            {{ $recurringExpense->next_process_at?->format('jS M Y h:i A') }}
                        </p>
                    </div>
                </div>

                <hr class="mt-1 mb-1 border-gray-600">

                <div class="flex items-center">
                    <p class="text-xs mt-1 mb-1 mr-1">
                        <i class="fas fa-folder-open text-yellow-400"></i>
                        {{ ucfirst($recurringExpense->category->value) }}
                    </p>
                    |
                    <p class="text-xs text-green-600 mt-1 mb-1 ml-1 mr-1">
                        KES {{ number_format($recurringExpense->amount, 2) }}
                    </p>
                    |
                    <p class="text-xs mt-1 mb-1 mr-1 ml-1">
                        <i class="fas fa-calendar-alt text-orange-400"></i>
                        {{ $recurringExpense->start_date->format('D, d M Y h:i A') }}
                    </p>
                </div>

                <!-- Content -->
                <p class="text-sm">{{ $recurringExpense->notes }}</p>

                <!-- Actions -->
                <div class="mt-2 text-sm flex justify-end">
                    <button wire:click="showRecurringExpenseDetails({{ $recurringExpense->id }})"
                            class="text-green-500 hover:underline">
                        View
                    </button>

                    <button wire:click="editRecurringExpense({{ $recurringExpense->id }})"
                            class="text-blue-500 ml-1 hover:underline">
                        Edit
                    </button>

                    <button wire:click="showToggleConfirmation({{ $recurringExpense->id }})"
                            class="ml-1 hover:underline {{ $recurringExpense->is_active ? 'text-orange-500' : 'text-green-500' }}">
                        {{ $recurringExpense->is_active ? 'Stop' : 'Resume' }}
                    </button>

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

        @if($showDetailsModal && !empty($selectedExpense))
            <div wire:click.self="closeModal('view-modal')" x-data="{ open: @entangle('showDetailsModal') }"
                 x-show="open"
                 class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

                <div class="bg-gray-700 rounded-md shadow-lg w-[600px] p-3 md:mr-0 md:ml-0 mr-1 ml-1">
                    <h2 class="text-md font-bold mb-2">Recurring Expense Details</h2>

                    <div class="bg-gray-800 p-2 rounded-sm shadow text-sm">
                        <div class="space-y-1 text-gray-300">
                            <p><strong>Name:</strong> {{ ucfirst($selectedExpense->name) }}</p>
                            <p><strong>Category:</strong> {{ ucfirst($selectedExpense->category->value) }}</p>
                            <p><strong>Amount:</strong> KES {{ number_format($selectedExpense->amount, 2) }}</p>
                            <p><strong>Frequency:</strong> {{ $selectedExpense->frequency->name }}</p>
                            <p class="flex items-center"><strong class="mr-1">Status:</strong>
                                @if($selectedExpense->is_active)
                                    <span class="relative flex h-3 w-3 mr-0.5">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full {{ get_category_color($selectedExpense->category)[0] }} opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-3 w-3 {{ get_category_color($selectedExpense->category)[1] }}"></span>
                                                </span>
                                @else
                                    <span class="relative flex h-3 w-3 mr-0.5">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                                </span>
                                @endif
                                {{ $selectedExpense->is_active ? 'Active' : 'Inactive' }}
                            </p>
                            <p><strong>Start
                                    Date:</strong> {{ $selectedExpense->start_date->format('D, jS M Y h:i A') }}
                            </p>
                            <p><strong>Last
                                    Processed:</strong> {{ $selectedExpense->last_processed_at?->format('D, jS M Y h:i A') }}
                            </p>
                            <p><strong>Next Process
                                    At:</strong> {{ $selectedExpense->next_process_at?->format('D, jS M Y h:i A') }}</p>
                            <p><strong>Note:</strong> {{ $selectedExpense->notes }}</p>
                            <p><strong>Execution Day(s):</strong> {{ $selectedExpense->execution_days }}</p>
                        </div>
                    </div>

                    <!-- Generated Expenses -->
                    <div class="mt-2">
                        <h3 class="text-md font-semibold text-white mb-2">Generated Expenses</h3>
                        <div class="overflow-y-auto max-h-60 bg-gray-800 p-2 rounded-sm shadow text-sm">
                            <ul class="space-y-1">
                                @forelse($selectedExpense->generatedExpenses as $expense)
                                    <li class="border-b border-gray-600 pb-2">
                                        <p><strong>Processed
                                                At:</strong> {{ $expense->created_at->format('D, jS M Y h:i A') }}</p>
                                        <p><strong>Amount:</strong> KES {{ number_format($expense->amount, 2) }}</p>
                                    </li>
                                @empty
                                    <p class="text-gray-400">No generated expenses yet.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="mt-2 flex justify-end space-x-4">
                        <button wire:click="closeModal('view-modal')"
                                class="bg-red-600 hover:bg-red-500 text-sm px-2 py-1 rounded">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($showToggleModal && $selectedExpense)
            <div x-data="{ open: @entangle('showToggleModal') }" x-show="open"
                 class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

                <div @click.outside="open = false"
                     class="bg-gray-700 rounded-md shadow-lg w-96 p-3 md:mr-0 md:ml-0 mr-1 ml-1">
                    <h2 class="text-md font-bold">
                        {{ $selectedExpense->is_active ? 'Stop Recurring Expense' : 'Resume Recurring Expense' }}
                    </h2>

                    <p class="mt-2 text-sm">
                        {{ $selectedExpense->is_active
                            ? 'Are you sure you want to stop this recurring expense? This action will deactivate it.'
                            : 'Are you sure you want to resume this recurring expense? This will reactivate it.' }}
                    </p>

                    <div class="mt-2 flex justify-between space-x-4">
                        <button @click="open = false"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm px-2 py-1 rounded">
                            Cancel
                        </button>

                        <button wire:click="toggleRecurringExpense"
                                class="{{ $selectedExpense->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600' }} text-white text-sm px-2 py-1 rounded">
                            {{ $selectedExpense->is_active ? 'Stop' : 'Resume' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($showDeleteModal)
            <div x-data="{ open: @entangle('showDeleteModal') }" x-show="open"
                 class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

                <!-- Modal Content -->
                <div @click.outside="open = false"
                     class="bg-gray-700 rounded-md shadow-lg w-96 p-3 md:mr-0 md:ml-0 mr-1 ml-1">
                    <h2 class="text-md font-bold">Delete Expense</h2>

                    <p class="mt-2 text-sm">Are you sure you want to delete this recurring expense?
                        This action cannot be undone.</p>

                    <div class="mt-2 flex justify-between space-x-4">
                        <button @click="open = false"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm px-2 py-1 rounded">
                            Cancel
                        </button>
                        <button wire:click="confirmDelete"
                                class="bg-red-600 hover:bg-red-700 text-sm px-2 py-1 rounded">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @script
    <script>
        $(document).ready(function () {
            let recurringFormState = JSON.parse(localStorage.getItem('showRecurringForm'));
            if (recurringFormState && recurringFormState.showForm === true) {
                if (recurringFormState.recurringExpenseId) {
                    $wire.dispatch('edit-recurring-expense', {recurringExpenseId: recurringFormState.recurringExpenseId});
                } else {
                    $wire.dispatch('toggle-form');
                }
            }

            let recurringViewState = JSON.parse(localStorage.getItem('showRecurringDetails'));
            if (recurringViewState && recurringViewState.showModal === true) {
                if (recurringViewState.recurringExpenseId) {
                    $wire.dispatch('show-recurring-details', {recurringExpenseId: recurringViewState.recurringExpenseId});
                }
            }
        });

        $wire.on('recurring-form-updated', (event) => {
            localStorage.setItem('showRecurringForm', JSON.stringify(event.details));
        });

        $wire.on('show-recurring-details', (event) => {
            localStorage.setItem('showRecurringDetails', JSON.stringify(event.details));
        });
    </script>
    @endscript
</div>
