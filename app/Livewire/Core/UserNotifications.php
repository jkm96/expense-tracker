<?php

namespace App\Livewire\Core;

use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\DateHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserNotifications extends Component
{
    public $notifications;
    public $unreadCount = 0;

    protected $listeners = ['notificationReceived' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        // Get the latest 5 notifications
        $this->notifications = $user->notifications()->latest()->take(15)->get()->map(function ($notification) {
            $notification->type = NotificationType::tryFrom($notification->data['type']) ?? NotificationType::REMINDER;
            $notification->formattedTimestamp = DateHelper::formatTimestamp($notification->created_at);

            return $notification;
        });

        // Count unread notifications
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications(); // Refresh the notifications
    }

    public function render()
    {
        return view('livewire.core.user-notifications');
    }
}
