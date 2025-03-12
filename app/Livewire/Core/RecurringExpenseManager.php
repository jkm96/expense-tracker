<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use App\Utils\Helpers\ExpenseHelper;
use Carbon\Carbon;
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

    public function mount()
    {
        $this->start_date = Carbon::now()->format('Y-m-d');
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
            if (empty($expense->notes)){
                $expense->notes = ExpenseHelper::generateDefaultNote($this->category, $this->name);
            }
            $expense->update();

            $recurringExpense->update([
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
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
                'notes' => ExpenseHelper::generateDefaultNote($this->category, $this->name),
                'is_recurring' => true,
            ]);

            RecurringExpense::create([
                'expense_id' => $expense->id,
                'user_id' => auth()->id(),
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
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
        $this->start_date = Carbon::parse($recurringExpense->start_date)->format('Y-m-d');
        $this->frequency = $recurringExpense->frequency->value;

        $this->showForm = true;
    }

    public function resetFields()
    {
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->reset(['name', 'amount', 'start_date', 'category', 'recurring_expense_id', 'showForm']);
    }

    public function render()
    {
        return view('livewire.core.recurring-expense-manager',
            ['frequencies' => ExpenseFrequency::cases()]);
    }
}
