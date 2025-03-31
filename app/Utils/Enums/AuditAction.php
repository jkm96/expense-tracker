<?php

namespace App\Utils\Enums;

enum AuditAction: string
{
    case CREATED = 'Created';
    case UPDATED = 'Updated';
    case DELETED = 'Deleted';
    case AUTH = 'Auth';
    case COMMAND_EXECUTION = 'Command Execution';
}
