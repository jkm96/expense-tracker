<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use App\Utils\Helpers\CategoryHelper;
use App\Utils\Helpers\ExpenseHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;

class RecurringExpenseManager extends Component
{
    #[Url]
    public $categoryFilter = 'all';
    #[Url]
    public $frequencyFilter = 'all';
    public $recurringExpenses = [];
    public $categories = [];
    public $frequencies = [];
    public $showForm = false;
    public $name, $amount, $category, $start_date, $recurring_expense_id;
    public $frequency;
    public $showDeleteModal = false;
    public $showDetailsModal = false;
    public $selectedExpense;
    public $showToggleModal = false;
    public $selectedExpenseId;

    public function mount()
    {
        $this->start_date = Carbon::now()->format('Y-m-d\TH:i');
        $this->categories = ExpenseCategory::cases();
        $this->frequencies = ExpenseFrequency::cases();
        $this->loadRecurringExpenses();
    }

    public function updatedFilter()
    {
        $this->loadRecurringExpenses();
    }

    public function updatedFrequencyFilter()
    {
        $this->loadRecurringExpenses();
    }

    public function loadRecurringExpenses()
    {
        $query = RecurringExpense::with('expense')
            ->where('user_id', auth()->id());

        if ($this->categoryFilter !== 'all') {
            $query->whereHas('expense', function ($q) {
                $q->where('category', $this->categoryFilter);
            });
        }

        if ($this->frequencyFilter !== 'all') {
            $query->where('frequency', $this->frequencyFilter);
        }

        $this->recurringExpenses = $query->latest()->get();
    }

    public function upsertRecurringExpense()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'category' => ['required', Rule::in(ExpenseCategory::cases())],
            'frequency' => ['required', Rule::in(ExpenseFrequency::cases())],
        ];

        $this->validate($rules);

        if ($this->recurring_expense_id) {
            // Update Existing Recurring Expense
            $recurringExpense = RecurringExpense::findOrFail($this->recurring_expense_id);
            $expense = $recurringExpense->expense;

            $expense->name = Str::title($this->name);
            $expense->amount = $this->amount;
            if (empty($expense->notes)) {
                $expense->notes = ExpenseHelper::generateDefaultNote($this->category, $this->name);
            }
            $expense->update();

            $recurringExpense->update([
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
                'last_processed_at' => $this->start_date,
            ]);

            session()->flash('success', 'Recurring expense updated successfully.');
        } else {
            // Create New Expense
            $expense = Expense::create([
                'user_id' => auth()->id(),
                'name' => Str::title($this->name),
                'amount' => $this->amount,
                'category' => $this->category,
                'date' => now(),
                'notes' => ExpenseHelper::generateDefaultNote($this->category, $this->name)
            ]);

            $nextProcessAt = ExpenseHelper::calculateNextProcessTime(
                ExpenseFrequency::from($this->frequency),
                Carbon::parse($this->start_date)
            );
            RecurringExpense::create([
                'expense_id' => $expense->id,
                'user_id' => auth()->id(),
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
                'is_active' => true,
                'last_processed_at' => null,
                'next_process_at' => $nextProcessAt,
            ]);

            session()->flash('success', 'Recurring expense added successfully.');
        }

        $this->resetFields();
        $this->loadRecurringExpenses();
    }

    public function editRecurringExpense($id)
    {
        $recurringExpense = RecurringExpense::findOrFail($id);

        $this->recurring_expense_id = $recurringExpense->id;
        $this->name = $recurringExpense->expense->name;
        $this->category = $recurringExpense->expense->category->value;
        $this->amount = $recurringExpense->expense->amount;
        $this->start_date = Carbon::parse($recurringExpense->start_date)->toDateTimeString();
        $this->frequency = $recurringExpense->frequency->value;

        $this->showForm = true;
    }

    public function showToggleConfirmation($id)
    {
        $this->selectedExpenseId = $id;
        $this->selectedExpense = RecurringExpense::with('expense')->findOrFail($id);
        $this->showToggleModal = true;
    }

    public function toggleRecurringExpense()
    {
        $expense = RecurringExpense::findOrFail($this->selectedExpenseId);
        $expense->update(['is_active' => !$expense->is_active]);

        $status = $expense->is_active ? 'resumed' : 'stopped';
        session()->flash('success', "Recurring expense has been {$status}.");

        $this->showToggleModal = false;
        $this->selectedExpenseId = null;
        $this->resetFields();
        $this->loadRecurringExpenses();
    }

    public function showDeleteConfirmation($id)
    {
        $this->selectedExpenseId = $id;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        $expense = RecurringExpense::where('id', $this->selectedExpenseId)->first();
        if ($expense) {
            $expense->delete();
            session()->flash('success', 'Recurring expense deleted successfully!');
        }

        $this->showDeleteModal = false;
        $this->selectedExpenseId = null;

        $this->resetFields();
        $this->loadRecurringExpenses();
    }

    public function showRecurringExpenseDetails($id)
    {
        $this->selectedExpense = RecurringExpense::with('expense','generatedExpenses')->findOrFail($id);
        $this->showDetailsModal = true;
    }

    public function resetFields()
    {
        $this->start_date = Carbon::now()->format('Y-m-d\TH:i');
        $this->reset(['name', 'amount', 'start_date', 'category', 'recurring_expense_id', 'showForm']);
    }

    public function render()
    {
        return view('livewire.core.recurring-expense-manager',
            ['frequencies' => ExpenseFrequency::cases()]);
    }
}
