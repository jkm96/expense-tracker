<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Services\Auth\AuthServiceInterface;
use App\Utils\Constants\AppEventListener;
use App\Utils\Enums\AuditAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoginUser extends Component
{
    public $identifier; // Username or email
    public $password;

    public function loginUser(AuthServiceInterface $authService)
    {
        $this->validate([
            'identifier' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($authService->login($this->identifier, $this->password)) {
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Welcome, ' . $this->identifier,
                'type' => 'success'
            ]);
            session()->flash('success', 'Welcome, ' . $this->identifier);
            return redirect()->intended(route('user.dashboard'));
        }

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
            'message' => 'Invalid credentials!',
            'type' => 'error'
        ]);

        $this->reset(['identifier', 'password']);
        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.auth.login-user');
    }
}
