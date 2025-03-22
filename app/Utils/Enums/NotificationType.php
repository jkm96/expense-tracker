<?php

namespace App\Utils\Enums;

enum NotificationType: string
{
    case REMINDER = 'reminder';
    case ANNOUNCEMENT = 'announcement';
    case ALERT = 'alert';
    case SYSTEM = 'system';
    case INFO = 'info';

    /**
     * Get the corresponding badge color class.
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::REMINDER => 'text-blue-500',
            self::ANNOUNCEMENT => 'text-yellow-500',
            self::ALERT => 'text-red-500',
            self::SYSTEM => 'text-orange-600',
            self::INFO => 'text-green-500',
        };
    }
}
