<?php

namespace App\Livewire\Core;

use App\Models\Expense;
use App\Utils\Enums\ExpenseCategory;
use App\Utils\Helpers\ExpenseHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseManager extends Component
{
    use WithPagination;

    public $expenses = [];
    public $totals = [];
    public $page = 1;
    public $hasMorePages = true;

    public $name, $amount, $date, $category, $notes, $expense_id;
    public $showForm = false;
    public $categories = [];
    #[Url]
    public $filter = 'all';
    public $showDetailsModal = false;
    public $selectedExpense;
    public $showDeleteModal = false;
    public $expenseIdToDelete;
    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d');
        $this->filter = 'all';
        $this->page = 1;
        $this->categories = ExpenseCategory::cases();
        $this->loadExpenses();
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadExpenses();
    }

    #[On('toggleForm')]
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->dispatch('upsert-form-updated', details:  ['showForm'=>$this->showForm,'expenseId'=>null]);
    }

    #[On('closeModal')]
    public function closeModal()
    {
        $this->showForm = false;
        $this->dispatch('upsert-form-updated', details:  ['showForm'=>$this->showForm,'expenseId'=>null]);
        $this->resetFields();
    }

    public function updatedFilter()
    {
        $this->page = 1;
        $this->loadExpenses();
    }

    public function loadExpenses()
    {
        $query = Expense::where('user_id', Auth::id());

        if ($this->filter !== 'all') {
            $query->where('category', '=', ExpenseCategory::from($this->filter));
        }
        // Reset expenses on new filter to avoid persisting old records
        if ($this->page === 1) {
            $this->expenses = collect();
        }

        $paginatedExpenses = $query
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $this->page);

        // Group expenses by year-month
        $newExpenses = $paginatedExpenses->getCollection()->groupBy(function ($expense) {
            return Carbon::parse($expense->date)->format('Y - F');
        });

        $this->expenses = collect($this->expenses);

        foreach ($newExpenses as $month => $group) {
            $existingGroup = $this->expenses->get($month, collect());

            // Merge unique expenses by 'id'
            $mergedGroup = $existingGroup->concat($group)->unique('id');

            $this->expenses[$month] = $mergedGroup;
        }

        $this->totals = $this->expenses->map(fn($group) => $group->sum('amount'));

        $this->hasMorePages = $paginatedExpenses->hasMorePages();
    }

    public function render()
    {
        return view('livewire.core.expense-manager', [
            'expenses' => $this->expenses,
            'totals' => $this->totals,
            'hasMorePages' => $this->hasMorePages,
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
        $defaultNote = ExpenseHelper::generateDefaultNote($this->category, $this->name);

        if ($this->expense_id) {
            // Update existing expense
            $expense = Expense::where('id', $this->expense_id)->where('user_id', Auth::id())->first();
            if ($expense) {
                $expense->update([
                    'name' => Str::title($this->name),
                    'amount' => $this->amount,
                    'date' => $this->date,
                    'category' => ExpenseCategory::tryFrom($this->category)?->value,
                    'notes' => !empty($this->notes) ? $this->notes : $defaultNote,
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
                'notes' => !empty($this->notes) ? $this->notes : $defaultNote,
                'user_id' => Auth::id(),
            ]);

            session()->flash('success', 'Expense added successfully!');
        }

        $this->resetFields();
        $this->loadExpenses();
    }

    #[On('editExpense')]
    public function editExpense($expenseId)
    {
        $expense = Expense::where('id', $expenseId)->where('user_id', Auth::id())->first();

        if ($expense) {
            $this->expense_id = $expense->id;
            $this->name = $expense->name;
            $this->amount = $expense->amount;
            $this->date = Carbon::parse($expense->date)->format('Y-m-d');
            $this->category = $expense->category->value;
            $this->notes = $expense->notes;
            $this->showForm = !$this->showForm;
            $this->dispatch('upsert-form-updated', details:  ['showForm'=>$this->showForm,'expenseId'=>$expenseId]);
        }
    }

    public function showExpenseDetails($id)
    {
        $this->selectedExpense = Expense::findOrFail($id);
        $this->showDetailsModal = true;
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
        $this->loadExpenses();
    }

    public function resetFields()
    {
        $this->reset(['name', 'amount', 'date', 'category', 'notes', 'expense_id', 'showForm', 'filter']);
        $this->date = Carbon::now()->format('Y-m-d');
    }
}
