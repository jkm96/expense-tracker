<?php

namespace App\Services\Audit;

interface AuditLogServiceInterface
{
    public function getUserAuditLogs(array $params);
}
