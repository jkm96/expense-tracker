<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Utils\Enums\AuditAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LogoutUser extends Component
{
    public function logout()
    {
        $user = Auth::user();
        AuditLog::log(
            AuditAction::AUTH,
            $user->username,
            $user->getAuthIdentifier(),
            'User logged out successfully',
            'User',
            $user->getAuthIdentifier(),
            ['identifier' => $user->username, 'ip' => request()->ip()]
        );

        Auth::logout();
        Session::flush();

        return redirect()->route('login.user')->with('success', 'You have been logged out successfully.');
    }

    public function render()
    {
        return view('livewire.auth.logout-user');
    }
}
