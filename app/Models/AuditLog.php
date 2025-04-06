<?php

namespace App\Models;

use App\Utils\Enums\AuditAction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action','actor','activity', 'model_type', 'model_id', 'changes', 'ip_address'
    ];

    protected $casts = [
        'action' => AuditAction::class,
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedModelAttribute()
    {
        return class_basename($this->model_type);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y-m-d H:i:s A');
    }

    public function getReadableMessageAttribute()
    {
        $user = $this->user->username ?? 'Someone';
        $action = strtolower($this->action);
        $model = class_basename($this->model_type);
        $changes = $this->changes ? json_encode($this->changes, JSON_PRETTY_PRINT) : '';

        return "{$user} {$action} a {$model}. Changes: {$changes}";
    }

    public function getActionTraitsAttribute(): array
    {
        return match ($this->action) {
            AuditAction::AUTH => ['icon' => 'fas fa-right-to-bracket', 'color' => 'text-green-400', 'border' => 'border-green-400'],
            AuditAction::CREATED => ['icon' => 'fas fa-plus-circle', 'color' => 'text-green-700', 'border' => 'border-green-700'],
            AuditAction::UPDATED => ['icon' => 'fas fa-edit', 'color' => 'text-orange-500', 'border' => 'border-orange-500'],
            AuditAction::DELETED => ['icon' => 'fas fa-trash-alt', 'color' => 'text-red-500', 'border' => 'border-red-500'],
            AuditAction::COMMAND_EXECUTION => ['icon' => 'fa-solid fa-gear', 'color' => 'text-red-500', 'border' => 'border-red-500'],
            default => ['icon' => 'fas fa-question-circle', 'color' => 'text-gray-500', 'border' => 'border-gray-400'],
        };
    }

    /**
     * Log an audit entry manually
     */
    public static function log(
        AuditAction|string $action,
        ?string $actor = null,
        ?int $userId = null,
        ?string $activity = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $changes = null
    ): void {
        self::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action instanceof AuditAction ? $action->value : $action,
            'actor'=> $actor,
            'activity' => $activity ?? ucfirst(strtolower($action)),
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => $changes,
            'ip_address' => Request::ip(),
        ]);
    }
}
