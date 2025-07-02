<?php

namespace App\Services\Notifications;

interface NotificationServiceInterface
{
    public function retrieveAll(int $userId, int $perPage);
    public function getUnreadCount(int $userId): int;
    public function markAsRead(int $userId, string $notificationId): void;
    public function markAllAsRead(int $userId): void;
    public function delete(int $userId, string $notificationId): void;
    public function deleteAll(int $userId): void;
}
