<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Services\Auth\AuthServiceInterface;
use App\Utils\Enums\AuditAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LogoutUser extends Component
{
    public function logout(AuthServiceInterface $authService)
    {
        $authService->logoutUser();

        return redirect()->route('login.user')
            ->with('success', 'You have been logged out successfully.');
    }

    public function render()
    {
        return view('livewire.auth.logout-user');
    }
}
