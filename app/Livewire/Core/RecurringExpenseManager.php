<?php

namespace App\Livewire\Core;

use App\Models\RecurringExpense;
use App\Notifications\ExpenseReminderNotification;
use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\ExpenseCategory;
use App\Utils\Enums\ExpenseFrequency;
use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\ExpenseHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
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
    public array $days = [];
    public ?string $dayOfWeek = null;
    public $dayOfMonth = null;
    public $showDeleteModal = false;
    public $showDetailsModal = false;
    public $selectedExpense;
    public $showToggleModal = false;
    public $selectedExpenseId;

    public $daysOfWeek = [
        'sun' => 'Sunday',
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thur' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
    ];

    public function mount()
    {
        $this->start_date = Carbon::now()->format('Y-m-d\TH:i');
        $this->categories = ExpenseCategory::cases();
        $this->frequencies = ExpenseFrequency::cases();
        $this->loadRecurringExpenses();
    }

    #[On('toggle-form')]
    public function toggleForm()
    {
        if (!isset($this->start_date)){
            $this->start_date = Carbon::now()->format('Y-m-d\TH:i');
        }
        $this->showForm = !$this->showForm;
        $this->dispatch(AppEventListener::RECURRING_FORM, details: [
            'showForm' => $this->showForm,
            'recurringExpenseId' => null
        ]);
    }

    public function closeModal($action)
    {
        switch ($action) {
            case 'form-modal':
                $this->showForm = false;
                $this->dispatch(AppEventListener::RECURRING_FORM, details: [
                    'showForm' => $this->showForm,
                    'recurringExpenseId' => null
                ]);
                break;
            case 'view-modal':
                $this->showDetailsModal = false;
                $this->dispatch(AppEventListener::VIEW_RECURRING_MODAL, details: [
                    'showModal' => $this->showDetailsModal,
                    'recurringExpenseId' => null
                ]);
                break;
        }

        $this->resetFields();
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
        $query = RecurringExpense::where('user_id', auth()->id());

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
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
            'days' => $this->frequency === ExpenseFrequency::DAILY->value ? 'required|array|min:1' : 'nullable',
            'dayOfWeek' => $this->frequency === ExpenseFrequency::WEEKLY->value ? 'required|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday' : 'nullable',
            'dayOfMonth' => $this->frequency === ExpenseFrequency::MONTHLY->value ? 'required|date' : 'nullable',
        ];

        $this->validate($rules);

        $fullDays = collect($this->days)->map(function ($shortDay) {
            return $this->mapShortToFullDay($shortDay);
        })->toArray();

        $scheduleConfig = json_encode([
            'days' => $this->frequency === ExpenseFrequency::DAILY->value ? $fullDays : null,
            'day_of_week' => $this->frequency === ExpenseFrequency::WEEKLY->value ? $this->dayOfWeek : null,
            'day_of_month' => $this->frequency === ExpenseFrequency::MONTHLY->value ? $this->dayOfMonth : null,
        ]);

        $nextProcessAt = ExpenseHelper::calculateNextProcessTime(
            ExpenseFrequency::from($this->frequency),
            Carbon::parse($this->start_date)
        );

        if ($this->recurring_expense_id) {
            // Update Existing Recurring Expense
            $recurringExpense = RecurringExpense::findOrFail($this->recurring_expense_id);

            $recurringExpense->update([
                'name' => Str::title($this->name),
                'amount' => $this->amount,
                'category' => $this->category,
                'notes' => ExpenseHelper::generateDefaultNote($this->category, $this->name),
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
                'schedule_config' => $scheduleConfig,
                'next_process_at' => $nextProcessAt,
            ]);

            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Recurring expense updated successfully!',
                'type' => 'success'
            ]);
        } else {
            $recurringExpense = RecurringExpense::create([
                'user_id' => auth()->id(),
                'name' => Str::title($this->name),
                'amount' => $this->amount,
                'category' => $this->category,
                'notes' => ExpenseHelper::generateDefaultNote($this->category, $this->name),
                'start_date' => $this->start_date,
                'frequency' => $this->frequency,
                'schedule_config' => $scheduleConfig,
                'next_process_at' => $nextProcessAt,
                'is_active' => true,
            ]);

            $message = "A new recurring expense {$recurringExpense->name} added on {$recurringExpense->created_at->format('Y-m-d h:i A')}";
            Auth::user()->notify(new ExpenseReminderNotification($message, NotificationType::INFO));
            $this->dispatch(AppEventListener::NOTIFICATION_SENT);

            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Recurring expense added successfully!',
                'type' => 'success'
            ]);
        }

        $this->showForm = false;
        $this->dispatch(AppEventListener::RECURRING_FORM, details: [
            'showForm' => $this->showForm,
            'recurringExpenseId' => null
        ]);
        $this->resetFields();
        $this->loadRecurringExpenses();
    }

    private function mapShortToFullDay($shortDay): string
    {
        return $this->daysOfWeek[$shortDay] ?? ucfirst($shortDay);
    }

    private function mapFullToShortDay($fullDay): string
    {
        return array_search($fullDay, $this->daysOfWeek) ?? strtolower(substr($fullDay, 0, 3));
    }

    #[On('edit-recurring-expense')]
    public function editRecurringExpense($recurringExpenseId)
    {
        $recurringExpense = RecurringExpense::findOrFail($recurringExpenseId);

        $this->recurring_expense_id = $recurringExpense->id;
        $this->name = $recurringExpense->name;
        $this->category = $recurringExpense->category->value;
        $this->amount = $recurringExpense->amount;
        $this->start_date = Carbon::parse($recurringExpense->start_date)->toDateTimeString();
        $this->frequency = $recurringExpense->frequency->value;

        $scheduleConfig = json_decode($recurringExpense->schedule_config, true);
        switch ($this->frequency) {
            case ExpenseFrequency::DAILY->value:
                $this->days = collect($scheduleConfig['days'])->map(function ($day) {
                    return $this->mapFullToShortDay($day);
                })->toArray();
                break;

            case ExpenseFrequency::WEEKLY->value:
                $this->dayOfWeek = strtolower($scheduleConfig['day_of_week'] ?? '');
                break;

            case ExpenseFrequency::MONTHLY->value:
                $this->dayOfMonth = $scheduleConfig['day_of_month'] ?? '';
                break;

            default:
                $this->days = [];
                $this->dayOfWeek = null;
                $this->dayOfMonth = null;
                break;
        }

        $this->showForm = !$this->showForm;
        $this->dispatch(AppEventListener::RECURRING_FORM, details: [
            'showForm' => $this->showForm,
            'recurringExpenseId' => $recurringExpenseId
        ]);
    }

    public function showToggleConfirmation($id)
    {
        $this->selectedExpenseId = $id;
        $this->selectedExpense = RecurringExpense::with('generatedExpenses')->findOrFail($id);
        $this->showToggleModal = true;
    }

    public function toggleRecurringExpense()
    {
        $expense = RecurringExpense::findOrFail($this->selectedExpenseId);
        $expense->update(['is_active' => !$expense->is_active]);

        $status = $expense->is_active ? 'resumed' : 'stopped';
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
            'message' => "Recurring expense has been {$status}.",
            'type' => 'success'
        ]);

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
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Recurring expense deleted successfully!',
                'type' => 'success'
            ]);
        }

        $this->showDeleteModal = false;
        $this->selectedExpenseId = null;

        $this->resetFields();
        $this->loadRecurringExpenses();
    }

    #[On('show-recurring-details')]
    public function showRecurringExpenseDetails($recurringExpenseId = null)
    {
        if (!empty($recurringExpenseId)) {
            $this->selectedExpense = RecurringExpense::with('generatedExpenses')->findOrFail($recurringExpenseId);
            $this->showDetailsModal = true;
            $this->dispatch(AppEventListener::VIEW_RECURRING_MODAL, details: [
                'showModal' => $this->showDetailsModal,
                'recurringExpenseId' => $recurringExpenseId
            ]);
        }
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
