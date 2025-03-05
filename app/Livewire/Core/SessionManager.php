<?php

namespace App\Livewire\Core;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SessionManager extends Component
{
    public $sessions = [];
    public $password;
    public $showLogoutModal = false;
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
                $session->deviceType = $this->getDeviceType($session->user_agent);
                $session->deviceName = $this->getDeviceName($session->user_agent);
                $session->browserName = $this->getBrowserName($session->user_agent);
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
            DB::table('sessions')->where('id', $this->sessionIdToLogout)->delete();
            $this->getActiveSessions();
            $this->sessionIdToLogout = null;
            session()->flash('success', 'Logged out successfully!');
        }
    }

    public function logoutOtherDevices()
    {
        if (!Auth::validate(['email' => auth()->user()->email, 'password' => $this->password])) {
            session()->flash('error', 'Incorrect password.');
            return;
        }

        Auth::logoutOtherDevices($this->password);
        $this->password = '';
        $this->getActiveSessions();
        session()->flash('success', 'Logged out from other devices.');
    }

    private function getDeviceType($userAgent)
    {
        if (stripos($userAgent, 'mobile') !== false) {
            return 'mobile';
        } elseif (stripos($userAgent, 'tablet') !== false) {
            return 'tablet';
        }
        return 'desktop';
    }

    private function getDeviceName($userAgent)
    {
        if (stripos($userAgent, 'android') !== false) return 'Android Device';
        if (stripos($userAgent, 'iphone') !== false) return 'iPhone';
        if (stripos($userAgent, 'ipad') !== false) return 'iPad';
        if (stripos($userAgent, 'windows') !== false) return 'Windows PC';
        if (stripos($userAgent, 'macintosh') !== false) return 'Mac';
        return 'Unknown Device';
    }

    private function getBrowserName($userAgent)
    {
        if (stripos($userAgent, 'chrome') !== false) return 'Chrome';
        if (stripos($userAgent, 'firefox') !== false) return 'Firefox';
        if (stripos($userAgent, 'safari') !== false && stripos($userAgent, 'chrome') === false) return 'Safari';
        if (stripos($userAgent, 'edge') !== false) return 'Edge';
        if (stripos($userAgent, 'msie') !== false || stripos($userAgent, 'trident') !== false) return 'Internet Explorer';
        return 'Unknown Browser';
    }

    public function render()
    {
        return view('livewire.core.session-manager');
    }
}
