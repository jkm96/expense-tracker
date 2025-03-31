<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\AuditAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginUser extends Component
{
    public $identifier; // Username or email
    public $password;

    public function loginUser()
    {
        $this->validate([
            'identifier' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // Attempt login with email or username
        $credentials = filter_var($this->identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $this->identifier, 'password' => $this->password, 'is_email_verified' => 1, 'is_active' => 1]
            : ['username' => $this->identifier, 'password' => $this->password, 'is_email_verified' => 1, 'is_active' => 1];

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Welcome, ' . $this->identifier,
                'type' => 'success']
            );

            AuditLog::log(
                AuditAction::AUTH,
                'User logged in successfully',
                'User',
                Auth::id(),
                ['identifier' => $this->identifier, 'ip' => request()->ip()]
            );

            return redirect()->intended(route('user.dashboard'));
        }

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Invalid credentials!', 'type' => 'error']);
        $this->reset(['identifier', 'password']);

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.auth.login-user');
    }
}
