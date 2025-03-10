<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use Carbon\Carbon;
use Livewire\Component;

class RecurringExpenseManager extends Component
{
    public $showForm = false;
    public $name, $amount, $category,$start_date, $recurring_expense_id;
    public $frequency;
    public $categories = [];
    protected $rules = [
        'name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'start_date' => 'required|date',
        'frequency' => 'required|in:daily,weekly,monthly,yearly',
    ];

    public function mount()
    {
        $this->categories = ExpenseCategory::cases();
    }

    public function upsertRecurringExpense()
    {
        $this->validate();

        if ($this->recurring_expense_id) {
            // Update Existing Recurring Expense
            $recurringExpense = RecurringExpense::findOrFail($this->recurring_expense_id);
            $expense = $recurringExpense->expense;

            $expense->update([
                'name' => $this->name,
                'amount' => $this->amount,
            ]);

            $recurringExpense->update([
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
            ]);

            session()->flash('success', 'Recurring expense updated successfully.');
        } else {
            // Create New Expense
            $expense = Expense::create([
                'user_id' => auth()->id(),
                'name' => $this->name,
                'amount' => $this->amount,
                'category' => $this->category,
                'date' => now(),
                'is_recurring' => true,
            ]);

            RecurringExpense::create([
                'expense_id' => $expense->id,
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
            ]);

            session()->flash('success', 'Recurring expense added successfully.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $recurringExpense = RecurringExpense::findOrFail($id);

        $this->recurring_expense_id = $recurringExpense->id;
        $this->name = $recurringExpense->expense->name;
        $this->amount = $recurringExpense->expense->amount;
        $this->start_date = $recurringExpense->start_date->format('Y-m-d');
        $this->frequency = $recurringExpense->frequency;

        $this->showForm = true;
    }

    public function resetFields()
    {
        $this->reset(['name', 'amount', 'start_date', 'category', 'recurring_expense_id', 'showForm']);
    }

    public function render()
    {
        return view('livewire.core.recurring-expense-manager',
        ['frequencies' => ExpenseFrequency::cases()]);
    }
}
