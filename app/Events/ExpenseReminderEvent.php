<?php

namespace App\Events;

use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpenseReminderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $message;
    public NotificationType $type;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $message, NotificationType $type = NotificationType::REMINDER)
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->userId)];
    }

    public function broadcastWith(): array
    {
        return [
            'userId' => $this->userId,
            'message' => $this->message,
            'type' => $this->type->value,
            'timestamp' => Carbon::now()->setTimezone(config('app.timezone')),
        ];
    }

    /**
     * Custom event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'expense.reminder';
    }
}
