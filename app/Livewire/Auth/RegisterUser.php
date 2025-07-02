<?php

namespace App\Livewire\Auth;

use App\Jobs\DispatchEmailNotificationsJob;
use App\Models\User;
use App\Models\UserVerification;
use App\Services\Auth\AuthServiceInterface;
use App\Utils\Constants\AppEmailType;
use App\Utils\Constants\AppEventListener;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Component;

class RegisterUser extends Component
{
    public $username;
    public $email;
    public $password;
    public $password_confirmation;
    public $isCreated = false;

    public function render()
    {
        return view('livewire.auth.register-user');
    }

    public function registerUser(AuthServiceInterface $authService)
    {
        $this->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $authService->registerUser([
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: [
            'message' => 'Account created successfully!',
            'type' => 'success'
        ]);

        $this->reset(['username', 'email', 'password', 'password_confirmation']);
        $this->isCreated = true;
    }
}
