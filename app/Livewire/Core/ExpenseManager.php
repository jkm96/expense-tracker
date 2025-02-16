<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Utils\Enums\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseManager extends Component
{
    use WithPagination;

    public $name, $amount, $date, $category, $notes, $expense_id;
    public $showForm = false;
    public $categories = [];
    #[Url]
    public $filter = 'all';
    public $showDeleteModal = false;
    public $expenseIdToDelete;

    public function mount()
    {
        $this->categories = ExpenseCategory::cases(); // Store categories for dropdown
    }

    public function loadExpenses()
    {
        $this->render();
    }

    public function render()
    {
        $query = Expense::where('user_id', Auth::id());

        if ($this->filter !== 'all') {
            $query->where('category', $this->filter);
        }

        // Paginate first before grouping
        $paginatedExpenses = $query->orderBy('date', 'desc')->paginate(10);

        // Manually group expenses by Year - Month
        $expenses = $paginatedExpenses->getCollection()->groupBy(function ($expense) {
            return Carbon::parse($expense->date)->format('Y - F'); // Example: "2024 - January"
        });

        $totals = $expenses->map(fn($group) => $group->sum('amount'));

        return view('livewire.core.expense-manager', [
            'expenses' => $expenses,
            'totals' => $totals,
            'pagination' => $paginatedExpenses
        ]);
    }

    public function addExpense()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => ['required', Rule::in(ExpenseCategory::cases())],
            'notes' => 'nullable|string',
        ];

        $this->validate($rules);

        if ($this->expense_id) {
            // Update existing expense
            $expense = Expense::where('id', $this->expense_id)->where('user_id', Auth::id())->first();
            if ($expense) {
                $expense->update([
                    'name' => Str::title($this->name),
                    'amount' => $this->amount,
                    'date' => $this->date,
                    'category' => ExpenseCategory::tryFrom($this->category)?->value,
                    'notes' => !empty($this->notes) ? $this->notes : "Payment for ". Str::title($this->name),
                ]);

                session()->flash('success', 'Expense updated successfully!');
            }
        } else {
            // Create new expense
            Expense::create([
                'name' => Str::title($this->name),
                'amount' => $this->amount,
                'date' => $this->date,
                'category' => $this->category,
                'notes' => !empty($this->notes) ? $this->notes : "Payment for ". Str::title($this->name),
                'user_id' => Auth::id(),
            ]);

            session()->flash('success', 'Expense added successfully!');
        }

        $this->resetFields();
    }

    public function editExpense($id)
    {
        $expense = Expense::where('id', $id)->where('user_id', Auth::id())->first();

        if ($expense) {
            $this->expense_id = $expense->id;
            $this->name = $expense->name;
            $this->amount = $expense->amount;
            $this->date = Carbon::parse($expense->date)->format('Y-m-d');
            $this->category = $expense->category->value;
            $this->notes = $expense->notes;
            $this->showForm = true;
        }
    }

    public function showDeleteConfirmation($id)
    {
        $this->expenseIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        $expense = Expense::where('id', $this->expenseIdToDelete)->where('user_id', Auth::id())->first();
        if ($expense) {
            $expense->delete();
            session()->flash('success', 'Expense deleted successfully!');
        }

        $this->showDeleteModal = false;
        $this->expenseIdToDelete = null;

        $this->resetPage();
    }

    public function resetFields()
    {
        $this->reset(['name', 'amount', 'date', 'category', 'notes', 'expense_id', 'showForm','filter']);
    }
}
