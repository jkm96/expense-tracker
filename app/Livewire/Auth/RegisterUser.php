<?php

namespace App\Livewire\Auth;

use App\Jobs\DispatchEmailNotificationsJob;
use App\Models\User;
use App\Models\UserVerification;
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

    public function registerUser()
    {
        $this->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => 1
        ]);

        //send email verification message
        $token = Str::random(100);
        $verificationUrl = route('email.verification', ['token' => $token]);
        UserVerification::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        $details = [
            'type' => AppEmailType::USER_VERIFICATION,
            'recipientEmail' => trim($this->email),
            'username' => trim($this->username),
            'verificationUrl' => trim($verificationUrl),
        ];

        DispatchEmailNotificationsJob::dispatch($details);

        $this->dispatch(AppEventListener::GLOBAL_TOAST, details: ['message' => 'Account created successfully!', 'type' => 'success']);
        $this->reset(['username','email','password','password_confirmation']);

        $this->isCreated = true;
    }
}
