<?php

namespace App\Notifications;

use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExpenseReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public NotificationType $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($message, NotificationType $type = NotificationType::REMINDER)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type->value,
            'timestamp' => Carbon::now()->setTimezone(config('app.timezone')),
        ];
    }
}
