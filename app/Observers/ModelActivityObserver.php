<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Utils\Enums\AuditAction;
use App\Utils\Enums\ExpenseCategory;
use BackedEnum;
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

        $original = collect($model->getOriginal())->map(function ($value) {
            return $value instanceof BackedEnum ? $value->value : $value;
        });

        switch ($action) {
            case AuditAction::CREATED:
                $changes = $model->getAttributes();
                $activity = "A new record was added to {$tableName} with ID {$model->id}.";
                break;

            case AuditAction::UPDATED:
                $updated = collect($model->getAttributes())->map(function ($value) {
                    return $value instanceof BackedEnum ? $value->value : $value;
                });
                $changes = $updated->diffAssoc($original);

                if ($changes->isNotEmpty()) {
                    $activity = "The record with ID {$model->id} in {$tableName} was updated.";
                } else {
                    $activity = "The record with ID {$model->id} in {$tableName} was updated, but no changes were detected.";
                }
                break;

            case AuditAction::DELETED:
                $changes = $model->getOriginal();
                $activity = "The record with ID {$model->id} was deleted from {$tableName}.";
                break;

            case AuditAction::COMMAND_EXECUTION:
                $activity = "A system command was executed that affected {$tableName}, impacting the record with ID {$model->id}.";
                break;

            default:
                $activity = "An action ({$action->value}) was performed on {$tableName} for the record with ID {$model->id}.";
                break;
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'actor' => $username,
            'activity' => $activity,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
