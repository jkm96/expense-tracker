<?php

namespace App\Livewire\Core;

use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\DateHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UserNotifications extends Component
{
    use WithPagination;
    protected $queryString = [];
    public $unreadCount = 0;

    protected $listeners = ['notification-sent' => 'loadUnreadCount'];

    public function mount()
    {
        $this->loadUnreadCount();
    }

    #[On('notification-sent')]
    public function loadUnreadCount()
    {
        $this->unreadCount = Auth::user()->unreadNotifications()->count();
        $this->dispatch('$refresh');
    }

    public function goToPreviousPage()
    {
        $this->previousPage();
    }

    public function goToNextPage()
    {
        $this->nextPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            $this->loadUnreadCount();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadUnreadCount();
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Marked all notifications as read!', 'type' => 'success']);
    }

    public function deleteNotification($notificationId)
    {
        Auth::user()->notifications()->where('id', $notificationId)->delete();
        $this->loadUnreadCount();
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Deleted notification!', 'type' => 'success']);
    }

    public function deleteAllNotifications()
    {
        Auth::user()->notifications()->delete();
        $this->loadUnreadCount();
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Deleted all notifications!', 'type' => 'success']);
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(10)->through(function ($notification) {
            $notification->type = NotificationType::tryFrom($notification->data['type']) ?? NotificationType::REMINDER;
            $notification->formattedTimestamp = DateHelper::formatTimestamp($notification->created_at);

            return $notification;
        });

        return view('livewire.core.user-notifications', [
            'notifications' => $notifications,
        ]);
    }
}
