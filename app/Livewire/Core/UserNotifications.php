<?php

namespace App\Livewire\Core;

use App\Services\Notifications\NotificationServiceInterface;
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

    public function mount(NotificationServiceInterface $notificationService)
    {
        $this->loadUnreadCount($notificationService);
    }

    #[On('notification-sent')]
    public function loadUnreadCount(NotificationServiceInterface $notificationService)
    {
        $this->unreadCount = $notificationService->getUnreadCount(Auth::id());
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

    public function markAsRead(NotificationServiceInterface $notificationService, $notificationId)
    {
        $notificationService->markAsRead(Auth::id(), $notificationId);
        $this->loadUnreadCount($notificationService);
    }

    public function markAllAsRead(NotificationServiceInterface $notificationService)
    {
        $notificationService->markAllAsRead(Auth::id());
        $this->loadUnreadCount($notificationService);
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Marked all notifications as read!', 'type' => 'success']);
    }

    public function deleteNotification(NotificationServiceInterface $notificationService, $notificationId)
    {
        $notificationService->delete(Auth::id(), $notificationId);
        $this->loadUnreadCount($notificationService);
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Deleted notification!', 'type' => 'success']);
    }

    public function deleteAllNotifications(NotificationServiceInterface $notificationService)
    {
        $notificationService->deleteAll(Auth::id());
        $this->loadUnreadCount($notificationService);
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Deleted all notifications!', 'type' => 'success']);
    }

    public function render(NotificationServiceInterface $notificationService)
    {
        $notifications = $notificationService->retrieveAll(Auth::id(), 10);

        return view('livewire.core.user-notifications', [
            'notifications' => $notifications,
        ]);
    }
}
