<?php

namespace App\Services\Notifications;

use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\DateHelper;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService implements NotificationServiceInterface
{
    public function retrieveAll(int $userId, int $perPage)
    {
        return DatabaseNotification::where('notifiable_id', $userId)
            ->latest()
            ->paginate($perPage)
            ->through(function ($notification) {
                $notification->type = NotificationType::tryFrom($notification->data['type'] ?? '') ?? NotificationType::REMINDER;
                $notification->formattedTimestamp = DateHelper::formatTimestamp($notification->created_at);
                return $notification;
            });
    }

    public function getUnreadCount(int $userId): int
    {
        return DatabaseNotification::where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead(int $userId, string $notificationId): void
    {
        $notification = DatabaseNotification::where('id', $notificationId)
            ->where('notifiable_id', $userId)
            ->first();

        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(int $userId): void
    {
        DatabaseNotification::where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function delete(int $userId, string $notificationId): void
    {
        DatabaseNotification::where('notifiable_id', $userId)
            ->where('id', $notificationId)
            ->delete();
    }

    public function deleteAll(int $userId): void
    {
        DatabaseNotification::where('notifiable_id', $userId)->delete();
    }
}
