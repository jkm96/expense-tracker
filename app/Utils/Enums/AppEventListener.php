<?php

namespace App\Utils\Enums;

enum AppEventListener: string
{
    case NOTIFICATION_SENT = 'notification-sent';
    case GLOBAL_TOAST = 'global-toast';
    case EXPENSE_FORM = 'expense-form-updated';
    case RECURRING_FORM = 'recurring-form-updated';
}
