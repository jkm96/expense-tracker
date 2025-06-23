<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService implements AuditLogServiceInterface
{
    public function getUserAuditLogs(array $params)
    {
        $search = $params["search"];
        $action = $params["action"];
        $perPage = $params["per_page"];
        $query = AuditLog::with('user')->where('user_id', Auth::id());

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('model_type', 'like', "%{$search}%")
                    ->orWhere('activity', 'like', "%{$search}%");
            });
        }

        if ($action && $action !== 'all') {
            $query->where('action', $action);
        }

        return $query->latest()->paginate($perPage);
    }
}
