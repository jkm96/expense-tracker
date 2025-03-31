<?php

namespace App\Livewire\Core;

use App\Models\AuditLog;
use App\Utils\Enums\AuditAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogManager extends Component
{
    use WithPagination;

    public $search = '';
    public $actionFilter = 'all';
    public $perPage = 10;
    public $auditActions = [];
    protected $queryString = ['search', 'actionFilter', 'perPage'];

    public $expandedLogs = [];
    public $filteredChanges = null;

    public function mount()
    {
        $this->auditActions = AuditAction::cases();
    }

    public function toggleLog($logId)
    {
        $this->expandedLogs[$logId] = !($this->expandedLogs[$logId] ?? false);

        if ($this->expandedLogs[$logId]) {
            $expandedLog = AuditLog::findOrFail($logId);
            $excludedFields = ['user_id', 'created_at', 'updated_at', 'last_processed_at',
                'next_process_at', 'schedule_config', 'recurring_expense_id'];

            $changes = is_array($expandedLog->changes) ? $expandedLog->changes : json_decode($expandedLog->changes, true);

            $this->filteredChanges[$logId] = Arr::except($changes ?? [], $excludedFields);
        } else {
            unset($this->filteredChanges[$logId]);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActionFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::with('user')->where('user_id', Auth::id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('model_type', 'like', "%{$this->search}%")
                    ->orWhere('changes', 'like', "%{$this->search}%");
            });
        }

        if ($this->actionFilter && $this->actionFilter !== 'all') {
            $query->where('action', $this->actionFilter);
        }

        $auditLogs = $query->latest()->paginate($this->perPage);

        return view('livewire.core.audit-log-manager', compact('auditLogs'));
    }
}
