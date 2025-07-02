<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\Auth\AuthServiceInterface;
use App\Utils\Constants\AppEventListener;
use App\Utils\Constants\MessageType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ResetPassword extends Component
{
    public $email;
    public $password;
    public $password_confirmation;
    public $token;

    public function mount($token)
    {
        $this->token = $token;
    }

    public function resetPassword(AuthServiceInterface $authService)
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required',
        ]);

        $success = $authService->resetPassword([
            'email' => $this->email,
            'password' => $this->password,
            'token' => $this->token,
        ]);

        if (!$success) {
            $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
                'message' => 'Invalid or expired token.',
                'type' => MessageType::ERROR,
            ]);
            return redirect()->back();
        }

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
            'message' => 'Password reset successfully.',
            'type' => MessageType::SUCCESS,
        ]);
        $this->dispatch('delayed-redirect', route('login.user'));
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
