<?php

namespace App\Livewire\Core;

use App\Notifications\ExpenseReminderNotification;
use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\NotificationType;
use App\Utils\Helpers\SessionHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SessionManager extends Component
{
    public $sessions = [];
    public $password;
    public $showLogoutModal = false;
    public $showLogoutOtherDevicesModal = false;
    public $sessionIdToLogout;

    public function mount()
    {
        $this->getActiveSessions();
    }

    public function getActiveSessions()
    {
        $this->sessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) {
                $session->deviceType = SessionHelper::getDeviceType($session->user_agent);
                $session->deviceName = SessionHelper::getDeviceName($session->user_agent);
                $session->browserName = SessionHelper::getBrowserName($session->user_agent);
                return $session;
            });
    }

    public function confirmLogout($sessionId)
    {
        $this->sessionIdToLogout = $sessionId;
        $this->showLogoutModal = true;
    }

    public function logoutSession()
    {
        if ($this->sessionIdToLogout) {
            $session = DB::table('sessions')->where('id', $this->sessionIdToLogout)->first();

            if ($session) {
                $deviceName = SessionHelper::getDeviceName($session->user_agent);
                $browser = SessionHelper::getBrowserName($session->user_agent);
                $ip = $session->ip_address;
                $lastActivity = Carbon::parse($session->last_activity)->format('Y-m-d h:i A');

                $message = "A device has been logged out: {$deviceName} using {$browser} (IP: {$ip}, Last Active: {$lastActivity}).";

                DB::table('sessions')->where('id', $this->sessionIdToLogout)->delete();

                Auth::user()->notify(new ExpenseReminderNotification($message, NotificationType::ALERT));
                $this->dispatch(AppEventListener::NOTIFICATION_SENT);

                $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Device logged out successfully!', 'type' => 'success']);
            }

            $this->getActiveSessions();
            $this->sessionIdToLogout = null;
            $this->showLogoutModal = false;
        }
    }

    public function confirmLogoutOtherDevices()
    {
        if (!$this->password) {
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Please enter your password!', 'type' => 'error']);
            return;
        }
        $this->showLogoutOtherDevicesModal = true;
    }

    public function logoutOtherDevices()
    {
        if (!Auth::validate(['email' => auth()->user()->email, 'password' => $this->password])) {
            $this->showLogoutOtherDevicesModal = false;
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Incorrect password!', 'type' => 'error']);
            return;
        }

        $currentSessionId = session()->getId();
        $otherSessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $currentSessionId)
            ->get();

        if ($otherSessions->isEmpty()) {
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'No other active sessions found!', 'type' => 'info']);
            return;
        }

        DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', $currentSessionId)
            ->delete();

        // Build a detailed message with device info
        $sessionDetails = $otherSessions->map(function ($session) {
            $deviceName = SessionHelper::getDeviceName($session->user_agent);
            $browser = SessionHelper::getBrowserName($session->user_agent);
            $ip = $session->ip_address;
            $lastActivity = Carbon::parse($session->last_activity)->format('Y-m-d h:i A');

            return "{$deviceName} using {$browser} (IP: {$ip}, Last Active: {$lastActivity})";
        })->implode("\n");

        $message = "You have logged out from the following devices:\n" . $sessionDetails;

        Auth::user()->notify(new ExpenseReminderNotification($message, NotificationType::ALERT));
        $this->dispatch(AppEventListener::NOTIFICATION_SENT);

        $this->password = '';
        $this->showLogoutOtherDevicesModal = false;
        $this->getActiveSessions();
        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Other devices logged out successfully!', 'type' => 'info']);
    }

    public function render()
    {
        return view('livewire.core.session-manager');
    }
}
