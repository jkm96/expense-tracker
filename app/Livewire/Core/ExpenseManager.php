<?php

namespace App\Livewire\Core;

use App\Exports\ExpensesExport;
use App\Models\Expense;
use App\Notifications\ExpenseReminderNotification;
use App\Services\Expense\ExpenseServiceInterface;
use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\ExpenseHelper;
use App\Utils\Validators\ExpenseValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

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
    public $filter = 'all';
    public $search;
    protected $queryString = ['search', 'filter', 'page'];
    public $showDetailsModal = false;
    public $selectedExpense;
    public $showDeleteModal = false;
    public $startDate, $endDate;
    public $exportFields = ['category' => null];
    public $showExportModal = false;
    public $expenseIdToDelete;
    public $totalExpenses;

    public function __construct()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d');
        $this->page = 1;
        $this->filter = 'all';
        $this->categories = ExpenseCategory::cases();
        $this->loadExpenses();
    }

    public function updatedSearch()
    {
        $this->loadExpenses();
    }

    public function updatedFilter()
    {
        $this->page = 1;
        $this->loadExpenses();
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadExpenses();
    }

    #[On('toggle-form')]
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->dispatch(AppEventListener::EXPENSE_FORM, details: [
            'showForm' => $this->showForm,
            'expenseId' => null
        ]);
    }

    public function closeModal($action)
    {
        switch ($action) {
            case 'form-modal':
                $this->showForm = false;
                $this->dispatch(AppEventListener::EXPENSE_FORM, details: [
                    'showForm' => $this->showForm,
                    'expenseId' => null
                ]);
                break;
            case 'view-modal':
                $this->showDetailsModal = false;
                $this->dispatch(AppEventListener::VIEW_EXPENSE_MODAL, details: [
                    'showModal' => $this->showDetailsModal,
                    'expenseId' => null
                ]);
                break;
        }

        $this->resetFields();
    }

    public function loadExpenses(ExpenseServiceInterface $expenseService)
    {
        $paginatedExpenses = $expenseService->fetchByUser([
            "filter" => $this->filter,
            "search" => $this->search,
            "current_page" => $this->page,
            "per_page" => 10
        ]);

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

        $this->totalExpenses = $paginatedExpenses->total();
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

    public function addExpense(ExpenseServiceInterface $expenseService)
    {
        $this->validate(ExpenseValidator::expenseRules(), ExpenseValidator::expenseMessages());

        $defaultNote = ExpenseHelper::generateDefaultNote($this->category, $this->name);

        $expense = $expenseService->addOrUpdate([
            'name' => Str::title($this->name),
            'amount' => $this->amount,
            'date' => $this->date,
            'category' => $this->category,
            'notes' => !empty($this->notes) ? $this->notes : $defaultNote,
        ], $this->expense_id);

        // Determine if it's an update or create
        if ($this->expense_id) {
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Expense updated successfully!',
                'type' => 'success'
            ]);
        } else {
            $message = "A new expense {$expense->name} added on {$expense->created_at->format('Y-m-d h:i A')}";
            Auth::user()->notify(new ExpenseReminderNotification($message, NotificationType::INFO));
            $this->dispatch(AppEventListener::NOTIFICATION_SENT);

            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Expense added successfully!',
                'type' => 'success'
            ]);
        }

        $this->showForm = false;
        $this->dispatch(AppEventListener::EXPENSE_FORM, details: ['showForm' => $this->showForm, 'expenseId' => null]);
        $this->resetFields();
        $this->loadExpenses($expenseService);
    }

    #[On('edit-expense')]
    public function editExpense(ExpenseServiceInterface $expenseService, $expenseId)
    {
        if (!empty($expenseId)) {
            $expense = $expenseService->find($expenseId);

            if ($expense) {
                $this->expense_id = $expense->id;
                $this->name = $expense->name;
                $this->amount = $expense->amount;
                $this->date = Carbon::parse($expense->date)->format('Y-m-d');
                $this->category = $expense->category->value;
                $this->notes = $expense->notes;

                $this->showForm = !$this->showForm;
                $this->dispatch(AppEventListener::EXPENSE_FORM, details: [
                    'showForm' => $this->showForm,
                    'expenseId' => $expenseId
                ]);
            }
        }
    }

    #[On('show-expense-details')]
    public function showExpenseDetails($expenseId = null)
    {
        if (!empty($expenseId)) {
            $this->selectedExpense = Expense::findOrFail($expenseId);
            $this->showDetailsModal = true;
            $this->dispatch(AppEventListener::VIEW_EXPENSE_MODAL, details: [
                'showModal' => $this->showDetailsModal,
                'expenseId' => $expenseId
            ]);
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
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Expense deleted successfully!',
                'type' => 'success'
            ]);
        }

        $this->showDeleteModal = false;
        $this->expenseIdToDelete = null;

        $this->resetPage();
        $this->loadExpenses();
    }

    public function toggleExportModal()
    {
        $this->showExportModal = !$this->showExportModal;
    }

    public function exportExpenses()
    {
        $this->validate(ExpenseValidator::exportRules(), ExpenseValidator::exportMessages());
        $fileName = 'expenses_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
            'message' => 'Expense exported successfully!',
            'type' => 'success'
        ]);

//        $this->reset('exportFields');

        return Excel::download(new ExpensesExport(
            $this->startDate,
            $this->endDate,
            $this->exportFields['category']
        ), $fileName);
    }

    public function resetFields()
    {
        $this->reset(['name', 'amount', 'date', 'category', 'notes', 'expense_id', 'showForm', 'filter']);
        $this->date = Carbon::now()->format('Y-m-d');
    }
}
