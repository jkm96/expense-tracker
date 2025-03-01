<?php

namespace App\Utils\Enums;

enum NotificationType: string
{
    case REMINDER = 'reminder';
    case ANNOUNCEMENT = 'announcement';
    case ALERT = 'alert';

    /**
     * Get the corresponding badge color class.
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::REMINDER => 'text-blue-500',
            self::ANNOUNCEMENT => 'text-green-500',
            self::ALERT => 'text-red-500',
        };
    }
}
