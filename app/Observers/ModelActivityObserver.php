<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Utils\Enums\AuditAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ModelActivityObserver
{
    public function created(Model $model)
    {
        $this->logActivity($model, AuditAction::CREATED);
    }

    public function updated(Model $model)
    {
        $this->logActivity($model, AuditAction::UPDATED);
    }

    public function deleted(Model $model)
    {
        $this->logActivity($model, AuditAction::DELETED);
    }

    private function logActivity(Model $model, AuditAction $action)
    {
        $user = Auth::user();
        $userId = $user?->id ?? ($model->user_id ?? null);
        $username = ucfirst($user?->username ?? 'System');
        $tableName = Str::title(str_replace('_',' ',$model->getTable()));
        $changes = null;

        switch ($action) {
            case AuditAction::CREATED:
                $changes = $model->getAttributes();
                $activity = "{$username} created a new {$tableName} record ID: {$model->id}";
                break;

            case AuditAction::UPDATED:
                $changes = $model->getChanges();
                $activity = "{$username} updated {$tableName} record ID: {$model->id}";
                break;

            case AuditAction::DELETED:
                $changes = $model->getOriginal();
                $activity = "{$username} deleted {$tableName} record ID: {$model->id}";
                break;

            case AuditAction::COMMAND_EXECUTION:
                $activity = "Command executed affecting {$tableName}, record ID: {$model->id}";
                break;

            default:
                $activity = "{$username} performed {$action->value} on {$tableName} ID: {$model->id}";
                break;
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'activity' => $activity,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
